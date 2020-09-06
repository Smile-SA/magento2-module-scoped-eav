<?php
/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future.
 *
 * @category  Smile
 * @package   Smile\ScopedEav
 * @author    Aurelien FOUCRET <aurelien.foucret@smile.fr>
 * @copyright 2017 Smile
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Smile\ScopedEav\Controller\Adminhtml\Attribute;

use Magento\Framework\Exception\LocalizedException;
use Zend\Validator\Regex;
use Zend\Validator\RegexFactory;

/**
 * Scoped EAV entity attribute save controller.
 *
 * @category Smile
 * @package  Smile\ScopedEav
 * @author   Aurelien FOUCRET <aurelien.foucret@smile.fr>
 */
class Save extends \Smile\ScopedEav\Controller\Adminhtml\AbstractAttribute
{
    /**
     * @var \Smile\ScopedEav\Helper\Data
     */
    private $entityHelper;

    /**
     * @var RegexFactory
     */
    private $regexFactory;

    /**
     * Constructor.
     *
     * @param \Magento\Backend\App\Action\Context $context          Context.
     * @param \Smile\ScopedEav\Helper\Data        $entityHelper     Entity helper.
     * @param BuilderInterface                    $attributeBuilder Attribute builder.
     * @param RegexFactory                        $regexFactory     Regexp validator factory.
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Smile\ScopedEav\Helper\Data $entityHelper,
        BuilderInterface $attributeBuilder,
        RegexFactory $regexFactory
    ) {
        parent::__construct($context, $entityHelper, $attributeBuilder);
        $this->entityHelper = $entityHelper;
        $this->regexFactory = $regexFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        try {
            $attribute = $this->getAttribute();
            $attribute = $this->addPostData($attribute);

            $attribute->save();

            $response = $this->_redirect("*/*/index");

            if ($this->getRequest()->getParam('back', false)) {
                $response = $this->_redirect("*/*/edit", ['attribute_id' => $attribute->getId(), '_current' => true]);
            }
        } catch (\Exception $e) {
            $response = $this->getRedirectError($e->getMessage());
        }

        return $response;
    }

    /**
     * Add request post data to the attribute.
     *
     * @param \Smile\ScopedEav\Api\Data\AttributeInterface $attribute Attribute.
     *
     * @return \Smile\ScopedEav\Api\Data\AttributeInterface
     */
    private function addPostData(\Smile\ScopedEav\Api\Data\AttributeInterface $attribute)
    {
        $data = array_filter($this->getRequest()->getParams());

        $frontendInput = isset($data['frontend_input']) ? $data['frontend_input'] : $attribute->getFrontendInput();

        if (!$attribute->getId()) {
            $data['attribute_code']  = $this->getAttributeCode();
            $data['is_user_defined'] = true;
            $data['backend_type']    = ($data['frontend_input'] == 'image') ? 'varchar' : $attribute->getBackendTypeByInput($data['frontend_input']);
            $data['source_model']    = $this->entityHelper->getAttributeSourceModelByInputType($data['frontend_input']);
            $data['backend_model']   = $this->entityHelper->getAttributeBackendModelByInputType($data['frontend_input']);
        }

        $defaultValueField = $attribute->getDefaultValueByInput($frontendInput);
        if ($defaultValueField) {
            $data['default_value'] = $this->getRequest()->getParam($defaultValueField);
        }

        $attribute->addData($data);

        return $attribute;
    }

    /**
     * Generate attribute code from request params.
     *
     * @throws \Exception
     *
     * @return string
     */
    private function getAttributeCode()
    {
        $attributeCode = $this->getRequest()->getParam('attribute_code');

        if (!$attributeCode) {
            $attributeCode = $this->generateCode($this->getRequest()->getParam('frontend_label')[0]);
        }

        if (trim($attributeCode) == '') {
            /** @var Regex $validatorAttrCode */
            $validatorAttrCode = $this->regexFactory->create(['pattern' => '/^[a-z][a-z_0-9]{0,30}$/']);

            if (!$validatorAttrCode->isValid($attributeCode)) {
                $errorMessageParts = [
                    __('Attribute code "%1" is invalid.', $attributeCode),
                    __('Please use only letters (a-z), numbers (0-9) or underscore(_) in this field.'),
                    __('First character should be a letter.'),
                ];
                throw new LocalizedException(__(implode(' ', $errorMessageParts), $attributeCode));
            }
        }

        return $attributeCode;
    }
}

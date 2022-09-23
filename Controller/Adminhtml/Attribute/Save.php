<?php

declare(strict_types=1);

namespace Smile\ScopedEav\Controller\Adminhtml\Attribute;

use Magento\Backend\App\Action\Context;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Serialize\Serializer\FormData;
use Smile\ScopedEav\Api\Data\AttributeInterface;
use Smile\ScopedEav\Controller\Adminhtml\AbstractAttribute;
use Smile\ScopedEav\ViewModel\Data as DataViewModel;
use Zend\Validator\Regex;
use Zend\Validator\RegexFactory;

/**
 * Scoped EAV entity attribute save controller.
 */
class Save extends AbstractAttribute
{
    private DataViewModel $dataViewModel;

    private RegexFactory $regexFactory;

    private ?FormData $formDataSerializer = null;

    /**
     * Constructor.
     *
     * @param Context $context Context.
     * @param DataViewModel $dataViewModel Scoped EAV data view model.
     * @param BuilderInterface $attributeBuilder Attribute builder.
     * @param RegexFactory $regexFactory Regexp validator factory.
     */
    public function __construct(
        Context $context,
        DataViewModel $dataViewModel,
        BuilderInterface $attributeBuilder,
        RegexFactory $regexFactory,
        ?FormData $formDataSerializer = null
    ) {
        parent::__construct($context, $dataViewModel, $attributeBuilder);
        $this->dataViewModel = $dataViewModel;
        $this->regexFactory = $regexFactory;
        $this->formDataSerializer = $formDataSerializer
            ?: ObjectManager::getInstance()->get(FormData::class);
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        try {
            $attribute = $this->getAttribute();
            $attribute = $this->addPostData($attribute);

            $attribute->save();

            $response = $this->resultRedirectFactory->create();

            if ($this->getRequest()->getParam('back', false)) {
                $response->setPath(
                    "*/*/edit",
                    ['attribute_id' => $attribute->getId(), '_current' => true]
                );
            } else {
                $response->setPath('*/*/index');
            }
        } catch (\Exception $e) {
            $response = $this->getRedirectError($e->getMessage());
        }

        return $response;
    }

    /**
     * Add request post data to the attribute.
     *
     * @param AttributeInterface $attribute Attribute.
     */
    private function addPostData(AttributeInterface $attribute): AttributeInterface
    {
        try {
            $optionData = $this->formDataSerializer
                ->unserialize($this->getRequest()->getParam('serialized_options', '[]'));
        } catch (\InvalidArgumentException $e) {
            $message = __("The attribute couldn't be saved due to an error. Verify your information and try again. "
                . "If the error persists, please try again later.");
            $this->messageManager->addErrorMessage($message);
            return $this->returnResult('catalog/*/edit', ['_current' => true], ['error' => true]);
        }

        $data = $this->getRequest()->getPostValue();
        $data = array_replace_recursive(
            $data,
            $optionData
        );

        $frontendInput = $data['frontend_input'] ?? $attribute->getFrontendInput();

        if (!$attribute->getId()) {
            $data['attribute_code']  = $this->getAttributeCode();
            $data['is_user_defined'] = true;
            $data['backend_type'] = $data['frontend_input'] == 'image' ?
                'varchar' : $attribute->getBackendTypeByInput($data['frontend_input']);
            $data['source_model'] = $this->dataViewModel->getAttributeSourceModelByInputType($data['frontend_input']);
            $data['backend_model'] = $this->dataViewModel->getAttributeBackendModelByInputType($data['frontend_input']);
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
     */
    private function getAttributeCode(): string
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

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

use Magento\Framework\DataObject;
use Magento\Framework\DataObjectFactory;
use Magento\Framework\Exception\AlreadyExistsException;

/**
 * Scoped EAV entity attribute validation controller.
 *
 * @category Smile
 * @package  Smile\ScopedEav
 * @author   Aurelien FOUCRET <aurelien.foucret@smile.fr>
 */
class Validate extends \Smile\ScopedEav\Controller\Adminhtml\AbstractAttribute
{
    /**
     * @var string
     */
    const DEFAULT_MESSAGE_KEY = 'message';

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    private $resultJsonFactory;

    /**
     * @var DataObjectFactory
     */
    private $dataObjectFactory;

    /**
     * Constructor.
     *
     * @param \Magento\Backend\App\Action\Context              $context           Context.
     * @param \Smile\ScopedEav\Helper\Data                     $entityHelper      Entity helper.
     * @param BuilderInterface                                 $attributeBuilder  Attribute builder.
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory JSON response factory.
     * @param DataObjectFactory                                $dataObjectFactory Data object factory.
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Smile\ScopedEav\Helper\Data $entityHelper,
        BuilderInterface $attributeBuilder,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        DataObjectFactory $dataObjectFactory
    ) {
        parent::__construct($context, $entityHelper, $attributeBuilder);
        $this->resultJsonFactory = $resultJsonFactory;
        $this->dataObjectFactory = $dataObjectFactory;
    }

    /**
     * {@inheritDoc}
     */
    public function execute()
    {
        $response = $this->dataObjectFactory->create();
        $response->setError(false);

        $response = $this->checkAttributeCode($response);

        return $this->resultJsonFactory->create()->setJsonData($response->toJson());
    }

    /**
     * Set message to response object.
     *
     * @param DataObject $response Original response object.
     * @param string[]   $messages Messages.
     *
     * @return DataObject
     */
    private function setMessageToResponse($response, $messages)
    {
        $messageKey = $this->getRequest()->getParam('message_key', static::DEFAULT_MESSAGE_KEY);
        if ($messageKey === static::DEFAULT_MESSAGE_KEY) {
            $messages = reset($messages);
        }

        return $response->setData($messageKey, $messages);
    }

    /**
     * Check an attribute with the same code does not exists yet.
     *
     * @param DataObject $response Response.
     *
     * @return DataObject
     */
    private function checkAttributeCode($response)
    {
        $attributeCode = $this->getRequest()->getParam('attribute_code');
        $frontendLabel = $this->getRequest()->getParam('frontend_label');
        $attributeCode = $attributeCode ?: $this->generateCode($frontendLabel[0]);

        $params = $this->getRequest()->getParams();
        if (!isset($params['attribute_code']) || empty($params['attribute_code'])) {
            $params['attribute_code'] = $attributeCode;
            $this->getRequest()->setParams($params);
        }

        try {
            $this->getAttribute();
        } catch (AlreadyExistsException $e) {
            $message = __('An attribute with this code already exists.');
            $this->setMessageToResponse($response, [$message]);
            $response->setError(true);
        }

        return $response;
    }
}

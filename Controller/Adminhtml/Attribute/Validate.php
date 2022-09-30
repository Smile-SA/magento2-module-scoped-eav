<?php

declare(strict_types=1);

namespace Smile\ScopedEav\Controller\Adminhtml\Attribute;

use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\Result\ForwardFactory;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\DataObject;
use Magento\Framework\DataObjectFactory;
use Magento\Framework\Exception\AlreadyExistsException;
use Smile\ScopedEav\Controller\Adminhtml\AbstractAttribute;
use Smile\ScopedEav\ViewModel\Data as DataViewModel;

/**
 * Scoped EAV entity attribute validation controller.
 */
class Validate extends AbstractAttribute implements HttpPostActionInterface
{
    private const DEFAULT_MESSAGE_KEY = 'message';

    private JsonFactory $resultJsonFactory;

    private DataObjectFactory $dataObjectFactory;

    /**
     * Constructor.
     *
     * @param Context $context Context.
     * @param DataViewModel $dataViewModel Scoped EAV data view model.
     * @param BuilderInterface $attributeBuilder Attribute builder.
     * @param JsonFactory $resultJsonFactory JSON response factory.
     * @param DataObjectFactory $dataObjectFactory Data object factory.
     * @param ForwardFactory $resultForwardFactory Forward factory.
     */
    public function __construct(
        Context $context,
        DataViewModel $dataViewModel,
        BuilderInterface $attributeBuilder,
        JsonFactory $resultJsonFactory,
        DataObjectFactory $dataObjectFactory,
        ForwardFactory $resultForwardFactory
    ) {
        parent::__construct($context, $dataViewModel, $attributeBuilder, $resultForwardFactory);
        $this->resultJsonFactory = $resultJsonFactory;
        $this->dataObjectFactory = $dataObjectFactory;
    }

    /**
     * @inheritDoc
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
     * @param string[] $messages Messages.
     */
    private function setMessageToResponse(DataObject $response, array $messages): DataObject
    {
        $messageKey = $this->getRequest()->getParam('message_key', self::DEFAULT_MESSAGE_KEY);
        if ($messageKey === self::DEFAULT_MESSAGE_KEY) {
            $messages = reset($messages);
        }

        return $response->setData($messageKey, $messages);
    }

    /**
     * Check an attribute with the same code does not exists yet.
     *
     * @param DataObject $response Response.
     */
    private function checkAttributeCode(DataObject $response): DataObject
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
            $message = (string) __('An attribute with this code already exists.');
            $this->setMessageToResponse($response, [$message]);
            $response->setError(true);
        }

        return $response;
    }
}

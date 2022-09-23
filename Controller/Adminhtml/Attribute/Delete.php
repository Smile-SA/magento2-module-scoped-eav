<?php

declare(strict_types=1);

namespace Smile\ScopedEav\Controller\Adminhtml\Attribute;

use Magento\Framework\Exception\NoSuchEntityException;
use Smile\ScopedEav\Controller\Adminhtml\AbstractAttribute;

/**
 * Scoped EAV entity attribute deletion controller.
 */
class Delete extends AbstractAttribute
{
    /**
     * @inheritDoc
     */
    public function execute()
    {
        try {
            $attribute = $this->getAttribute();

            if (!$attribute || !$attribute->getId()) {
                throw new NoSuchEntityException(__('Attribute does not exists'));
            }

            $attribute->delete();

            $this->messageManager->addSuccessMessage(__('Attribute has been deleted'));
            $response = $this->resultRedirectFactory->create();
            $response->setPath('*/*/index');
        } catch (\Exception $e) {
            $response = $this->getRedirectError($e->getMessage());
        }

        return $response;
    }
}

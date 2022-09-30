<?php

declare(strict_types=1);

namespace Smile\ScopedEav\Controller\Adminhtml\Attribute;

use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Model\AbstractModel;
use Smile\ScopedEav\Controller\Adminhtml\AbstractAttribute;

/**
 * Scoped EAV entity attribute deletion controller.
 */
class Delete extends AbstractAttribute implements HttpPostActionInterface
{
    /**
     * @inheritDoc
     */
    public function execute()
    {
        try {
            /** @var AbstractModel $attribute */
            $attribute = $this->getAttribute();

            if (!$attribute->getId()) {
                throw new NoSuchEntityException(__('Attribute does not exists'));
            }

            /** @var AbstractModel $attribute */
            $attribute->delete();

            $this->messageManager->addSuccessMessage((string) __('Attribute has been deleted'));
            $response = $this->resultRedirectFactory->create();
            $response->setPath('*/*/index');
        } catch (\Exception $e) {
            $response = $this->getRedirectError($e->getMessage());
        }

        return $response;
    }
}

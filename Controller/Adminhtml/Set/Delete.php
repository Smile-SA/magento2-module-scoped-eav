<?php

declare(strict_types=1);

namespace Smile\ScopedEav\Controller\Adminhtml\Set;

use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Model\AbstractModel;
use Smile\ScopedEav\Controller\Adminhtml\AbstractSet;

/**
 * Scoped EAV entity attribute set admin delete controller.
 */
class Delete extends AbstractSet implements HttpGetActionInterface
{
    /**
     * @inheritDoc
     */
    public function execute()
    {
        try {
            /** @var AbstractModel $attributeSet */
            $attributeSet = $this->getAttributeSet();
            $attributeSet->delete();
            $this->messageManager->addSuccessMessage((string) __('The attribute set has been removed.'));
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage((string) __('We can\'t delete this set right now.'));
        }

        $resultRedirect = $this->resultRedirectFactory->create();
        return $resultRedirect->setPath('*/*/index');
    }
}

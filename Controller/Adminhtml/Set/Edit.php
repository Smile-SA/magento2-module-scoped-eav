<?php

declare(strict_types=1);

namespace Smile\ScopedEav\Controller\Adminhtml\Set;

use Magento\Framework\App\Action\HttpGetActionInterface;
use Smile\ScopedEav\Controller\Adminhtml\AbstractSet;

/**
 * Scoped EAV entity attribute set admin edit controller.
 */
class Edit extends AbstractSet implements HttpGetActionInterface
{
    /**
     * @inheritDoc
     */
    public function execute()
    {
        $this->setTypeId();
        try {
            $attributeSet = $this->getAttributeSet();
            $result = $this->createActionPage(
                $attributeSet->getId() ? $attributeSet->getAttributeSetName() : (string) __('New Set')
            );
        } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
            $this->messageManager->addErrorMessage((string) __('No such attribute set.'));
            $result = $this->resultRedirectFactory->create()->setPath('*/*/index');
        }

        return $result;
    }
}

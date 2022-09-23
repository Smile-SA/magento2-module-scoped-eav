<?php

declare(strict_types=1);

namespace Smile\ScopedEav\Controller\Adminhtml\Set;

use Smile\ScopedEav\Controller\Adminhtml\AbstractSet;

/**
 * Scoped EAV entity attribute set admin edit controller.
 */
class Edit extends AbstractSet
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
                $attributeSet->getId() ? $attributeSet->getAttributeSetName() : __('New Set')
            );
        } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
            $this->messageManager->addErrorMessage(__('No such attribute set.'));
            $result = $this->resultRedirectFactory->create()->setPath('*/*/index');
        }

        return $result;
    }
}

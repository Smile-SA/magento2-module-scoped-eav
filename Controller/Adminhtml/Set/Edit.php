<?php

declare(strict_types=1);

namespace Smile\ScopedEav\Controller\Adminhtml\Set;

/**
 * Scoped EAV entity attribute set admin edit controller.
 */
class Edit extends \Smile\ScopedEav\Controller\Adminhtml\AbstractSet
{
    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $this->setTypeId();
        try {
            $attributeSet = $this->getAttributeSet();
            $result = $this->createActionPage($attributeSet->getId() ? $attributeSet->getAttributeSetName() : __('New Set'));
        } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
            $this->messageManager->addErrorMessage(__('No such attribute set.'));
            $result = $this->resultRedirectFactory->create()->setPath('*/*/index');
        }

        return $result;
    }
}

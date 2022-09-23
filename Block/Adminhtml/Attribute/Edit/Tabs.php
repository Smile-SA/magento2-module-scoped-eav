<?php

declare(strict_types=1);

namespace Smile\ScopedEav\Block\Adminhtml\Attribute\Edit;

/**
 * Scoped EAV entity attribute edit form tabs management.
 */
class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    /**
     * @inheritDoc
     * @SuppressWarnings(PHPMD.CamelCaseMethodName)
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('entity_attribute_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Attribute Information'));
    }

    /**
     * @inheritDoc
     * @SuppressWarnings(PHPMD.CamelCaseMethodName)
     */
    protected function _beforeToHtml()
    {
        $this->addTab('main', [
            'label' => __('Properties'),
            'title' => __('Properties'),
            'content' => $this->getChildHtml('main'),
            'active' => true,
        ]);
        $this->addTab('labels', [
            'label' => __('Manage Labels'),
            'title' => __('Manage Labels'),
            'content' => $this->getChildHtml('labels'),
        ]);

        return parent::_beforeToHtml();
    }
}

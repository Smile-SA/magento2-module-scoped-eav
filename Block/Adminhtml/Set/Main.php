<?php

declare(strict_types=1);

namespace Smile\ScopedEav\Block\Adminhtml\Set;

use Magento\Backend\Block\Template;
use Magento\Backend\Block\Widget\Button;
use Magento\Catalog\Block\Adminhtml\Product\Attribute\Set\Main\Tree\Group;
use Smile\ScopedEav\Block\Adminhtml\Set\Main\Formset;

/**
 * Scoped EAV entity attribute set main form container.
 *
 * @SuppressWarnings(PHPMD.CamelCasePropertyName)
 */
class Main extends \Magento\Catalog\Block\Adminhtml\Product\Attribute\Set\Main
{
    /**
     * @inheritDoc
     */
    protected $_template = 'Magento_Catalog::catalog/product/attribute/set/main.phtml';

    /**
     * Returns attribute set save URL.
     */
    public function getMoveUrl(): string
    {
        return $this->getUrl('*/*/save', ['id' => $this->_getSetId()]);
    }

    /**
     * Returns attribute set delete URL.
     */
    public function getDeleteUrl(): string
    {
        return $this->getUrl('*/*/delete', ['id' => $this->_getSetId()]);
    }

    /**
     * @inheritDoc
     * @SuppressWarnings(PHPMD.CamelCaseMethodName)
     */
    protected function _prepareLayout(): self
    {
        $this->addChild('group_tree', Group::class);
        $this->addChild('edit_set_form', Formset::class);

        $this->addChild(
            'delete_group_button',
            Button::class,
            [
                'label' => __('Delete Selected Group'),
                'onclick' => 'editSet.submit();',
                'class' => 'delete',
            ]
        );

        $this->addChild(
            'add_group_button',
            Button::class,
            [
                'label' => __('Add New'),
                'onclick' => 'editSet.addGroup();',
                'class' => 'add',
            ]
        );

        /** @var Template $toolbar */
        $toolbar = $this->getToolbar();
        $toolbar->addChild(
            'back_button',
            Button::class,
            [
                'label' => __('Back'),
                'onclick' => 'setLocation(\'' . $this->getUrl('*/*/index') . '\')',
                'class' => 'back',
            ]
        );

        $toolbar->addChild(
            'reset_button',
            Button::class,
            [
                'label' => __('Reset'),
                'onclick' => 'window.location.reload()',
                'class' => 'reset',
            ]
        );

        if (!$this->getIsCurrentSetDefault()) {
            $deleteMessage =
                (string) __(
                    'You are about to delete all products in this attribute set. Are you sure you want to do that ?'
                );
            $toolbar->addChild(
                'delete_button',
                Button::class,
                [
                    'label'   => __('Delete'),
                    'onclick' => 'deleteConfirm(\''
                        . $this->escapeJsQuote($deleteMessage) . '\', \'' . $this->getDeleteUrl() . '\')',
                    'class' => 'delete',
                ]
            );
        }

        $toolbar->addChild(
            'save_button',
            Button::class,
            [
                'label' => __('Save'),
                'onclick' => 'editSet.save();',
                'class' => 'save primary save-attribute-set',
            ]
        );

        $this->addChild(
            'rename_button',
            Button::class,
            [
                'label' => __('New Set Name'),
                'onclick' => 'editSet.rename()',
            ]
        );

        return $this;
    }
}

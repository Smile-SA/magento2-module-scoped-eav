<?php

declare(strict_types=1);

namespace Smile\ScopedEav\Block\Adminhtml\Set;

/**
 * Scoped EAV entity attribute set main form container.
 *
 * @SuppressWarnings(PHPMD.CamelCasePropertyName)
 */
class Main extends \Magento\Catalog\Block\Adminhtml\Product\Attribute\Set\Main
{
    /**
     * @var string
     */
    protected $_template = 'Magento_Catalog::catalog/product/attribute/set/main.phtml';

    /**
     * Returns attribute set save URL.
     *
     * @return string
     */
    public function getMoveUrl(): string
    {
        return $this->getUrl('*/*/save', ['id' => $this->_getSetId()]);
    }

    /**
     * Returns attribute set delete URL.
     *
     * @return string
     */
    public function getDeleteUrl(): string
    {
        return $this->getUrl('*/*/delete', ['id' => $this->_getSetId()]);
    }

    /**
     * {@inheritDoc}
     *
     * @SuppressWarnings(PHPMD.CamelCaseMethodName)
     */
    protected function _prepareLayout(): self
    {
        $this->addChild('group_tree', 'Magento\Catalog\Block\Adminhtml\Product\Attribute\Set\Main\Tree\Group');
        $this->addChild('edit_set_form', 'Smile\ScopedEav\Block\Adminhtml\Set\Main\Formset');

        $this->addChild(
            'delete_group_button',
            'Magento\Backend\Block\Widget\Button',
            [
                'label' => __('Delete Selected Group'),
                'onclick' => 'editSet.submit();',
                'class' => 'delete',
            ]
        );

        $this->addChild(
            'add_group_button',
            'Magento\Backend\Block\Widget\Button',
            [
                'label' => __('Add New'),
                'onclick' => 'editSet.addGroup();',
                'class' => 'add',
            ]
        );

        $this->getToolbar()->addChild(
            'back_button',
            'Magento\Backend\Block\Widget\Button',
            [
                'label' => __('Back'),
                'onclick' => 'setLocation(\'' . $this->getUrl('*/*/index') . '\')',
                'class' => 'back',
            ]
        );

        $this->getToolbar()->addChild(
            'reset_button',
            'Magento\Backend\Block\Widget\Button',
            [
                'label' => __('Reset'),
                'onclick' => 'window.location.reload()',
                'class' => 'reset',
            ]
        );

        if (! $this->getIsCurrentSetDefault()) {
            $deleteMessage = __('You are about to delete all products in this attribute set. ' . 'Are you sure you want to do that?');
            $this->getToolbar()->addChild(
                'delete_button',
                'Magento\Backend\Block\Widget\Button',
                [
                    'label'   => __('Delete'),
                    'onclick' => 'deleteConfirm(\'' . $this->escapeJsQuote($deleteMessage) . '\', \'' . $this->getDeleteUrl() . '\')',
                    'class' => 'delete',
                ]
            );
        }

        $this->getToolbar()->addChild(
            'save_button',
            'Magento\Backend\Block\Widget\Button',
            [
                'label' => __('Save'),
                'onclick' => 'editSet.save();',
                'class' => 'save primary save-attribute-set',
            ]
        );

        $this->addChild(
            'rename_button',
            'Magento\Backend\Block\Widget\Button',
            [
                'label' => __('New Set Name'),
                'onclick' => 'editSet.rename()',
            ]
        );

        return $this;
    }
}

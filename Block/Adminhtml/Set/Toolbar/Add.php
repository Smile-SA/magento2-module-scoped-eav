<?php

declare(strict_types=1);

namespace Smile\ScopedEav\Block\Adminhtml\Set\Toolbar;

/**
 * Attribute set add main container.
 */
class Add extends \Magento\Catalog\Block\Adminhtml\Product\Attribute\Set\Toolbar\Add
{
    /**
     * {@inheritdoc}
     *
     * @SuppressWarnings(PHPMD.CamelCaseMethodName)
     */
    protected function _prepareLayout()
    {
        if ($this->getToolbar()) {
            $this->getToolbar()->addChild(
                'save_button',
                'Magento\Backend\Block\Widget\Button',
                [
                    'label' => __('Save'),
                    'class' => 'save primary save-attribute-set',
                    'data_attribute' => ['mage-init' => ['button' => ['event' => 'save', 'target' => '#set-prop-form']]],
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
        }

        $this->addChild('setForm', 'Smile\ScopedEav\Block\Adminhtml\Set\Main\Formset');

        return $this;
    }
}

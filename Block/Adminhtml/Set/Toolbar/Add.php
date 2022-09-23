<?php

declare(strict_types=1);

namespace Smile\ScopedEav\Block\Adminhtml\Set\Toolbar;

use Magento\Backend\Block\Widget\Button;
use Smile\ScopedEav\Block\Adminhtml\Set\Main\Formset;

/**
 * Attribute set add main container.
 */
class Add extends \Magento\Catalog\Block\Adminhtml\Product\Attribute\Set\Toolbar\Add
{
    /**
     * @inheritDoc
     * @SuppressWarnings(PHPMD.CamelCaseMethodName)
     */
    protected function _prepareLayout(): self
    {
        if ($this->getToolbar()) {
            $this->getToolbar()->addChild(
                'save_button',
                Button::class,
                [
                    'label' => __('Save'),
                    'class' => 'save primary save-attribute-set',
                    'data_attribute' =>
                        ['mage-init' => ['button' => ['event' => 'save', 'target' => '#set-prop-form']]],
                ]
            );

            $this->getToolbar()->addChild(
                'back_button',
                Button::class,
                [
                    'label' => __('Back'),
                    'onclick' => 'setLocation(\'' . $this->getUrl('*/*/index') . '\')',
                    'class' => 'back',
                ]
            );
        }

        $this->addChild('setForm', Formset::class);

        return $this;
    }
}

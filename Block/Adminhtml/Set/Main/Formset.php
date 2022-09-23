<?php

declare(strict_types=1);

namespace Smile\ScopedEav\Block\Adminhtml\Set\Main;

/**
 * Attribute set formset.
 */
class Formset extends \Magento\Catalog\Block\Adminhtml\Product\Attribute\Set\Main\Formset
{
    /**
     * @inheritDoc
     * @SuppressWarnings(PHPMD.CamelCaseMethodName)
     */
    protected function _prepareForm()
    {
        parent::_prepareForm();

        $this->getForm()->setAction($this->getUrl('*/*/save'));
    }
}

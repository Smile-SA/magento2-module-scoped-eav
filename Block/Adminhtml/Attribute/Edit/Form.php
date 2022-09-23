<?php

declare(strict_types=1);

namespace Smile\ScopedEav\Block\Adminhtml\Attribute\Edit;

use Magento\Backend\Block\Widget\Form\Generic;

/**
 * Entity attribute edit form.
 */
class Form extends Generic
{
    /**
     * @inheritDoc
     * @SuppressWarnings(PHPMD.CamelCaseMethodName)
     */
    protected function _prepareForm()
    {
        $form = $this->_formFactory->create(
            ['data' => ['id' => 'edit_form', 'action' => $this->getData('action'), 'method' => 'post']]
        );

        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }
}

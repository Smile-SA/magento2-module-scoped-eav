<?php

declare(strict_types=1);

namespace Smile\ScopedEav\Controller\Adminhtml\Attribute;

/**
 * Scoped EAV entity attribute create controller.
 */
class NewAction extends \Smile\ScopedEav\Controller\Adminhtml\AbstractAttribute
{
    /**
     * {@inheritDoc}
     */
    public function execute()
    {
        return $this->_forward("edit");
    }
}

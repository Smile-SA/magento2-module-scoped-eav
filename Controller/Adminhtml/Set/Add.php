<?php

declare(strict_types=1);

namespace Smile\ScopedEav\Controller\Adminhtml\Set;

/**
 * Scoped EAV entity attribute set admin add controller.
 */
class Add extends \Smile\ScopedEav\Controller\Adminhtml\AbstractSet
{
    /**
     * {@inheritDoc}
     */
    public function execute()
    {
        $this->setTypeId();

        return $this->createActionPage(__('New Attribute Set'));
    }
}

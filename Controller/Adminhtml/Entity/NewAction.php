<?php

declare(strict_types=1);

namespace Smile\ScopedEav\Controller\Adminhtml\Entity;

use Smile\ScopedEav\Controller\Adminhtml\AbstractEntity;

/**
 * Scoped EAV entity creation controller.
 */
class NewAction extends AbstractEntity
{
    /**
     * {@inheritDoc}
     */
    public function execute()
    {
        if (!$this->getRequest()->getParam('set')) {
            return $this->_forward('noroute');
        }

        $this->getEntity();

        return $this->createActionPage(__('New entity'));
    }
}

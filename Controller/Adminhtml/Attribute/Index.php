<?php

declare(strict_types=1);

namespace Smile\ScopedEav\Controller\Adminhtml\Attribute;

use Smile\ScopedEav\Controller\Adminhtml\AbstractAttribute;

/**
 * Scoped EAV attribute listing controller.
 */
class Index extends AbstractAttribute
{
    /**
     * @inheritDoc
     */
    public function execute()
    {
        $resultPage = $this->createActionPage(__('Manage Attributes'));

        return $resultPage;
    }
}

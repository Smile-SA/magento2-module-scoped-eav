<?php

declare(strict_types=1);

namespace Smile\ScopedEav\Controller\Adminhtml\Attribute;

/**
 * Scoped EAV attribute listing controller.
 */
class Index extends \Smile\ScopedEav\Controller\Adminhtml\AbstractAttribute
{
    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $resultPage = $this->createActionPage(__('Manage Attributes'));

        return $resultPage;
    }
}

<?php

declare(strict_types=1);

namespace Smile\ScopedEav\Controller\Adminhtml\Set;

/**
 * Scoped EAV entity attribute set admin list controller.
 */
class Index extends \Smile\ScopedEav\Controller\Adminhtml\AbstractSet
{
    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $this->setTypeId();

        return $this->createActionPage(__('Attribute Sets'));
    }
}

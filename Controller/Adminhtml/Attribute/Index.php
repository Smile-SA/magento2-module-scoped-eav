<?php

declare(strict_types=1);

namespace Smile\ScopedEav\Controller\Adminhtml\Attribute;

use Magento\Framework\App\Action\HttpGetActionInterface;
use Smile\ScopedEav\Controller\Adminhtml\AbstractAttribute;

/**
 * Scoped EAV attribute listing controller.
 */
class Index extends AbstractAttribute implements HttpGetActionInterface
{
    /**
     * @inheritDoc
     */
    public function execute()
    {
        return $this->createActionPage(__('Manage Attributes'));
    }
}

<?php

declare(strict_types=1);

namespace Smile\ScopedEav\Controller\Adminhtml\Attribute;

use Magento\Framework\App\Action\HttpGetActionInterface;
use Smile\ScopedEav\Controller\Adminhtml\AbstractAttribute;

/**
 * Scoped EAV entity attribute create controller.
 */
class NewAction extends AbstractAttribute implements HttpGetActionInterface
{
    /**
     * @inheritDoc
     */
    public function execute()
    {
        return $this->_forward("edit");
    }
}

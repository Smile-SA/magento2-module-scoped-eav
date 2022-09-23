<?php

declare(strict_types=1);

namespace Smile\ScopedEav\Controller\Adminhtml\Entity;

use Magento\Framework\App\Action\HttpGetActionInterface;
use Smile\ScopedEav\Controller\Adminhtml\AbstractEntity;

/**
 * Scoped EAV entity creation controller.
 */
class NewAction extends AbstractEntity implements HttpGetActionInterface
{
    /**
     * @inheritDoc
     */
    public function execute()
    {
        if (!$this->getRequest()->getParam('set')) {
            $resultForward = $this->resultForwardFactory->create();
            return $resultForward->forward('noroute');
        }

        $this->getEntity();

        return $this->createActionPage(__('New entity'));
    }
}

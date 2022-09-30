<?php

declare(strict_types=1);

namespace Smile\ScopedEav\Controller\Adminhtml\Entity;

use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Smile\ScopedEav\Controller\Adminhtml\AbstractEntity;

/**
 * Scoped EAV entity edit controller.
 */
class Edit extends AbstractEntity implements HttpGetActionInterface
{
    /**
     * @inheritDoc
     */
    public function execute()
    {
        try {
            $entityId = (int) $this->getRequest()->getParam('id', 0);

            if ($entityId === 0) {
                throw new LocalizedException(__('Invalid entity id. Should be numeric value greater than 0'));
            }

            $entity = $this->getEntity();

            $response = $this->createActionPage($entity->getName());
            $switchBlock = $response->getLayout()->getBlock('store_switcher');
            if (!$this->storeManager->isSingleStoreMode() && $switchBlock) {
                $switchUrl = $this->getUrl(
                    '*/*/*',
                    ['_current' => true, 'active_tab' => null, 'tab' => null, 'store' => null]
                );
                // @phpstan-ignore-next-line
                $switchBlock->setDefaultStoreName(__('Default Values'))->setSwitchUrl($switchUrl);
            }
        } catch (NoSuchEntityException $e) {
            $this->messageManager->addErrorMessage('This entity doesn\'t exist.');
            $response = $this->resultRedirectFactory->create();
            $response->setPath('*/*/index');
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            $response = $this->resultRedirectFactory->create();
            $response->setPath('*/*/index');
        }

        return $response;
    }
}

<?php

declare(strict_types=1);

namespace Smile\ScopedEav\Controller\Adminhtml\Entity;

use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Smile\ScopedEav\Controller\Adminhtml\AbstractEntity;

/**
 * Scoped EAV entity delete controller.
 */
class Delete extends AbstractEntity implements HttpPostActionInterface
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

            $this->getEntity()->delete();
            $this->messageManager->addSuccessMessage(
                (string) __('Entity have been deleted successfuly.')
            );
        } catch (NoSuchEntityException $e) {
            $this->messageManager->addErrorMessage('This entity doesn\'t exist.');
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage(
                $e,
                (string) __('Can not delete entity.')
            );
        }

        $resultRedirect = $this->resultRedirectFactory->create();
        return $resultRedirect->setPath('*/*/');
    }
}

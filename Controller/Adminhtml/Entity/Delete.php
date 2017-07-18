<?php
/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future.
 *
 * @category  Smile
 * @package   Smile\ScopedEav
 * @author    Aurelien FOUCRET <aurelien.foucret@smile.fr>
 * @copyright 2017 Smile
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Smile\ScopedEav\Controller\Adminhtml\Entity;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\LocalizedException;

/**
 * Scoped EAV entity delete controller.
 *
 * @category Smile
 * @package  Smile\ScopedEav
 * @author   Aurelien FOUCRET <aurelien.foucret@smile.fr>
 */
class Delete extends \Smile\ScopedEav\Controller\Adminhtml\AbstractEntity
{
    /**
     * {@inheritDoc}
     */
    public function execute()
    {
        try {
            $entityId = (int) $this->getRequest()->getParam('id', 0);

            if ($entityId === 0) {
                throw new LocalizedException(__('Invalid entity id. Should be numeric value greater than 0'));
            }

            $this->getEntity()->delete();
            $this->messageManager->addSuccessMessage(__('Entity have been deleted successfuly.'));
        } catch (NoSuchEntityException $e) {
            $this->messageManager->addErrorMessage('This entity doesn\'t exist.');
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage($e, __('Can not delete entity.'));
        }

        return $this->_redirect("*/*/");
    }
}

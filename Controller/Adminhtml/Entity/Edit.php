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

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Scoped EAV entity edit controller.
 *
 * @category Smile
 * @package  Smile\ScopedEav
 * @author   Aurelien FOUCRET <aurelien.foucret@smile.fr>
 */
class Edit extends \Smile\ScopedEav\Controller\Adminhtml\AbstractEntity
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

            $entity = $this->getEntity();

            $response = $this->createActionPage($entity->getName());

            if (!$this->storeManager->isSingleStoreMode() && ($switchBlock = $response->getLayout()->getBlock('store_switcher'))) {
                $switchUrl = $this->getUrl('*/*/*', ['_current' => true, 'active_tab' => null, 'tab' => null, 'store' => null]);
                $switchBlock->setDefaultStoreName(__('Default Values'))->setSwitchUrl($switchUrl);
            }
        } catch (NoSuchEntityException $e) {
            $this->messageManager->addErrorMessage('This entity doesn\'t exist.');
            $response = $this->_redirect('*/*/index');
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            $response = $this->_redirect('*/*/index');
        }

        return $response;
    }
}

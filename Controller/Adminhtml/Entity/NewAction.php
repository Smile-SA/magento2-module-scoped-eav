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

/**
 * Scoped EAV entity creation controller.
 *
 * @category Smile
 * @package  Smile\ScopedEav
 * @author   Aurelien FOUCRET <aurelien.foucret@smile.fr>
 */
class NewAction extends \Smile\ScopedEav\Controller\Adminhtml\AbstractEntity
{
    /**
     * {@inheritDoc}
     */
    public function execute()
    {
        if (!$this->getRequest()->getParam('set')) {
            return $this->_forward('noroute');
        }

        $this->getEntity();

        return $this->createActionPage(__('New entity'));
    }
}

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

namespace Smile\ScopedEav\Controller\Adminhtml\Attribute;

/**
 * Scoped EAV entity attribute create controller.
 *
 * @category Smile
 * @package  Smile\ScopedEav
 * @author   Aurelien FOUCRET <aurelien.foucret@smile.fr>
 */
class NewAction extends \Smile\ScopedEav\Controller\Adminhtml\AbstractAttribute
{
    /**
     * {@inheritDoc}
     */
    public function execute()
    {
        return $this->_forward("edit");
    }
}

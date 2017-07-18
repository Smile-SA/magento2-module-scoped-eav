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

namespace Smile\ScopedEav\Model\Locator;

/**
 * Adminhtml entity locator.
 *
 * @category Smile
 * @package  Smile\ScopedEav
 * @author   Aurelien FOUCRET <aurelien.foucret@smile.fr>
 */
interface LocatorInterface
{
    /**
     * Returns current entity.
     *
     * @return \Smile\ScopedEav\Api\Data\EntityInterface
     */
    public function getEntity();

    /**
     *
     * @return \Magento\Store\Api\Data\StoreInterface
     */
    public function getStore();
}

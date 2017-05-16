<?php
/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Smile Elastic Suite to newer
 * versions in the future.
 *
 *
 * @category  Smile
 * @package   Smile\ScopedEav
 * @author    Aurelien FOUCRET <aurelien.foucret@smile.fr>
 * @copyright 2016 Smile
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Smile\ScopedEav\Model\Entity;

use Smile\ScopedEav\Api\Data\AttributeInterface;

/**
 * Scoped EAV attribute implementation.
 *
 * @category Smile
 * @package  Smile\ScopedEav
 * @author   Aurelien FOUCRET <aurelien.foucret@smile.fr>
 */
class Attribute extends \Magento\Eav\Model\Attribute implements AttributeInterface
{
    /**
     * {@inheritdoc}
     */
    public function getScope()
    {
        $scope    = self::SCOPE_STORE_TEXT;
        $isGlobal = $this->getIsGlobal();

        if ($isGlobal == self::SCOPE_GLOBAL) {
            $scope = self::SCOPE_GLOBAL_TEXT;
        } elseif ($isGlobal == self::SCOPE_WEBSITE) {
            $scope = self::SCOPE_WEBSITE_TEXT;
        }

        return $scope;
    }

    /**
     * {@inheritdoc}
     */
    public function getIsGlobal()
    {
        return $this->_getData(self::KEY_IS_GLOBAL);
    }

    /**
     * {@inheritdoc}
     */
    public function setScope($scope)
    {
        $isGlobal = self::SCOPE_STORE;

        if ($scope == self::SCOPE_GLOBAL_TEXT) {
            $isGlobal = $this->setData(self::KEY_IS_GLOBAL, self::SCOPE_GLOBAL);
        } elseif ($scope == self::SCOPE_WEBSITE_TEXT) {
            $isGlobal = $this->setData(self::KEY_IS_GLOBAL, self::SCOPE_WEBSITE);
        }

        return $this->setIsGlobal($isGlobal);
    }

    /**
     * {@inheritdoc}
     */
    public function setIsGlobal($isGlobal)
    {
        return $this->setData(self::KEY_IS_GLOBAL, $isGlobal);
    }

    /**
     * {@inheritdoc}
     */
    public function isScopeGlobal()
    {
        return $this->getIsGlobal() == self::SCOPE_GLOBAL;
    }

    /**
     * {@inheritdoc}
     */
    public function isScopeWebsite()
    {
        return $this->getIsGlobal() == self::SCOPE_WEBSITE;
    }

    /**
     * {@inheritdoc}
     */
    public function isScopeStore()
    {
        return !$this->isScopeGlobal() && !$this->isScopeWebsite();
    }

    /**
     * {@inheritdoc}
     */
    public function __sleep()
    {
        $this->unsetData('entity_type');

        return parent::__sleep();
    }
}

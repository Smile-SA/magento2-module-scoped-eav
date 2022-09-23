<?php

declare(strict_types=1);

namespace Smile\ScopedEav\Model\ResourceModel;

use Magento\Eav\Model\Entity\Attribute\AbstractAttribute;
use Magento\Framework\Model\Entity\ScopeInterface;
use Smile\ScopedEav\Api\Data\AttributeInterface as ScopedEavAttribute;

/**
 * Scoped EAV entity attribute persistor.
 */
class AttributePersistor extends \Magento\Eav\Model\ResourceModel\AttributePersistor
{
    /**
     * @inheritDoc
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    protected function getScopeValue(ScopeInterface $scope, AbstractAttribute $attribute, $useDefault = false)
    {
        if ($attribute instanceof ScopedEavAttribute) {
            $useDefault = $useDefault || $attribute->isScopeGlobal();
        }

        return parent::getScopeValue($scope, $attribute, $useDefault);
    }
}

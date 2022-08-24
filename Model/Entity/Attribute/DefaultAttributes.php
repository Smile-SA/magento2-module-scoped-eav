<?php

declare(strict_types=1);

namespace Smile\ScopedEav\Model\Entity\Attribute;

use Magento\Eav\Model\ResourceModel\Attribute\DefaultEntityAttributes\ProviderInterface;
use Smile\ScopedEav\Api\Data\EntityInterface;

/**
 * Scoped EAV entity default attributes..
 */
class DefaultAttributes implements ProviderInterface
{
    /**
     * List of default entity attributes.
     *
     * @return array
     */
    public function getDefaultAttributes(): array
    {
        return [
            EntityInterface::CREATED_AT,
            EntityInterface::UPDATED_AT,
            EntityInterface::ATTRIBUTE_SET_ID,
        ];
    }
}

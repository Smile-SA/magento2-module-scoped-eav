<?php

declare(strict_types=1);

namespace Smile\ScopedEav\Ui\DataProvider\Entity\Form\Modifier;

use Magento\Ui\DataProvider\Modifier\ModifierInterface;

/**
 * Scoped EAV form modifier abstract.
 */
abstract class AbstractModifier implements ModifierInterface
{
    protected const DATA_SOURCE_DEFAULT = 'entity';

    protected const DATA_SCOPE_ENTITY   = 'data.entity';

    protected const CONTAINER_PREFIX = 'container_';

    protected const META_CONFIG_PATH = 'arguments/data/config';

    protected const SORT_ORDER_MULTIPLIER = 10;

    /**
     * Retrieve next group sort order.
     *
     * @param array $meta Meta.
     * @param array|string $groupCodes Preceding group codes.
     * @param int $defaultSortOrder Default sort order.
     * @param int $iteration Value to be added.
     */
    protected function getNextGroupSortOrder(array $meta, $groupCodes, int $defaultSortOrder, int $iteration = 1): int
    {
        $groupCodes = (array) $groupCodes;

        foreach ($groupCodes as $groupCode) {
            if (isset($meta[$groupCode]['arguments']['data']['config']['sortOrder'])) {
                return $meta[$groupCode]['arguments']['data']['config']['sortOrder'] + $iteration;
            }
        }

        return $defaultSortOrder;
    }

    /**
     * Retrieve next attribute sort order.
     *
     * @param array $meta Meta.
     * @param array|string $attributeCodes Preceding attribute codes.
     * @param int $defaultSortOrder Default sort order.
     * @param int $iteration Value to be added.
     */
    protected function getNextAttributeSortOrder(
        array $meta,
        $attributeCodes,
        int $defaultSortOrder,
        int $iteration = 1
    ): int {
        $attributeCodes = (array) $attributeCodes;

        foreach ($meta as $groupMeta) {
            $defaultSortOrder = $this->getNextAttributeSortOrderInGroup(
                $groupMeta,
                $attributeCodes,
                $defaultSortOrder,
                $iteration
            );
        }

        return $defaultSortOrder;
    }

    /**
     * Search backwards starting from haystack length characters from the end.
     *
     * @param string $haystack Source string.
     * @param string $needle Searched string.
     */
    protected function startsWith(string $haystack, string $needle): bool
    {
        return $needle === '' || strrpos($haystack, $needle, - strlen($haystack)) !== false;
    }

    /**
     * Retrieve first panel name.
     *
     * @param array $meta Meta.
     */
    protected function getFirstPanelCode(array $meta): ?string
    {
        $min = null;
        $name = null;

        foreach ($meta as $fieldSetName => $fieldSetMeta) {
            if (isset($fieldSetMeta['arguments']['data']['config']['sortOrder'])) {
                if (null === $min || $fieldSetMeta['arguments']['data']['config']['sortOrder'] <= $min) {
                    $min = $fieldSetMeta['arguments']['data']['config']['sortOrder'];
                    $name = $fieldSetName;
                }
            }
        }

        return $name;
    }

    /**
     * Get group code by field.
     *
     * @param array $meta Meta.
     * @param string $field Field.
     */
    protected function getGroupCodeByField(array $meta, string $field): mixed
    {
        foreach ($meta as $groupCode => $groupData) {
            if (
                isset($groupData['children'][$field]) ||
                isset($groupData['children'][static::CONTAINER_PREFIX . $field])
            ) {
                return $groupCode;
            }
        }

        return false;
    }

    /**
     * Retrieve next attribute sort order in a group.
     *
     * @param array $meta Meta.
     * @param array $attributeCodes Preceding attribute codes.
     * @param int $defaultSortOrder Default sort order.
     * @param int $iteration Value to be added.
     */
    private function getNextAttributeSortOrderInGroup(
        array $meta,
        array $attributeCodes,
        int $defaultSortOrder,
        int $iteration = 1
    ): mixed {
        if (isset($meta['children'])) {
            foreach ($meta['children'] as $attributeCode => $attributeMeta) {
                if ($this->startsWith($attributeCode, self::CONTAINER_PREFIX)) {
                    $defaultSortOrder = $this->getNextAttributeSortOrderInGroup(
                        $attributeMeta,
                        $attributeCodes,
                        $defaultSortOrder,
                        $iteration
                    );
                } elseif (
                    in_array($attributeCode, $attributeCodes) &&
                    isset($attributeMeta['arguments']['data']['config']['sortOrder'])
                ) {
                    $defaultSortOrder = $attributeMeta['arguments']['data']['config']['sortOrder'] + $iteration;
                }
            }
        }

        return $defaultSortOrder;
    }
}

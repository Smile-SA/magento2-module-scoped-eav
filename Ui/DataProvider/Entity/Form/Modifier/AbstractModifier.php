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

namespace Smile\ScopedEav\Ui\DataProvider\Entity\Form\Modifier;

/**
 * Scoped EAV form modifier abstract.
 *
 * @category Smile
 * @package  Smile\ScopedEav
 * @author   Aurelien FOUCRET <aurelien.foucret@smile.fr>
 */
abstract class AbstractModifier implements \Magento\Ui\DataProvider\Modifier\ModifierInterface
{
    /**
     * @var string
     */
    const DATA_SOURCE_DEFAULT = 'entity';

    /**
     * @var string
     */
    const DATA_SCOPE_ENTITY   = 'data.entity';

    /**
     * @var string
     */
    const CONTAINER_PREFIX = 'container_';

    /**
     * @var string
     */
    const META_CONFIG_PATH = 'arguments/data/config';

    /**
     * @var int
     */
    const SORT_ORDER_MULTIPLIER = 10;

    /**
     * Retrieve next group sort order.
     *
     * @param array        $meta             Meta.
     * @param array|string $groupCodes       Preceding group codes.
     * @param int          $defaultSortOrder Default sort order.
     * @param int          $iteration        Value to be added.
     *
     * @return int
     */
    protected function getNextGroupSortOrder(array $meta, $groupCodes, $defaultSortOrder, $iteration = 1)
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
     * @param array        $meta             Meta.
     * @param array|string $attributeCodes   Preceding attribute codes.
     * @param int          $defaultSortOrder Default sort order.
     * @param int          $iteration        Value to be added.
     *
     * @return int
     */
    protected function getNextAttributeSortOrder(array $meta, $attributeCodes, $defaultSortOrder, $iteration = 1)
    {
        $attributeCodes = (array) $attributeCodes;

        foreach ($meta as $groupMeta) {
            $defaultSortOrder = $this->getNextAttributeSortOrderInGroup($groupMeta, $attributeCodes, $defaultSortOrder, $iteration);
        }

        return $defaultSortOrder;
    }

    /**
     * Search backwards starting from haystack length characters from the end.
     *
     * @param string $haystack Source string.
     * @param string $needle   Searched string.
     *
     * @return bool
     */
    protected function startsWith($haystack, $needle)
    {
        return $needle === '' || strrpos($haystack, $needle, - strlen($haystack)) !== false;
    }

    /**
     * Retrieve first panel name.
     *
     * @param array $meta Meta.
     *
     * @return string|null
     */
    protected function getFirstPanelCode(array $meta)
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
     * @param array  $meta  Meta.
     * @param string $field Field.
     *
     * @return string|bool
     */
    protected function getGroupCodeByField(array $meta, $field)
    {
        foreach ($meta as $groupCode => $groupData) {
            if (isset($groupData['children'][$field]) || isset($groupData['children'][static::CONTAINER_PREFIX . $field])) {
                return $groupCode;
            }
        }

        return false;
    }

    /**
     * Retrieve next attribute sort order in a group.
     *
     * @param array $meta             Meta.
     * @param array $attributeCodes   Preceding attribute codes.
     * @param int   $defaultSortOrder Default sort order.
     * @param int   $iteration        Value to be added.
     *
     * @return mixed
     */
    private function getNextAttributeSortOrderInGroup(array $meta, $attributeCodes, $defaultSortOrder, $iteration = 1)
    {
        if (isset($meta['children'])) {
            foreach ($meta['children'] as $attributeCode => $attributeMeta) {
                if ($this->startsWith($attributeCode, self::CONTAINER_PREFIX)) {
                    $defaultSortOrder = $this->getNextAttributeSortOrderInGroup(
                        $attributeMeta,
                        $attributeCodes,
                        $defaultSortOrder,
                        $iteration
                    );
                } elseif (in_array($attributeCode, $attributeCodes) && isset($attributeMeta['arguments']['data']['config']['sortOrder'])) {
                    $defaultSortOrder = $attributeMeta['arguments']['data']['config']['sortOrder'] + $iteration;
                }
            }
        }

        return $defaultSortOrder;
    }
}

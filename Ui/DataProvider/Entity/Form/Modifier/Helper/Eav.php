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

namespace Smile\ScopedEav\Ui\DataProvider\Entity\Form\Modifier\Helper;

use Magento\Eav\Api\Data\AttributeGroupInterface;
use Smile\ScopedEav\Api\Data\AttributeInterface;
use Smile\ScopedEav\Api\Data\EntityInterface;

/**
 * Scoped EAV form modifier EAV helper.
 *
 * @category Smile
 * @package  Smile\ScopedEav
 * @author   Aurelien FOUCRET <aurelien.foucret@smile.fr>
 */
class Eav
{
    /**
     * @var \Magento\Eav\Api\AttributeGroupRepositoryInterface
     */
    private $attributeGroupRepository;

    /**
     * @var \Magento\Eav\Api\AttributeRepositoryInterface
     */
    private $attributeRepository;

    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var \Magento\Framework\Api\SortOrderBuilder $searchCriteriaBuilder
     */
    private $sortOrderBuilder;

    /**
     * @var \Smile\ScopedEav\Helper\Data
     */
    private $eavHelper;

    /**
     * @var \Magento\Catalog\Model\Attribute\ScopeOverriddenValue
     */
    private $scopeOverriddenValue;

    /**
     * @var AttributeGroupInterface[]
     */
    private $attributeGroups = [];

    /**
     * @var AttributeInterface[]
     */
    private $attributes = [];

    /**
     * @var array
     */
    private $canDisplayUseDefault = [];

    /**
     * Constructor.
     *
     * @param \Magento\Eav\Api\AttributeGroupRepositoryInterface    $attributeGroupRepository Attribute group repository.
     * @param \Magento\Eav\Api\AttributeRepositoryInterface         $attributeRepository      Attribute repository.
     * @param \Magento\Framework\Api\SearchCriteriaBuilder          $searchCriteriaBuilder    Search criteria builder.
     * @param \Magento\Framework\Api\SortOrderBuilder               $sortOrderBuilder         Sort order builder.
     * @param \Magento\Catalog\Model\Attribute\ScopeOverriddenValue $scopeOverriddenValue     Scope attribute helper.
     * @param \Smile\ScopedEav\Helper\Data                          $eavHelper                Scoped EAV helper.
     */
    public function __construct(
        \Magento\Eav\Api\AttributeGroupRepositoryInterface $attributeGroupRepository,
        \Magento\Eav\Api\AttributeRepositoryInterface $attributeRepository,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\Framework\Api\SortOrderBuilder $sortOrderBuilder,
        \Magento\Catalog\Model\Attribute\ScopeOverriddenValue $scopeOverriddenValue,
        \Smile\ScopedEav\Helper\Data $eavHelper
    ) {
        $this->attributeGroupRepository = $attributeGroupRepository;
        $this->attributeRepository      = $attributeRepository;
        $this->searchCriteriaBuilder    = $searchCriteriaBuilder;
        $this->sortOrderBuilder         = $sortOrderBuilder;
        $this->scopeOverriddenValue     = $scopeOverriddenValue;
        $this->eavHelper                = $eavHelper;
    }

    /**
     * List of attribute group by attribute set id.
     *
     * @param int $attributeSetId Attribute set id.
     *
     * @return AttributeGroupInterface[]
     */
    public function getGroups($attributeSetId)
    {
        if (!isset($this->attributeGroups[$attributeSetId])) {
            $this->attributeGroups[$attributeSetId] = [];
            $searchCriteria = $this->prepareGroupSearchCriteria($attributeSetId)->create();

            $attributeGroupSearchResult = $this->attributeGroupRepository->getList($searchCriteria);

            foreach ($attributeGroupSearchResult->getItems() as $group) {
                $groupCode = $group->getAttributeGroupCode();
                $this->attributeGroups[$attributeSetId][$groupCode] = $group;
            }
        }

        return $this->attributeGroups[$attributeSetId];
    }

    /**
     * List of attribute by attribute set id.
     *
     * @param EntityInterface $entity         Entity.
     * @param int             $attributeSetId Attribute set id.
     *
     * @return AttributeInterface[]
     */
    public function getAttributes(EntityInterface $entity, $attributeSetId)
    {
        if (!isset($this->attributes[$attributeSetId])) {
            $this->attributes[$attributeSetId] = [];
            foreach ($this->getGroups($attributeSetId) as $group) {
                $groupCode = $group->getAttributeGroupCode();
                $this->attributes[$attributeSetId][$groupCode] = $this->loadAttributes($entity, $group);
            }
        }

        return $this->attributes[$attributeSetId];
    }

    /**
     * Scope label for an attribute.
     *
     * @param AttributeInterface $attribute Attribute.
     *
     * @return string|\Magento\Framework\Phrase
     */
    public function getScopeLabel(AttributeInterface $attribute)
    {
        return $this->eavHelper->getScopeLabel($attribute);
    }

    /**
     * Check if attribute is global.
     *
     * @param AttributeInterface $attribute Attribute.
     *
     * @return boolean
     */
    public function isScopeGlobal(AttributeInterface $attribute)
    {
        return $this->eavHelper->isScopeGlobal($attribute);
    }

    /**
     * Check if the attribute value have been overriden for the current store.
     *
     * @param EntityInterface    $entity    Entity
     * @param AttributeInterface $attribute Attribute.
     * @param int                $storeId   Store id.
     *
     * @return boolean
     */
    public function hasValueForStore(EntityInterface $entity, AttributeInterface $attribute, $storeId)
    {
        $hasValue       = false;
        $attributeCode  = $attribute->getAttributeCode();
        $interface      = $this->eavHelper->getEntityInterface($entity);

        try {
             $hasValue = $hasValue || $this->scopeOverriddenValue->containsValue($interface, $entity, $attributeCode, $storeId);
        } catch (\Exception $e) {
            ;
        }

        return $hasValue;
    }

    /**
     * Can the use default checkbox be displayed for an attribute.
     *
     * @param AttributeInterface $attribute Attribute.
     * @param EntityInterface    $entity    Entity.
     *
     * @return mixed
     */
    public function canDisplayUseDefault(AttributeInterface $attribute, EntityInterface $entity)
    {
        $attributeCode = $attribute->getAttributeCode();

        if (!isset($this->canDisplayUseDefault[$attributeCode])) {
            $isGlobal = $attribute->getScope() == $attribute::SCOPE_GLOBAL_TEXT;
            $this->canDisplayUseDefault[$attributeCode] = (!$isGlobal && $entity->getId() && $entity->getStoreId() > 0);
        }

        return $this->canDisplayUseDefault[$attributeCode];
    }

    /**
     * Return form element by frontend input.
     *
     * @param string $frontendInput Frontend input.
     *
     * @return string|NULL
     */
    public function getFormElement($frontendInput)
    {
        return $this->eavHelper->getFormElement($frontendInput);
    }

    /**
     * Prepare a search criteria that filter group by attribute set.
     *
     * @param int $attributeSetId Attribute set id.
     *
     * @return \Magento\Framework\Api\SearchCriteriaBuilder
     */
    private function prepareGroupSearchCriteria($attributeSetId)
    {
        return $this->searchCriteriaBuilder->addFilter(AttributeGroupInterface::ATTRIBUTE_SET_ID, $attributeSetId);
    }

    /**
     * Return attribute list for a group.
     *
     * @param EntityInterface         $entity Entity.
     * @param AttributeGroupInterface $group  Attribute group.
     *
     * @return \Magento\Eav\Api\Data\AttributeInterface[]
     */
    private function loadAttributes(EntityInterface $entity, AttributeGroupInterface $group)
    {
        $attributes = [];

        $sortOrder = $this->sortOrderBuilder
            ->setField('sort_order')
            ->setAscendingDirection()
            ->create();

        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter(AttributeGroupInterface::GROUP_ID, $group->getAttributeGroupId())
            ->addSortOrder($sortOrder)
            ->create();

        $entityTypeCode  = $this->eavHelper->getEntityMetadata($entity)->getEavEntityType();
        $groupAttributes = $this->attributeRepository->getList($entityTypeCode, $searchCriteria)->getItems();

        foreach ($groupAttributes as $attribute) {
            $attributes[] = $attribute;
        }

        return $attributes;
    }
}

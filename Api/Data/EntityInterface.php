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

namespace Smile\ScopedEav\Api\Data;

/**
 * Scoped entity interface.
 *
 * @api
 *
 * @category Smile
 * @package  Smile\ScopedEav
 * @author   Aurelien FOUCRET <aurelien.foucret@smile.fr>
 */
interface EntityInterface extends \Magento\Framework\Api\CustomAttributesDataInterface
{
    /**#@+
     * Constants defined for keys of data array
     */
    const NAME = 'name';

    const IS_ACTIVE = 'is_active';

    const ATTRIBUTE_SET_ID = 'attribute_set_id';

    const CREATED_AT = 'created_at';

    const UPDATED_AT = 'updated_at';
    /**#@-*/

    /**
     * Returns entity id.
     *
     * @return int|null
     */
    public function getId();

    /**
     * Returns entity attribute set id.
     *
     * @return int
     */
    public function getAttributeSetId();

    /**
     * Returns is active.
     *
     * @return boolean
     */
    public function getIsActive();

    /**
     * Returns entity name.
     *
     * @return string
     */
    public function getName();

    /**
     * Returns custom entity store id.
     *
     * @return int
     */
    public function getStoreId();

    /**
     * Returns custom entity creation date.
     *
     * @return string
     */
    public function getCreatedAt();

    /**
     * Returns custom entity update date.
     *
     * @return string
     */
    public function getUpdatedAt();

    /**
     * Set entity id
     *
     * @param int $entityId Entity id.
     *
     * @return $this
     */
    public function setId($entityId);

    /**
     * Set attribute set id.
     *
     * @param int $attributeSetId Attribute set id.
     *
     * @return $this
     */
    public function setAttributeSetId($attributeSetId);

    /**
     * Set is active.
     *
     * @param boolean $isActive Status.
     *
     * @return $this
     */
    public function setIsActive($isActive);

    /**
     * Set name.
     *
     * @param string $name Name.
     *
     * @return $this
     */
    public function setName($name);

    /**
     * Set creation date.
     *
     * @param string $createdAt Creation date.
     *
     * @return $this
     */
    public function setCreatedAt($createdAt);

    /**
     * Set update date.
     *
     * @param string $updatedAt Update date.
     *
     * @return $this
     */
    public function setUpdatedAt($updatedAt);

    /**
     * Set entity store id.
     *
     * @param int $storeId Store Id
     *
     * @return $this
     */
    public function setStoreId($storeId);
}

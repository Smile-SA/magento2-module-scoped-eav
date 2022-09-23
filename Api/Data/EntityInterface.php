<?php

declare(strict_types=1);

namespace Smile\ScopedEav\Api\Data;

use Magento\Framework\Api\CustomAttributesDataInterface;

/**
 * Scoped entity interface.
 *
 * @api
 */
interface EntityInterface extends CustomAttributesDataInterface
{
    /**#@+
     * Constants defined for keys of data array
     */
    public const NAME = 'name';

    public const IS_ACTIVE = 'is_active';

    public const ATTRIBUTE_SET_ID = 'attribute_set_id';

    public const CREATED_AT = 'created_at';

    public const UPDATED_AT = 'updated_at';

    public const IMAGE = 'image';

    public const DESCRIPTION = 'description';
    /**#@-*/

    /**
     * Returns entity id.
     *
     * @return mixed
     */
    public function getId();

    /**
     * Returns entity attribute set id.
     *
     * @return mixed
     */
    public function getAttributeSetId();

    /**
     * Returns is active.
     *
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     * @return mixed
     */
    public function getIsActive();

    /**
     * Returns entity name.
     *
     * @return string|null
     */
    public function getName(): ?string;

    /**
     * Returns entity store id.
     *
     * @return mixed
     */
    public function getStoreId();

    /**
     * Returns entity creation date.
     *
     * @return string|null
     */
    public function getCreatedAt(): ?string;

    /**
     * Returns entity update date.
     *
     * @return string|null
     */
    public function getUpdatedAt(): ?string;

    /**
     * Return entity description.
     *
     * @return string|null
     */
    public function getDescription(): ?string;

    /**
     * Return entity image.
     *
     * @return string|null
     */
    public function getImage(): ?string;

    /**
     * Set entity id
     *
     * @param int $entityId Entity id.
     * @return $this
     */
    public function setId(int $entityId);

    /**
     * Set attribute set id.
     *
     * @param int $attributeSetId Attribute set id.
     * @return $this
     */
    public function setAttributeSetId(int $attributeSetId): self;

    /**
     * Set is active.
     *
     * @param bool $isActive Status.
     * @return $this
     */
    public function setIsActive(bool $isActive): self;

    /**
     * Set name.
     *
     * @param string $name Name.
     * @return $this
     */
    public function setName(string $name): self;

    /**
     * Set creation date.
     *
     * @param string $createdAt Creation date.
     * @return $this
     */
    public function setCreatedAt(string $createdAt): self;

    /**
     * Set update date.
     *
     * @param string $updatedAt Update date.
     * @return $this
     */
    public function setUpdatedAt(string $updatedAt): self;

    /**
     * Set entity store id.
     *
     * @param mixed $storeId Store Id
     * @return $this
     */
    public function setStoreId($storeId);

    /**
     * Set entity description.
     *
     * @param string $description Description.
     * @return $this
     */
    public function setDescription(string $description);

    /**
     * Set entity image.
     *
     * @param string $image Image.
     * @return $this
     */
    public function setImage(string $image);
}

<?php

declare(strict_types=1);

namespace Smile\ScopedEav\Model;

use Magento\Catalog\Model\AbstractModel;
use Magento\Framework\Exception\LocalizedException;
use Smile\ScopedEav\Api\Data\EntityInterface;
use Smile\ScopedEav\Model\Entity\Attribute;

/**
 * Scoped EAV entity abstract model.
 *
 * @SuppressWarnings(PHPMD.CamelCasePropertyName)
 */
class AbstractEntity extends AbstractModel implements EntityInterface
{
    /**
     * Entity Store Id
     */
    const STORE_ID = 'store_id';

    /**
     * Name of object id field
     */
    protected $_idFieldName = 'entity_id';

    /**
     * {@inheritdoc}
     */
    public function getAttributeSetId()
    {
        return $this->_getData(self::ATTRIBUTE_SET_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function getCreatedAt(): ?string
    {
        return $this->_getData(self::CREATED_AT);
    }

    /**
     * {@inheritdoc}
     */
    public function getUpdatedAt(): ?string
    {
        return $this->_getData(self::UPDATED_AT);
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): ?string
    {
        return $this->_getData(self::NAME);
    }

    /**
     * {@inheritdoc}
     */
    public function getIsActive()
    {
        return $this->_getData(self::IS_ACTIVE);
    }

    /**
     * {@inheritdoc}
     */
    public function getStoreId()
    {
        if ($this->hasData(self::STORE_ID)) {
            return $this->getData(self::STORE_ID);
        }

        return $this->_storeManager->getStore()->getId();
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription(): ?string
    {
        return $this->_getData(self::DESCRIPTION);
    }

    /**
     * {@inheritdoc}
     */
    public function getImage(): ?string
    {
        return $this->_getData(self::IMAGE);
    }

    /**
     * Retrieve default attribute set id
     */
    public function getDefaultAttributeSetId(): int
    {
        return $this->getResource()->getEntityType()->getDefaultAttributeSetId();
    }

    /**
     * {@inheritdoc}
     */
    public function setAttributeSetId(int $attributeSetId): self
    {
        return $this->setData(self::ATTRIBUTE_SET_ID, $attributeSetId);
    }

    /**
     * {@inheritdoc}
     */
    public function setCreatedAt(string $createdAt): self
    {
        return $this->setData(self::CREATED_AT, $createdAt);
    }

    /**
     * {@inheritdoc}
     */
    public function setUpdatedAt(string $updatedAt): self
    {
        return $this->setData(self::UPDATED_AT, $updatedAt);
    }

    /**
     * {@inheritdoc}
     */
    public function setName(string $name): self
    {
        return $this->setData(self::NAME, $name);
    }

    /**
     * {@inheritdoc}
     */
    public function setIsActive(bool $isActive): self
    {
        return $this->setData(self::IS_ACTIVE, $isActive);
    }

    /**
     * {@inheritdoc}
     */
    public function setStoreId($storeId)
    {
        $this->setData(self::STORE_ID, $storeId);
    }

    /**
     * {@inheritdoc}
     */
    public function setDescription(string $description)
    {
        $this->setData(self::DESCRIPTION, $description);
    }

    /**
     * {@inheritdoc}
     */
    public function setImage(string $image)
    {
        $this->setData(self::IMAGE, $image);
    }

    /**
     * Return image url.
     *
     * @param string $attributeCode Attribute code.
     * @return bool|string
     * @throws LocalizedException
     */
    public function getImageUrl(string $attributeCode)
    {
        $url = false;
        $image = $this->getData($attributeCode);
        if ($image) {
            if (!is_string($image)) {
                throw new LocalizedException(
                    __('Something went wrong while getting the image url.')
                );
            }

            $url = $image;
            $isRelativeUrl = substr($image, 0, 1) === '/';
            if (!$isRelativeUrl) {
                $mediaBaseUrl = $this->_storeManager->getStore()->getBaseUrl(
                    \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
                );
                $url = $mediaBaseUrl
                    . 'scoped_eav/entity/'
                    . $image;
            }
        }

        return $url;
    }

    /**
     * Retrieve default entity static attributes.
     *
     * @return string[]
     */
    public function getDefaultAttributes(): array
    {
        return array_unique(array_merge($this->_getDefaultAttributes(), [$this->getEntityIdField(), $this->getLinkField()]));
    }

    /**
     * Re-declare attribute model
     *
     * @SuppressWarnings(PHPMD.CamelCaseMethodName)
     */
    protected function _getDefaultAttributeModel(): string
    {
        return Attribute::class;
    }

    /**
     * Retrieve default entity attributes
     *
     * @SuppressWarnings(PHPMD.CamelCaseMethodName)
     * @return string[]
     */
    protected function _getDefaultAttributes(): array
    {
        return ['entity_type_id', 'attribute_set_id', 'created_at', 'updated_at'];
    }
}

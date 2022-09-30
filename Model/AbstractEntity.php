<?php

declare(strict_types=1);

namespace Smile\ScopedEav\Model;

use Magento\Catalog\Model\AbstractModel;
use Magento\Framework\Exception\LocalizedException;
use Magento\Store\Model\Store;
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
    protected const STORE_ID = 'store_id';

    /**
     * @inheritDoc
     */
    protected $_idFieldName = 'entity_id';

    /**
     * @inheritDoc
     */
    public function getAttributeSetId()
    {
        return $this->_getData(self::ATTRIBUTE_SET_ID);
    }

    /**
     * @inheritDoc
     */
    public function getCreatedAt(): ?string
    {
        return $this->_getData(self::CREATED_AT);
    }

    /**
     * @inheritDoc
     */
    public function getUpdatedAt(): ?string
    {
        return $this->_getData(self::UPDATED_AT);
    }

    /**
     * @inheritDoc
     */
    public function getName(): ?string
    {
        return $this->_getData(self::NAME);
    }

    /**
     * @inheritDoc
     */
    public function getIsActive()
    {
        return $this->_getData(self::IS_ACTIVE);
    }

    /**
     * @inheritDoc
     */
    public function getStoreId()
    {
        if ($this->hasData(self::STORE_ID)) {
            return $this->getData(self::STORE_ID);
        }

        return $this->_storeManager->getStore()->getId();
    }

    /**
     * @inheritDoc
     */
    public function getDescription(): ?string
    {
        return $this->_getData(self::DESCRIPTION);
    }

    /**
     * @inheritDoc
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
        // @phpstan-ignore-next-line
        return $this->getResource()->getEntityType()->getDefaultAttributeSetId();
    }

    /**
     * @inheritDoc
     */
    public function setAttributeSetId(int $attributeSetId): self
    {
        return $this->setData(self::ATTRIBUTE_SET_ID, $attributeSetId);
    }

    /**
     * @inheritDoc
     */
    public function setCreatedAt(string $createdAt): self
    {
        return $this->setData(self::CREATED_AT, $createdAt);
    }

    /**
     * @inheritDoc
     */
    public function setUpdatedAt(string $updatedAt): self
    {
        return $this->setData(self::UPDATED_AT, $updatedAt);
    }

    /**
     * @inheritDoc
     */
    public function setName(string $name): self
    {
        return $this->setData(self::NAME, $name);
    }

    /**
     * @inheritDoc
     */
    public function setIsActive(bool $isActive): self
    {
        return $this->setData(self::IS_ACTIVE, $isActive);
    }

    /**
     * @inheritDoc
     */
    public function setStoreId($storeId)
    {
        return $this->setData(self::STORE_ID, $storeId);
    }

    /**
     * @inheritDoc
     */
    public function setDescription(string $description)
    {
        return $this->setData(self::DESCRIPTION, $description);
    }

    /**
     * @inheritDoc
     */
    public function setImage(string $image)
    {
        return $this->setData(self::IMAGE, $image);
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
                /** @var Store $store */
                $store = $this->_storeManager->getStore();
                $mediaBaseUrl = $store->getBaseUrl(
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
        return array_unique(
            array_merge(
                $this->_getDefaultAttributes(),
                [$this->getEntityIdField(), $this->getLinkField()]
            )
        );
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

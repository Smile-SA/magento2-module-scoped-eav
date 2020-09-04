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

namespace Smile\ScopedEav\Model;

use Smile\ScopedEav\Api\Data\EntityInterface;

/**
 * Scoped EAV entity abstract model.
 *
 * @SuppressWarnings(PHPMD.CamelCasePropertyName)
 *
 * @category Smile
 * @package  Smile\ScopedEav
 * @author   Aurelien FOUCRET <aurelien.foucret@smile.fr>
 */
class AbstractEntity extends \Magento\Catalog\Model\AbstractModel implements EntityInterface
{
    /**
     * Entity Store Id
     */
    const STORE_ID = 'store_id';

    /**
     * Name of object id field
     *
     * @var string
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
    public function getCreatedAt()
    {
        return $this->_getData(self::CREATED_AT);
    }

    /**
     * {@inheritdoc}
     */
    public function getUpdatedAt()
    {
        return $this->_getData(self::UPDATED_AT);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
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
    public function getDescription()
    {
        return $this->_getData(self::DESCRIPTION);
    }

    /**
     * {@inheritdoc}
     */
    public function getImage()
    {
        return $this->_getData(self::IMAGE);
    }

    /**
     * Retrieve default attribute set id
     *
     * @return int
     */
    public function getDefaultAttributeSetId()
    {
        return $this->getResource()->getEntityType()->getDefaultAttributeSetId();
    }

    /**
     * {@inheritdoc}
     */
    public function setAttributeSetId($attributeSetId)
    {
        return $this->setData(self::ATTRIBUTE_SET_ID, $attributeSetId);
    }

    /**
     * {@inheritdoc}
     */
    public function setCreatedAt($createdAt)
    {
        return $this->setData(self::CREATED_AT, $createdAt);
    }

    /**
     * {@inheritdoc}
     */
    public function setUpdatedAt($updatedAt)
    {
        return $this->setData(self::UPDATED_AT, $updatedAt);
    }

    /**
     * {@inheritdoc}
     */
    public function setName($name)
    {
        return $this->setData(self::NAME, $name);
    }

    /**
     * {@inheritdoc}
     */
    public function setIsActive($isActive)
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
    public function setDescription($description)
    {
        $this->setData(self::DESCRIPTION, $description);
    }

    /**
     * {@inheritdoc}
     */
    public function setImage($image)
    {
        $this->setData(self::IMAGE, $image);
    }

    /**
     * Return image url.
     *
     * @param string $attributeCode Attribute code.
     *
     * @return bool|string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getImageUrl($attributeCode)
    {
        $url = false;
        $image = $this->getData($attributeCode);
        if ($image) {
            if (!is_string($image)) {
                throw new \Magento\Framework\Exception\LocalizedException(
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
    public function getDefaultAttributes()
    {
        return array_unique(array_merge($this->_getDefaultAttributes(), [$this->getEntityIdField(), $this->getLinkField()]));
    }

    /**
     * Re-declare attribute model
     *
     * @SuppressWarnings(PHPMD.CamelCaseMethodName)
     *
     * @return string
     */
    protected function _getDefaultAttributeModel()
    {
        return \Smile\ScopedEav\Model\Entity\Attribute::class;
    }

    /**
     * Retrieve default entity attributes
     *
     * @SuppressWarnings(PHPMD.CamelCaseMethodName)
     *
     * @return string[]
     */
    protected function _getDefaultAttributes()
    {
        return ['entity_type_id', 'attribute_set_id', 'created_at', 'updated_at'];
    }
}

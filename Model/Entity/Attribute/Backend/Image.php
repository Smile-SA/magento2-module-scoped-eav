<?php
/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future.
 *
 * @category  Smile
 * @package   Smile\ScopedEav
 * @author    Maxime LECLERCQ <maxime.leclercq@smile.fr>
 * @copyright 2019 Smile
 * @license   Open Software License ("OSL") v. 3.0
 */
namespace Smile\ScopedEav\Model\Entity\Attribute\Backend;

/**
 * Scoped EAV image backend model.
 *
 * @category Smile
 * @package  Smile\ScopedEav
 * @author   Maxime LECLERCQ <maxime.leclercq@smile.fr>
 */
class Image extends \Magento\Catalog\Model\Category\Attribute\Backend\Image
{
    /**
     * @var \Magento\Catalog\Model\ImageUploader
     */
    private $imageUploader;

    /**
     * @var string
     */
    private $additionalData = '_additional_data_';

    /**
     * Image constructor.
     *
     * @param \Psr\Log\LoggerInterface                         $logger              Logger.
     * @param \Magento\Framework\Filesystem                    $filesystem          Filesystem.
     * @param \Magento\MediaStorage\Model\File\UploaderFactory $fileUploaderFactory File uploader factory.
     * @param \Magento\Catalog\Model\ImageUploader|null        $imageUploader       Image uploader.
     */
    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\MediaStorage\Model\File\UploaderFactory $fileUploaderFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager = null,
        \Magento\Catalog\Model\ImageUploader $imageUploader = null
    ) {
        parent::__construct($logger, $filesystem, $fileUploaderFactory, $storeManager, $imageUploader);
        $this->imageUploader = $imageUploader;
    }

    /**
     * Save uploaded file and set its name to category
     *
     * @param \Magento\Framework\DataObject $object Object model.
     *
     * @return \Magento\Catalog\Model\Category\Attribute\Backend\Image
     */
    public function afterSave($object)
    {
        $value = $object->getData($this->additionalData . $this->getAttribute()->getName());

        if ($this->isTmpFileAvailable($value) && $imageName = $this->getUploadedImageName($value)) {
            try {
                $this->getImageUploader()->moveFileFromTmp($imageName);
            } catch (\Exception $e) {
                $this->_logger->critical($e);
            }
        }

        return $this;
    }

    /**
     * @return \Magento\Catalog\Model\ImageUploader
     */
    private function getImageUploader()
    {
        return $this->imageUploader;
    }

    /**
     * Gets image name from $value array.
     * Will return empty string in a case when $value is not an array
     *
     * @param array $value Attribute value
     *
     * @return string
     */
    private function getUploadedImageName($value)
    {
        if (is_array($value) && isset($value[0]['name'])) {
            return $value[0]['name'];
        }

        return '';
    }

    /**
     * Check if temporary file is available for new image upload.
     *
     * @param array $value Attribute value.
     *
     * @return bool
     */
    private function isTmpFileAvailable($value)
    {
        return is_array($value) && isset($value[0]['tmp_name']);
    }
}

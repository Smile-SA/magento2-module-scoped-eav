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
namespace Smile\ScopedEav\Model\Entity;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\File\Mime;
use Magento\Framework\Filesystem;

/**
 * Scoped EAV file information.
 *
 * @category Smile
 * @package  Smile\ScopedEav
 * @author   Maxime LECLERCQ <maxime.leclercq@smile.fr>
 */
class FileInfo extends \Magento\Catalog\Model\Category\FileInfo
{
    const ENTITY_MEDIA_PATH = '/scoped_eav/entity';

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * FileInfo constructor.
     *
     * @param Filesystem $filesystem Filesystem.
     * @param Mime       $mime       File mine.
     */
    public function __construct(Filesystem $filesystem, Mime $mime)
    {
        parent::__construct($filesystem, $mime);
        $this->filesystem = $filesystem;
    }

    /**
     * Return true if file exist.
     *
     * @param string $fileName File name.
     *
     * @return bool
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function isExist($fileName)
    {
        $fileName = $this->getMediaDirectoryPathRelativeToBaseDirectoryPath() . self::ENTITY_MEDIA_PATH . '/' .$fileName;

        return parent::isExist($fileName);
    }

    /**
     * Return statistic of file.
     *
     * @param string $fileName File name.
     *
     * @return array
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function getStat($fileName)
    {
        $fileName = $this->getMediaDirectoryPathRelativeToBaseDirectoryPath() . self::ENTITY_MEDIA_PATH . '/' .$fileName;

        return parent::getStat($fileName);
    }

    /**
     * Return file mine type.
     *
     * @param string $fileName File name.
     *
     * @return string
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function getMimeType($fileName)
    {
        $fileName = $this->getMediaDirectoryPathRelativeToBaseDirectoryPath() . self::ENTITY_MEDIA_PATH . '/' .$fileName;

        return parent::getMimeType($fileName);
    }

    /**
     * Get media directory subpath relative to base directory path.
     *
     * @return string
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    private function getMediaDirectoryPathRelativeToBaseDirectoryPath()
    {
        $baseDirectoryPath = $this->getBaseDirectory()->getAbsolutePath();
        $mediaDirectoryPath = $this->getMediaDirectory()->getAbsolutePath();

        return substr($mediaDirectoryPath, strlen($baseDirectoryPath));
    }

    /**
     * Return base directory.
     *
     * @return Filesystem\Directory\ReadInterface|Filesystem\Directory\WriteInterface
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    private function getBaseDirectory()
    {
        return $this->filesystem->getDirectoryWrite(DirectoryList::ROOT);
    }

    /**
     * Return media directory.
     *
     * @return Filesystem\Directory\WriteInterface
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    private function getMediaDirectory()
    {
        return $this->filesystem->getDirectoryWrite(DirectoryList::MEDIA);
    }
}

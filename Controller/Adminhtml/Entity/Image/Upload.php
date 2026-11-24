<?php

declare(strict_types=1);

namespace Smile\ScopedEav\Controller\Adminhtml\Entity\Image;

use Exception;
use Magento\Backend\App\Action;
use Magento\Catalog\Model\ImageUploader;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\NotFoundException;

/**
 * Scoped EAV uploader controller.
 */
class Upload extends Action implements HttpPostActionInterface
{
    private ImageUploader $imageUploader;

    private string $aclResource;

    /**
     * Upload constructor.
     *
     * @param Action\Context $context Context.
     * @param ImageUploader $imageUploader Image uploader.
     * @param string $aclResource ACL resource required to upload (set it in the module using scoped EAV).
     */
    public function __construct(
        Action\Context $context,
        ImageUploader $imageUploader,
        string $aclResource = self::ADMIN_RESOURCE
    ) {
        parent::__construct($context);
        $this->imageUploader = $imageUploader;
        $this->aclResource = $aclResource;
    }

    /**
     * @inheritDoc
     */
    protected function _isAllowed(): bool
    {
        return $this->_authorization->isAllowed($this->aclResource);
    }

    /**
     * Execute action based on request and return result
     *
     * Note: Request will be added as operation argument in future
     *
     * @return ResultInterface|ResponseInterface
     * @throws NotFoundException
     */
    /**
     * Upload file controller action
     */
    public function execute(): ResultInterface
    {
        // @todo need https://patch-diff.githubusercontent.com/raw/magento/magento2/pull/19249.patch
        $imageId = $this->_request->getParam('param_name');

        try {
            $result = $this->imageUploader->saveFileToTmpDir($imageId);
        } catch (Exception $e) {
            $result = ['error' => $e->getMessage(), 'errorcode' => $e->getCode()];
        }
        /** @var Json $resultFactory */
        $resultFactory = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        return $resultFactory->setData($result);
    }
}

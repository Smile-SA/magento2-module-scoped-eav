<?php

declare(strict_types=1);

namespace Smile\ScopedEav\Controller\Adminhtml\Entity;

use Magento\Backend\App\Action\Context;
use Magento\Eav\Model\Config;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Store\Model\StoreManagerInterface;
use Smile\ScopedEav\Api\Data\EntityInterface;
use Smile\ScopedEav\Controller\Adminhtml\AbstractEntity;
use Smile\ScopedEav\Model\Entity\Attribute\Backend\Image;

/**
 * Scoped EAV entity save controller.
 */
class Save extends AbstractEntity
{
    private DataPersistorInterface $dataPersistor;

    private Config $eavConfig;

    /**
     * Constructor.
     *
     * @param Context $context Context.
     * @param BuilderInterface $entityBuilder Entity builder.
     * @param StoreManagerInterface $storeManager Store manager.
     * @param DataPersistorInterface $dataPersistor Data Persistor
     * @param Config $eavConfig Eav config.
     */
    public function __construct(
        Context $context,
        BuilderInterface $entityBuilder,
        StoreManagerInterface $storeManager,
        DataPersistorInterface $dataPersistor,
        Config $eavConfig
    ) {
        parent::__construct($context, $entityBuilder, $storeManager);

        $this->dataPersistor = $dataPersistor;
        $this->eavConfig = $eavConfig;
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        $storeId        = $this->getRequest()->getParam('store', 0);
        $entityId       = $this->getRequest()->getParam('id');
        $attributeSetId = $this->getRequest()->getParam('set');
        $redirectBack   = $this->getRequest()->getParam('back', false);

        $resultRedirect = $this->resultRedirectFactory->create();

        try {
            $entity = $this->getEntity();
            $entity->save();

            $entityId       = $entity->getEntityId();
            $attributeSetId = $entity->getAttributeSetId();

            $this->messageManager->addSuccessMessage(__('You saved the entity.'));
            $this->dataPersistor->clear('entity');

            $resultRedirect->setPath('*/*/index', ['store' => $storeId]);
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            $this->dataPersistor->set('entity', $this->getRequest()->getPostValue());
            $redirectBack = $entityId ? true : 'new';
        } catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
            $this->dataPersistor->set('entity', $this->getRequest()->getPostValue());
            $redirectBack = $entityId ? true : 'new';
        }

        if ($redirectBack === 'new') {
            $resultRedirect->setPath('*/*/new', ['set' => $attributeSetId]);
        } elseif ($redirectBack) {
            $resultRedirect->setPath(
                '*/*/edit',
                ['id' => $entityId, '_current' => true, 'set' => $attributeSetId, 'storeId' => $storeId]
            );
        }

        return $resultRedirect;
    }

    /**
     * @inheritDoc
     */
    protected function getEntity(): EntityInterface
    {
        $entity = parent::getEntity();

        $data = $this->getRequest()->getPostValue();
        $data = $this->imagePreprocessing($entity, $data);
        $entity->addData($data['entity']);

        $useDefaults = (array) $this->getRequest()->getPost('use_default', []);

        foreach ($useDefaults as $attributeCode => $useDefault) {
            if ((bool) $useDefault) {
                $entity->setData($attributeCode, null);
            }
        }

        return $entity;
    }

    /**
     * Sets image attribute data to false if image was removed.
     *
     * @param EntityInterface $entity Current entity.
     * @param array $data Data.
     * @return array
     * @throws LocalizedException
     */
    private function imagePreprocessing(EntityInterface $entity, array $data): array
    {
        $entityType = $this->eavConfig->getEntityType($entity->getResource()->getEntityType()->getEntityTypeCode());

        foreach ($entityType->getAttributeCollection() as $attributeModel) {
            $attributeCode = $attributeModel->getAttributeCode();
            $backendModel = $attributeModel->getBackend();
            if (isset($data['entity'][$attributeCode]) || !$backendModel instanceof Image) {
                continue;
            }

            $data['entity'][$attributeCode] = '';
        }

        return $data;
    }
}

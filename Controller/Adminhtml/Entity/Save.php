<?php

declare(strict_types=1);

namespace Smile\ScopedEav\Controller\Adminhtml\Entity;

use Magento\Backend\App\Action;
use Smile\ScopedEav\Controller\Adminhtml\AbstractEntity;

/**
 * Scoped EAV entity save controller.
 */
class Save extends \Smile\ScopedEav\Controller\Adminhtml\AbstractEntity
{
    /**
     * @var \Magento\Framework\App\Request\DataPersistorInterface
     */
    private $dataPersistor;

    /**
     * @var \Magento\Eav\Model\Config
     */
    private $eavConfig;

    /**
     * Constructor.
     *
     * @param \Magento\Backend\App\Action\Context                   $context       Context.
     * @param BuilderInterface                                      $entityBuilder Entity builder.
     * @param \Magento\Store\Model\StoreManagerInterface            $storeManager  Store manager.
     * @param \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor Data Persistor
     * @param \Magento\Eav\Model\Config                             $eavConfig     Eav config.
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        BuilderInterface $entityBuilder,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor,
        \Magento\Eav\Model\Config $eavConfig
    ) {
        parent::__construct($context, $entityBuilder, $storeManager);

        $this->dataPersistor = $dataPersistor;
        $this->eavConfig = $eavConfig;
    }

    /**
     * {@inheritDoc}
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
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
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
            $resultRedirect->setPath('*/*/edit', ['id' => $entityId, '_current' => true, 'set' => $attributeSetId, 'storeId' => $storeId]);
        }

        return $resultRedirect;
    }

    /**
     * {@inheritDoc}
     */
    protected function getEntity()
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
     * @param \Smile\ScopedEav\Api\Data\EntityInterface $entity Current entity.
     * @param array                                     $data   Data.
     *
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function imagePreprocessing(\Smile\ScopedEav\Api\Data\EntityInterface $entity, $data)
    {
        $entityType = $this->eavConfig->getEntityType($entity->getResource()->getEntityType()->getEntityTypeCode());

        foreach ($entityType->getAttributeCollection() as $attributeModel) {
            $attributeCode = $attributeModel->getAttributeCode();
            $backendModel = $attributeModel->getBackend();
            if (isset($data['entity'][$attributeCode]) || !$backendModel instanceof \Smile\ScopedEav\Model\Entity\Attribute\Backend\Image) {
                continue;
            }

            $data['entity'][$attributeCode] = '';
        }

        return $data;
    }
}

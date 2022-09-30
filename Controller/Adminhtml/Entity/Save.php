<?php

declare(strict_types=1);

namespace Smile\ScopedEav\Controller\Adminhtml\Entity;

use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\ForwardFactory;
use Magento\Eav\Model\Config;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\App\Request\Http;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\AbstractModel;
use Magento\Store\Model\StoreManagerInterface;
use Smile\ScopedEav\Api\Data\EntityInterface;
use Smile\ScopedEav\Controller\Adminhtml\AbstractEntity;
use Smile\ScopedEav\Model\Entity\Attribute\Backend\Image;

/**
 * Scoped EAV entity save controller.
 */
class Save extends AbstractEntity implements HttpPostActionInterface
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
     * @param ForwardFactory $resultForwardFactory Forward.
     */
    public function __construct(
        Context $context,
        BuilderInterface $entityBuilder,
        StoreManagerInterface $storeManager,
        DataPersistorInterface $dataPersistor,
        Config $eavConfig,
        ForwardFactory $resultForwardFactory
    ) {
        parent::__construct($context, $entityBuilder, $storeManager, $resultForwardFactory);

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
            /** @var AbstractModel $entity */
            $entity = $this->getEntity();
            $entity->save();

            $entityId       = $entity->getEntityId();
            $attributeSetId = $entity->getAttributeSetId();

            $this->messageManager->addSuccessMessage(
                (string) __('You saved the entity.')
            );
            $this->dataPersistor->clear('entity');

            $resultRedirect->setPath('*/*/index', ['store' => $storeId]);
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            /** @var Http $request */
            $request = $this->getRequest();
            $this->dataPersistor->set('entity', $request->getPostValue());
            $redirectBack = $entityId ? true : 'new';
        } catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
            /** @var Http $request */
            $request = $this->getRequest();
            $this->dataPersistor->set('entity', $request->getPostValue());
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

        /** @var Http $request */
        $request = $this->getRequest();
        $data = $request->getPostValue();
        $data = $this->imagePreprocessing($entity, $data);

        /** @var DataObject $entity */
        $entity->addData($data['entity']);

        $useDefaults = (array) $request->getPost('use_default', []);

        foreach ($useDefaults as $attributeCode => $useDefault) {
            if ((bool) $useDefault) {
                $entity->setData($attributeCode, null);
            }
        }

        /** @var EntityInterface $entity */
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
        // @phpstan-ignore-next-line
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

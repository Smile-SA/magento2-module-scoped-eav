<?php
/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future.
 *
 * @category  Smile
 * @package   Smile\ScopedEav
 * @author    Aurelien FOUCRET <aurelien.foucret@smile.fr>
 * @copyright 2017 Smile
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Smile\ScopedEav\Controller\Adminhtml\Entity;

use Magento\Backend\App\Action;
use Smile\ScopedEav\Controller\Adminhtml\AbstractEntity;

/**
 * Scoped EAV entity save controller.
 *
 * @category Smile
 * @package  Smile\ScopedEav
 * @author   Aurelien FOUCRET <aurelien.foucret@smile.fr>
 */
class Save extends \Smile\ScopedEav\Controller\Adminhtml\AbstractEntity
{
    /**
     * @var \Magento\Framework\App\Request\DataPersistorInterface
     */
    private $dataPersistor;

    /**
     * Constructor.
     *
     * @param \Magento\Backend\App\Action\Context                   $context       Context.
     * @param Entity\BuilderInterface                               $entityBuilder Entity builder.
     * @param \Magento\Store\Model\StoreManagerInterface            $storeManager  Store manager.
     * @param \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor Data Persistor
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        BuilderInterface $entityBuilder,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor
    ) {
        parent::__construct($context, $entityBuilder, $storeManager);

        $this->dataPersistor = $dataPersistor;
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

        $entity->addData($data['entity']);

        $useDefaults = (array) $this->getRequest()->getPost('use_default', []);

        foreach ($useDefaults as $attributeCode => $useDefault) {
            if ((bool) $useDefault) {
                $entity->setData($attributeCode, null);
            }
        }

        return $entity;
    }
}

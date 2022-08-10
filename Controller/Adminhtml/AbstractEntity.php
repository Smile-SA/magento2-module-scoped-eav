<?php

declare(strict_types=1);

namespace Smile\ScopedEav\Controller\Adminhtml;

/**
 * Scoped EAV entity attribute set admin abstract controller.
 */
abstract class AbstractEntity extends \Magento\Backend\App\Action
{
    /**
     * @var Entity\BuilderInterface
     */
    protected $entityBuilder;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * Constructor.
     *
     * @param \Magento\Backend\App\Action\Context        $context       Context.
     * @param Entity\BuilderInterface                    $entityBuilder Entity builder.
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager  Store manager.
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        Entity\BuilderInterface $entityBuilder,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        parent::__construct($context);

        $this->entityBuilder = $entityBuilder;
        $this->storeManager  = $storeManager;
    }

    /**
     * Create the page.
     *
     * @param \Magento\Framework\Phrase|null $title Page title.
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    protected function createActionPage($title = null)
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->_view->getPage()->initLayout();

        if (!empty($title)) {
            $resultPage->addBreadcrumb($title, $title);
            $resultPage->getConfig()->getTitle()->prepend($title);
        }

        return $resultPage;
    }

    /**
     * Return current entity.
     *
     * @return \Smile\ScopedEav\Api\Data\EntityInterface
     */
    protected function getEntity()
    {
        return $this->entityBuilder->build($this->getRequest());
    }

    /**
     * Return current store id.
     *
     * @return int
     */
    protected function getStoreId()
    {
        $storeId = $this->getRequest()->getParam('store', 0);
        $store   = $this->storeManager->getStore($storeId);
        $this->storeManager->setCurrentStore($store->getCode());

        return $storeId;
    }
}

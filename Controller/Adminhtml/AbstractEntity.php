<?php

declare(strict_types=1);

namespace Smile\ScopedEav\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\ForwardFactory;
use Magento\Backend\Model\View\Result\Page;
use Magento\Framework\Phrase;
use Magento\Store\Model\StoreManagerInterface;
use Smile\ScopedEav\Api\Data\EntityInterface;

/**
 * Scoped EAV entity attribute set admin abstract controller.
 */
abstract class AbstractEntity extends Action
{
    protected Entity\BuilderInterface $entityBuilder;

    protected StoreManagerInterface $storeManager;

    protected ForwardFactory $resultForwardFactory;

    /**
     * Constructor.
     *
     * @param Context $context Context.
     * @param Entity\BuilderInterface $entityBuilder Entity builder.
     * @param StoreManagerInterface $storeManager Store manager.
     * @param ForwardFactory $resultForwardFactory Forward.
     */
    public function __construct(
        Context $context,
        Entity\BuilderInterface $entityBuilder,
        StoreManagerInterface $storeManager,
        ForwardFactory $resultForwardFactory
    ) {
        parent::__construct($context);

        $this->entityBuilder = $entityBuilder;
        $this->storeManager  = $storeManager;
        $this->resultForwardFactory = $resultForwardFactory;
    }

    /**
     * Create the page.
     *
     * @param Phrase|string $title Page title.
     */
    protected function createActionPage($title = null): Page
    {
        /** @var Page $resultPage */
        $resultPage = $this->_view->getPage()->initLayout();

        if (!empty($title)) {
            $resultPage->addBreadcrumb($title, $title);
            $resultPage->getConfig()->getTitle()->prepend($title);
        }

        return $resultPage;
    }

    /**
     * Return current entity.
     */
    protected function getEntity(): EntityInterface
    {
        return $this->entityBuilder->build($this->getRequest());
    }

    /**
     * Return current store id.
     */
    protected function getStoreId(): int
    {
        $storeId = $this->getRequest()->getParam('store', 0);
        $store   = $this->storeManager->getStore($storeId);
        $this->storeManager->setCurrentStore($store->getCode());

        return $storeId;
    }
}

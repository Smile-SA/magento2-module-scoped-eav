<?php

declare(strict_types=1);

namespace Smile\ScopedEav\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Page;
use Magento\Eav\Api\AttributeSetRepositoryInterface;
use Magento\Eav\Api\Data\AttributeSetInterface;
use Magento\Eav\Api\Data\AttributeSetInterfaceFactory;
use Magento\Eav\Model\Config;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Phrase;
use Magento\Framework\Registry;

/**
 * Scoped EAV entity attribute set admin abstract controller.
 */
abstract class AbstractSet extends Action
{
    private Registry $registry;

    private AttributeSetRepositoryInterface $attributeSetRepository;

    private AttributeSetInterfaceFactory $attributeSetFactory;

    private Config $eavConfig;

    protected string $entityTypeCode;

    /**
     * Constructor.
     *
     * @param Context $context Context.
     * @param Registry $registry Registry.
     * @param Config $eavConfig EAV config.
     * @param AttributeSetRepositoryInterface $attributeSetRepository Attribute set repository.
     * @param AttributeSetInterfaceFactory $attributeSetFactory Attribute set factory.
     */
    public function __construct(
        Context $context,
        Registry $registry,
        Config $eavConfig,
        AttributeSetRepositoryInterface $attributeSetRepository,
        AttributeSetInterfaceFactory $attributeSetFactory
    ) {
        parent::__construct($context);
        $this->registry               = $registry;
        $this->eavConfig              = $eavConfig;
        $this->attributeSetRepository = $attributeSetRepository;
        $this->attributeSetFactory    = $attributeSetFactory;
    }

    /**
     * Define in register entity type code as entityType
     *
     * @return $this
     */
    protected function setTypeId(): self
    {
        if ($this->registry->registry('entityType') == null) {
            $entityType = $this->eavConfig->getEntityType($this->entityTypeCode);
            $this->registry->register('entityType', $entityType->getId());
        }

        return $this;
    }

    /**
     * Return current type id.
     */
    protected function getTypeId(): string
    {
        if ($this->registry->registry('entityType') == null) {
            $this->setTypeId();
        }

        return $this->registry->registry('entityType');
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
     * Get current attribute set.
     */
    protected function getAttributeSet(): AttributeSetInterface
    {
        $attributeSet = $this->registry->registry('current_attribute_set');

        if ($attributeSet == null) {
            /** @var int $entityTypeId */
            $entityTypeId = $this->getTypeId();
            $attributeSet = $this->attributeSetFactory->create()->setEntityTypeId($entityTypeId);
            $attributeSetId = $this->getRequest()->getParam('id');
            if ($attributeSetId) {
                $attributeSet = $this->attributeSetRepository->get($attributeSetId);

                if ($attributeSet->getEntityTypeId() != $entityTypeId) {
                    throw new NoSuchEntityException(__("Attribute set not found."));
                }
            }

            $this->registry->register('current_attribute_set', $attributeSet);
        }

        return $attributeSet;
    }
}

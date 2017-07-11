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

namespace Smile\ScopedEav\Controller\Adminhtml;

use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Scoped EAV entity attribute set admin abstract controller.
 *
 * @category Smile
 * @package  Smile\ScopedEav
 * @author   Aurelien FOUCRET <aurelien.foucret@smile.fr>
 */
abstract class AbstractSet extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Framework\Registry
     */
    private $registry;

    /**
     * @var \Magento\Eav\Api\AttributeSetRepositoryInterface
     */
    private $attributeSetRepository;

    /**
     * @var \Magento\Eav\Api\Data\AttributeSetInterfaceFactory
     */
    private $attributeSetFactory;

    /**
     * @var \Magento\Eav\Model\Config
     */
    private $eavConfig;

    /**
     * @var string
     */
    protected $entityTypeCode;

    /**
     * Constructor.
     *
     * @param \Magento\Backend\App\Action\Context                $context                Context.
     * @param \Magento\Framework\Registry                        $registry               Registry.
     * @param \Magento\Eav\Model\Config                          $eavConfig              EAV config.
     * @param \Magento\Eav\Api\AttributeSetRepositoryInterface   $attributeSetRepository Attribute set repository.
     * @param \Magento\Eav\Api\Data\AttributeSetInterfaceFactory $attributeSetFactory    Attribute set factory.
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Eav\Api\AttributeSetRepositoryInterface $attributeSetRepository,
        \Magento\Eav\Api\Data\AttributeSetInterfaceFactory $attributeSetFactory
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
     * @return void
     */
    protected function setTypeId()
    {
        if ($this->registry->registry('entityType') == null) {
            $entityType = $this->eavConfig->getEntityType($this->entityTypeCode);
            $this->registry->register('entityType', $entityType->getId());
        }

        return $this;
    }

    /**
     * Return current type id.
     *
     * @return string
     */
    protected function getTypeId()
    {
        if ($this->registry->registry('entityType') == null) {
            $this->setTypeId();
        }

        return $this->registry->registry('entityType');
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
     * Get current attribute set.
     *
     * @return \Magento\Eav\Api\Data\AttributeSetInterface
     */
    protected function getAttributeSet()
    {
        $attributeSet = $this->registry->registry('current_attribute_set');

        if ($attributeSet == null) {
            $entityTypeId = $this->getTypeId();
            $attributeSet = $this->attributeSetFactory->create()->setEntityTypeId($entityTypeId);

            if ($attributeSetId = $this->getRequest()->getParam('id')) {
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

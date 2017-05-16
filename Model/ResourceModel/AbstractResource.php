<?php
/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Smile Elastic Suite to newer
 * versions in the future.
 *
 *
 * @category  Smile
 * @package   Smile\ScopedEav
 * @author    Aurelien FOUCRET <aurelien.foucret@smile.fr>
 * @copyright 2016 Smile
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Smile\ScopedEav\Model\ResourceModel;

/**
 * Scoped EAV entity resource model.
 *
 * @category Smile
 * @package  Smile\ScopedEav
 * @author   Aurelien FOUCRET <aurelien.foucret@smile.fr>
 */
class AbstractResource extends \Magento\Catalog\Model\ResourceModel\AbstractResource
{
    /**
     * @var \Magento\Framework\EntityManager\EntityManager
     */
    private $entityManager;

    /**
     * @var \Magento\Eav\Model\Entity\TypeFactory
     */
    private $typeFactory;

    /**
     * @var \Magento\Eav\Model\Entity\Attribute\SetFactory
     */
    private $setFactory;

    /**
     * @var \Smile\ScopedEav\Model\Entity\Attribute\DefaultAttributes
     */
    private $defaultAttributes;

    /**
     * AbstractResource constructor.
     *
     * @param \Magento\Eav\Model\Entity\Context                         $context           Context.
     * @param \Magento\Store\Model\StoreManagerInterface                $storeManager      Store manager.
     * @param \Magento\Catalog\Model\Factory                            $modelFactory      Model factory.
     * @param \Magento\Framework\EntityManager\EntityManager            $entityManager     Entity manager.
     * @param \Magento\Eav\Model\Entity\TypeFactory                     $typeFactory       Entity type factory.
     * @param \Magento\Eav\Model\Entity\Attribute\SetFactory            $setFactory        Attribute set factory.
     * @param \Smile\ScopedEav\Model\Entity\Attribute\DefaultAttributes $defaultAttributes Default attributes.
     * @param array                                                     $data              Additional data.
     */
    public function __construct(
        \Magento\Eav\Model\Entity\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\Factory $modelFactory,
        \Magento\Framework\EntityManager\EntityManager $entityManager,
        \Magento\Eav\Model\Entity\TypeFactory $typeFactory,
        \Magento\Eav\Model\Entity\Attribute\SetFactory $setFactory,
        \Smile\ScopedEav\Model\Entity\Attribute\DefaultAttributes $defaultAttributes,
        array $data = []
    ) {
        parent::__construct($context, $storeManager, $modelFactory, $data);

        $this->entityManager     = $entityManager;
        $this->typeFactory       = $typeFactory;
        $this->setFactory        = $setFactory;
        $this->defaultAttributes = $defaultAttributes;
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultAttributeSourceModel()
    {
        return \Magento\Eav\Model\Entity\Attribute\Source\Table::class;
    }

    /**
     * {@inheritdoc}
     */
    public function validate($object)
    {
        $entityType   = $this->typeFactory->create()->loadByCode($this->getEntityType());
        $attributeSet = $this->setFactory->create()->load($object->getAttributeSetId());

        if ($attributeSet->getEntityTypeId() != $entityType->getId()) {
            return ['attribute_set' => 'Invalid attribute set entity type'];
        }

        return parent::validate($object);
    }

    /**
     * {@inheritdoc}
     */
    public function load($object, $entityId, $attributes = [])
    {
        $this->loadAttributesMetadata($attributes);
        $this->entityManager->load($object, $entityId);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function save(\Magento\Framework\Model\AbstractModel $object)
    {
        $this->entityManager->save($object);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function delete($object)
    {
        $this->entityManager->delete($object);

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @SuppressWarnings(PHPMD.CamelCaseMethodName)
     */
    protected function _getDefaultAttributes()
    {
        return $this->defaultAttributes->getDefaultAttributes();
    }

    /**
     * {@inheritdoc}
     *
     * @SuppressWarnings(PHPMD.CamelCaseMethodName)
     */
    protected function _getDefaultAttributeModel()
    {
        return \Smile\ScopedEav\Model\Entity\Attribute::class;
    }
}

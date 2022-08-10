<?php

declare(strict_types=1);

namespace Smile\ScopedEav\Model\ResourceModel;

use Magento\Eav\Model\Entity\Context;

/**
 * Scoped EAV entity resource model.
 */
class AbstractResource extends \Magento\Eav\Model\Entity\AbstractEntity
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
     * @param \Magento\Framework\EntityManager\EntityManager            $entityManager     Entity manager.
     * @param \Magento\Eav\Model\Entity\TypeFactory                     $typeFactory       Entity type factory.
     * @param \Magento\Eav\Model\Entity\Attribute\SetFactory            $setFactory        Attribute set factory.
     * @param \Smile\ScopedEav\Model\Entity\Attribute\DefaultAttributes $defaultAttributes Default attributes.
     * @param array                                                     $data              Additional data.
     */
    public function __construct(
        Context $context,
        \Magento\Framework\EntityManager\EntityManager $entityManager,
        \Magento\Eav\Model\Entity\TypeFactory $typeFactory,
        \Magento\Eav\Model\Entity\Attribute\SetFactory $setFactory,
        \Smile\ScopedEav\Model\Entity\Attribute\DefaultAttributes $defaultAttributes,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->entityManager = $entityManager;
        $this->typeFactory = $typeFactory;
        $this->setFactory = $setFactory;
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
        $this->loadAllAttributes();
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

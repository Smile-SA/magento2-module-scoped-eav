<?php

declare(strict_types=1);

namespace Smile\ScopedEav\Model\ResourceModel;

use Magento\Eav\Model\Entity\AbstractEntity;
use Magento\Eav\Model\Entity\Attribute\Source\Table;
use Magento\Eav\Model\Entity\Context;
use Magento\Framework\EntityManager\EntityManager;
use Magento\Framework\Model\AbstractModel;
use Smile\ScopedEav\Model\Entity\Attribute;
use Smile\ScopedEav\Model\Entity\Attribute\DefaultAttributes;

/**
 * Scoped EAV entity resource model.
 */
class AbstractResource extends AbstractEntity
{
    private EntityManager $entityManager;

    private \Magento\Eav\Model\Entity\TypeFactory $typeFactory;

    private \Magento\Eav\Model\Entity\Attribute\SetFactory $setFactory;

    private DefaultAttributes $defaultAttributes;

    /**
     * AbstractResource constructor.
     *
     * @param Context $context Context.
     * @param EntityManager $entityManager Entity manager.
     * @param \Magento\Eav\Model\Entity\TypeFactory $typeFactory Entity type factory.
     * @param \Magento\Eav\Model\Entity\Attribute\SetFactory $setFactory Attribute set factory.
     * @param DefaultAttributes $defaultAttributes Default attributes.
     * @param array                                                     $data              Additional data.
     */
    public function __construct(
        Context $context,
        EntityManager $entityManager,
        \Magento\Eav\Model\Entity\TypeFactory $typeFactory,
        \Magento\Eav\Model\Entity\Attribute\SetFactory $setFactory,
        DefaultAttributes $defaultAttributes,
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
        return Table::class;
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
    public function save(AbstractModel $object)
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
        return Attribute::class;
    }
}

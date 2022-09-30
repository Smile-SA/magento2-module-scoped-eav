<?php

declare(strict_types=1);

namespace Smile\ScopedEav\Model\ResourceModel;

use Magento\Eav\Model\Entity\AbstractEntity;
use Magento\Eav\Model\Entity\Attribute\SetFactory;
use Magento\Eav\Model\Entity\Attribute\Source\Table;
use Magento\Eav\Model\Entity\Context;
use Magento\Eav\Model\Entity\TypeFactory;
use Magento\Framework\EntityManager\EntityManager;
use Smile\ScopedEav\Model\Entity\Attribute;
use Smile\ScopedEav\Model\Entity\Attribute\DefaultAttributes;

/**
 * Scoped EAV entity resource model.
 */
class AbstractResource extends AbstractEntity
{
    private EntityManager $entityManager;

    private TypeFactory $typeFactory;

    private SetFactory $setFactory;

    private DefaultAttributes $defaultAttributes;

    /**
     * AbstractResource constructor.
     *
     * @param Context $context Context.
     * @param EntityManager $entityManager Entity manager.
     * @param TypeFactory $typeFactory Entity type factory.
     * @param SetFactory $setFactory Attribute set factory.
     * @param DefaultAttributes $defaultAttributes Default attributes.
     * @param array $data Additional data.
     */
    public function __construct(
        Context $context,
        EntityManager $entityManager,
        TypeFactory $typeFactory,
        SetFactory $setFactory,
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
     * @inheritDoc
     */
    public function getDefaultAttributeSourceModel()
    {
        return Table::class;
    }

    /**
     * @inheritDoc
     */
    public function validate($object)
    {
        /** @var string $codeEntityType */
        $codeEntityType = $this->getEntityType();
        $entityType = $this->typeFactory->create()->loadByCode($codeEntityType);
        $attributeSet = $this->setFactory->create()->load($object->getAttributeSetId());

        if ($attributeSet->getEntityTypeId() != $entityType->getId()) {
            return ['attribute_set' => 'Invalid attribute set entity type'];
        }

        return parent::validate($object);
    }

    /**
     * @inheritDoc
     */
    public function load($object, $entityId, $attributes = [])
    {
        $this->loadAttributesMetadata($attributes);
        $this->entityManager->load($object, (string) $entityId);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function save($object)
    {
        $this->loadAllAttributes();
        $this->entityManager->save($object);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function delete($object)
    {
        $this->entityManager->delete($object);

        return $this;
    }

    /**
     * @inheritDoc
     * @SuppressWarnings(PHPMD.CamelCaseMethodName)
     */
    protected function _getDefaultAttributes()
    {
        return $this->defaultAttributes->getDefaultAttributes();
    }

    /**
     * @inheritDoc
     * @SuppressWarnings(PHPMD.CamelCaseMethodName)
     */
    protected function _getDefaultAttributeModel()
    {
        return Attribute::class;
    }
}

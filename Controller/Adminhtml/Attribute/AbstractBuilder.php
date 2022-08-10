<?php

declare(strict_types=1);

namespace Smile\ScopedEav\Controller\Adminhtml\Attribute;

use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Scoped entity attribute builder used in controllers.
 */
abstract class AbstractBuilder implements BuilderInterface
{
    /**
     * @var \Magento\Eav\Model\Config
     */
    private $eavConfig;

    /**
     * @var \Magento\Framework\Registry
     */
    private $registry;

    /**
     * Constructor.
     *
     * @param \Magento\Framework\Registry $registry  Registry.
     * @param \Magento\Eav\Model\Config   $eavConfig EAV configuration.
     */
    public function __construct(
        \Magento\Framework\Registry $registry,
        \Magento\Eav\Model\Config $eavConfig
    ) {
        $this->eavConfig = $eavConfig;
        $this->registry  = $registry;
    }

    /**
     * {@inheritDoc}
     */
    public function build(\Magento\Framework\App\RequestInterface $request)
    {
        $attribute = $this->registry->registry('entity_attribute');

        if ($attribute === null) {
            $entityTypeId = $this->eavConfig->getEntityType($this->getEntityTypeCode())->getId();

            $attributeId   = $request->getParam('attribute_id');
            $attributeCode = $request->getParam('attribute_code');

            $attribute = $this->getAttributeFactory()->create();
            $attribute->setEntityTypeId($entityTypeId);

            if ($attributeCode && $attributeId == null) {
                try {
                    $this->getAttributeRepository()->get($attributeCode);
                    throw new AlreadyExistsException(__('An attribute with the same code already exists.'));
                } catch (NoSuchEntityException $e) {
                    ; // Does nothing since no other attribute exists => attribute code is valid.
                }
            } elseif ($attributeId != null) {
                $attribute = $this->getAttributeRepository()->get($attributeId);
            }

            $this->registry->register('entity_attribute', $attribute);
        }

        return $attribute;
    }

    /**
     * Entity attribute factory.
     *
     * @return \Smile\ScopedEav\Api\Data\AttributeInterfaceFactory
     */
    abstract protected function getAttributeFactory();

    /**
     * Entity attribute repository.
     *
     * @return mixed
     */
    abstract protected function getAttributeRepository();

    /**
     * Entity type code.
     *
     * @return string
     */
    abstract protected function getEntityTypeCode();
}

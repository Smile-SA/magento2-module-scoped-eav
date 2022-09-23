<?php

declare(strict_types=1);

namespace Smile\ScopedEav\Controller\Adminhtml\Attribute;

use Magento\Eav\Model\Config;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Registry;
use Smile\ScopedEav\Api\Data\AttributeInterface;

/**
 * Scoped entity attribute builder used in controllers.
 */
abstract class AbstractBuilder implements BuilderInterface
{
    private Config $eavConfig;

    private Registry $registry;

    /**
     * Constructor.
     *
     * @param Registry $registry  Registry.
     * @param Config   $eavConfig EAV configuration.
     */
    public function __construct(
        Registry $registry,
        Config $eavConfig
    ) {
        $this->eavConfig = $eavConfig;
        $this->registry  = $registry;
    }

    /**
     * @inheritDoc
     */
    public function build(RequestInterface $request): AttributeInterface
    {
        $attribute = $this->registry->registry('entity_attribute');

        if ($attribute === null) {
            $entityTypeId = $this->eavConfig->getEntityType($this->getEntityTypeCode())->getId();

            $attributeId = $request->getParam('attribute_id');
            $attributeCode = $request->getParam('attribute_code');

            $attribute = $this->getAttributeFactory()->create();
            $attribute->setEntityTypeId($entityTypeId);

            if ($attributeCode && $attributeId == null) {
                try {
                    $this->getAttributeRepository()->get($attributeCode);
                    throw new AlreadyExistsException(__('An attribute with the same code already exists.'));
                } catch (NoSuchEntityException $e) {
                    // Does nothing since no other attribute exists => attribute code is valid.
                    $e->getMessage();
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
     */
    abstract protected function getAttributeFactory(): mixed;

    /**
     * Entity attribute repository.
     */
    abstract protected function getAttributeRepository(): mixed;

    /**
     * Entity type code.
     */
    abstract protected function getEntityTypeCode(): string;
}

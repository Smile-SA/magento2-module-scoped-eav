<?php

declare(strict_types=1);

namespace Smile\ScopedEav\Model\Locator;

use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\Registry;
use Magento\Store\Api\Data\StoreInterface;
use Smile\ScopedEav\Api\Data\EntityInterface;

/**
 * Adminhtml entity locator implementation.
 */
class RegistryLocator implements LocatorInterface
{
    private Registry $registry;

    private EntityInterface $entity;

    private StoreInterface $store;

    /**
     * Constructor.
     *
     * @param Registry $registry Registry.
     */
    public function __construct(Registry $registry)
    {
        $this->registry = $registry;
    }

    /**
     * Get Entity
     *
     * @throws NotFoundException
     */
    public function getEntity(): EntityInterface
    {
        if (empty($this->entity)) {
            try {
                $this->entity = $this->registry->registry('current_entity');
            } catch (NotFoundException $e) {
                throw new NotFoundException(__('Entity was not registered'));
            }
        }

        return $this->entity;
    }

    /**
     * Get Store
     *
     * @throws NotFoundException
     */
    public function getStore(): StoreInterface
    {
        if (empty($this->store)) {
            try {
                $this->store = $this->registry->registry('current_store');
            } catch (NotFoundException $e) {
                throw new NotFoundException(__('Store was not registered'));
            }
        }

        return $this->store;
    }
}

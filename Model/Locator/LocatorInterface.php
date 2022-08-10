<?php

declare(strict_types=1);

namespace Smile\ScopedEav\Model\Locator;

/**
 * Adminhtml entity locator.
 */
interface LocatorInterface
{
    /**
     * Returns current entity.
     *
     * @return \Smile\ScopedEav\Api\Data\EntityInterface
     */
    public function getEntity();

    /**
     *
     * @return \Magento\Store\Api\Data\StoreInterface
     */
    public function getStore();
}

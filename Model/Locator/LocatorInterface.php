<?php

declare(strict_types=1);

namespace Smile\ScopedEav\Model\Locator;

use Magento\Store\Api\Data\StoreInterface;
use Smile\ScopedEav\Api\Data\EntityInterface;

/**
 * Adminhtml entity locator.
 */
interface LocatorInterface
{
    /**
     * Returns current entity.
     */
    public function getEntity(): EntityInterface;

    /**
     * Returns current store.
     */
    public function getStore(): StoreInterface;
}

<?php

declare(strict_types=1);

namespace Smile\ScopedEav\Controller\Adminhtml\Entity;

use Magento\Framework\App\RequestInterface;
use Smile\ScopedEav\Api\Data\EntityInterface;

/**
 * Scoped EAV entity builder interface.
 */
interface BuilderInterface
{
    /**
     * Retrieve and init an entity from the request.
     *
     * @param RequestInterface $request Request.
     *
     * @return EntityInterface|null
     */
    public function build(RequestInterface $request): ?EntityInterface;
}

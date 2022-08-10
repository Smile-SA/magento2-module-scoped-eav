<?php

declare(strict_types=1);

namespace Smile\ScopedEav\Controller\Adminhtml\Entity;

/**
 * Scoped EAV entity builder interface.
 */
interface BuilderInterface
{
    /**
     * Retrieve and init an entity from the request.
     *
     * @param \Magento\Framework\App\RequestInterface $request Request.
     *
     * @return \Smile\ScopedEav\Api\Data\EntityInterface
     */
    public function build(\Magento\Framework\App\RequestInterface $request);
}

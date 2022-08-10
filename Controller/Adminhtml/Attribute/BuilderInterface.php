<?php

declare(strict_types=1);

namespace Smile\ScopedEav\Controller\Adminhtml\Attribute;

/**
 * Scoped entity attribute builder interface used in controllers.
 */
interface BuilderInterface
{
    /**
     * Init attribute from request.
     *
     * @param RequestInterface $request Request.
     *
     * @return \Smile\ScopedEav\Api\Data\AttributeInterface
     */
    public function build(\Magento\Framework\App\RequestInterface $request);
}

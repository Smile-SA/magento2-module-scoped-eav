<?php

declare(strict_types=1);

namespace Smile\ScopedEav\Controller\Adminhtml\Attribute;

use Magento\Framework\App\RequestInterface;
use Smile\ScopedEav\Api\Data\AttributeInterface;

/**
 * Scoped entity attribute builder interface used in controllers.
 */
interface BuilderInterface
{
    /**
     * Init attribute from request.
     *
     * @param RequestInterface $request Request.
     */
    public function build(RequestInterface $request): AttributeInterface;
}

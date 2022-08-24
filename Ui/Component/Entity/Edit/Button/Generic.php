<?php

declare(strict_types=1);

namespace Smile\ScopedEav\Ui\Component\Entity\Edit\Button;

use Magento\Framework\Registry;
use Magento\Framework\View\Element\UiComponent\Context;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;
use Smile\ScopedEav\Api\Data\EntityInterface;

/**
 * Entity edit generic button.
 */
class Generic implements ButtonProviderInterface
{
    /**
     * @var Context
     */
    private $context;

    /**
     * Registry
     *
     * @var Registry
     */
    private $registry;

    /**
     * Constructor.
     *
     * @param Context $context  Context.
     * @param Registry                         $registry Registry.
     */
    public function __construct(
        Context $context,
        Registry $registry
    ) {
        $this->context = $context;
        $this->registry = $registry;
    }

    /**
     * Generate url by route and parameters
     *
     * @param string $route  URL route.
     * @param array  $params URL params.
     *
     * @return string
     */
    public function getUrl(string $route = '', array $params = []): string
    {
        return $this->context->getUrl($route, $params);
    }

    /**
     * Get current entity.
     *
     * @return EntityInterface
     */
    public function getEntity(): EntityInterface
    {
        return $this->registry->registry('current_entity');
    }

    /**
     * {@inheritdoc}
     */
    public function getButtonData()
    {
        return [];
    }
}

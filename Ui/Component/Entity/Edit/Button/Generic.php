<?php

declare(strict_types=1);

namespace Smile\ScopedEav\Ui\Component\Entity\Edit\Button;

/**
 * Entity edit generic button.
 */
class Generic implements \Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface
{
    /**
     * @var \Magento\Framework\View\Element\UiComponent\Context
     */
    private $context;

    /**
     * Registry
     *
     * @var \Magento\Framework\Registry
     */
    private $registry;

    /**
     * Constructor.
     *
     * @param \Magento\Framework\View\Element\UiComponent\Context $context  Context.
     * @param \Magento\Framework\Registry                         $registry Registry.
     */
    public function __construct(
        \Magento\Framework\View\Element\UiComponent\Context $context,
        \Magento\Framework\Registry $registry
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
    public function getUrl($route = '', $params = [])
    {
        return $this->context->getUrl($route, $params);
    }

    /**
     * Get current entity.
     *
     * @return \Smile\ScopedEav\Api\Data\EntityInterface
     */
    public function getEntity()
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

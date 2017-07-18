<?php
/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future.
 *
 * @category  Smile
 * @package   Smile\ScopedEav
 * @author    Aurelien FOUCRET <aurelien.foucret@smile.fr>
 * @copyright 2017 Smile
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Smile\ScopedEav\Ui\Component\Entity\Edit\Button;

/**
 * Entity edit generic button.
 *
 * @category Smile
 * @package  Smile\ScopedEav
 * @author   Aurelien FOUCRET <aurelien.foucret@smile.fr>
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

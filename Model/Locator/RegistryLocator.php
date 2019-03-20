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

namespace Smile\ScopedEav\Model\Locator;

use Magento\Framework\Exception\NotFoundException;

/**
 * Adminhtml entity locator implementation.
 *
 * @category Smile
 * @package  Smile\ScopedEav
 * @author   Aurelien FOUCRET <aurelien.foucret@smile.fr>
 */
class RegistryLocator implements LocatorInterface
{

    /**
     * @var \Magento\Framework\Registry
     */
    private $registry;

    /**
     *
     * @var \Smile\ScopedEav\Api\Data\EntityInterface
     */
    private $entity;

    /**
     *
     * @var \Magento\Store\Api\Data\StoreInterface
     */
    private $store;

    /**
     * Constructor.
     *
     * @param \Magento\Framework\Registry $registry Registry.
     */
    public function __construct(\Magento\Framework\Registry $registry)
    {
        $this->registry = $registry;
    }

    /**
     * {@inheritdoc}
     *
     * @throws NotFoundException
     */
    public function getEntity()
    {
        if (null === $this->entity) {
            $this->entity = $this->registry->registry('current_entity');
        }

        if (null === $this->entity) {
            throw new NotFoundException(__('Entity was not registered'));
        }

        return $this->entity;
    }

    /**
     * {@inheritdoc}
     *
     * @throws NotFoundException
     */
    public function getStore()
    {
        if (null === $this->store) {
            $this->store = $this->registry->registry('current_store');
        }

        if (null === $this->store) {
             throw new NotFoundException(__('Store was not registered'));
        }

        return $this->store;
    }
}

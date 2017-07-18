<?php
/**
 * DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade Smile Elastic Suite to newer
* versions in the future.
*
*
* @category  Smile
* @package   Smile\ScopedEav
* @author    Aurelien FOUCRET <aurelien.foucret@smile.fr>
* @copyright 2016 Smile
* @license   Open Software License ("OSL") v. 3.0
*/

namespace Smile\ScopedEav\Ui\DataProvider\Entity\Form;

/**
 * Scoped EAV generic dataprovider for edit form.
 *
 * @category Smile
 * @package  Smile\ScopedEav
 * @author   Aurelien FOUCRET <aurelien.foucret@smile.fr>
 */
class EntityDataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    /**
     *
     * @var \Magento\Ui\DataProvider\Modifier\PoolInterface
     */
    private $pool;

    /**
     * Constructor.
     *
     * @param string                                          $name             Source name.
     * @param string                                          $primaryFieldName Primary field name.
     * @param string                                          $requestFieldName Request field name.
     * @param \Magento\Ui\DataProvider\Modifier\PoolInterface $pool             Meta & data modifier pool.
     * @param array                                           $meta             Original meta.
     * @param array                                           $data             Original data.
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        \Magento\Ui\DataProvider\Modifier\PoolInterface $pool,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->pool       = $pool;
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        foreach ($this->pool->getModifiersInstances() as $modifier) {
            $this->data = $modifier->modifyData($this->data);
        }

        return $this->data;
    }

    /**
     * {@inheritdoc}
     */
    public function getMeta()
    {
        $meta = parent::getMeta();

        foreach ($this->pool->getModifiersInstances() as $modifier) {
            $meta = $modifier->modifyMeta($meta);
        }

        return $meta;
    }
}

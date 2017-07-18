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
namespace Smile\ScopedEav\Ui\DataProvider\Entity\Listing;

/**
 * Scoped EAV entity listing UI component dataprovider.
 *
 * @category Smile
 * @package  Smile\ScopedEav
 * @author   Aurelien FOUCRET <aurelien.foucret@smile.fr>
 */
class EntityDataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    /**
     *
     * @var \Magento\Ui\DataProvider\AddFieldToCollectionInterface[]
     */
    protected $addFieldStrategies;

    /**
     *
     * @var \Magento\Ui\DataProvider\AddFilterToCollectionInterface[]
     */
    protected $addFilterStrategies;

    /**
     * Constructor.
     *
     * @param string                                                    $name                Name.
     * @param string                                                    $primaryFieldName    Primary field name.
     * @param string                                                    $requestFieldName    Request field name.
     * @param \Magento\Ui\DataProvider\AddFieldToCollectionInterface[]  $addFieldStrategies  Field add stategies.
     * @param \Magento\Ui\DataProvider\AddFilterToCollectionInterface[] $addFilterStrategies Filter strategies.
     * @param array                                                     $meta                Meta.
     * @param array                                                     $data                Data.
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        array $addFieldStrategies = [],
        array $addFilterStrategies = [],
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);

        $this->addFieldStrategies  = $addFieldStrategies;
        $this->addFilterStrategies = $addFilterStrategies;
    }

    /**
     * {@inheritDoc}
     */
    public function getData()
    {
        if (!$this->getCollection()->isLoaded()) {
            $this->getCollection()->load();
        }

        $data = [
            'totalRecords' => $this->getCollection()->getSize(),
            'items'        => array_values($this->getCollection()->toArray()),
        ];

        return $data;
    }

    /**
     * {@inheritDoc}
     *
     * @SuppressWarnings(PHPMD.ElseExpression)
     */
    public function addField($field, $alias = null)
    {
        if (isset($this->addFieldStrategies[$field])) {
            $this->addFieldStrategies[$field]->addField($this->getCollection(), $field, $alias);
        } else {
            parent::addField($field, $alias);
        }
    }

    /**
     * {@inheritdoc}
     *
     * @SuppressWarnings(PHPMD.ElseExpression)
     */
    public function addFilter(\Magento\Framework\Api\Filter $filter)
    {
        if (isset($this->addFilterStrategies[$filter->getField()])) {
            $filterStrategy = $this->addFilterStrategies[$filter->getField()];
            $filterStrategy->addFilter($this->getCollection(), $filter->getField(), [$filter->getConditionType() => $filter->getValue()]);
        } else {
            parent::addFilter($filter);
        }
    }
}

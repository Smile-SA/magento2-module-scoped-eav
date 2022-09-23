<?php

declare(strict_types=1);

namespace Smile\ScopedEav\Ui\DataProvider\Entity\Listing;

use Magento\Ui\DataProvider\AbstractDataProvider;
use Magento\Ui\DataProvider\AddFieldToCollectionInterface;
use Magento\Ui\DataProvider\AddFilterToCollectionInterface;

/**
 * Scoped EAV entity listing UI component dataprovider.
 */
class EntityDataProvider extends AbstractDataProvider
{
    /**
     * @var AddFieldToCollectionInterface[]
     */
    protected array $addFieldStrategies;

    /**
     * @var AddFilterToCollectionInterface[]
     */
    protected array $addFilterStrategies;

    /**
     * Constructor.
     *
     * @param string  $name Name.
     * @param string $primaryFieldName Primary field name.
     * @param string $requestFieldName Request field name.
     * @param AddFieldToCollectionInterface[] $addFieldStrategies Field add stategies.
     * @param AddFilterToCollectionInterface[] $addFilterStrategies Filter strategies.
     * @param array $meta Meta.
     * @param array $data Data.
     */
    public function __construct(
        string $name,
        string $primaryFieldName,
        string $requestFieldName,
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

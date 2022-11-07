<?php

declare(strict_types=1);

namespace Smile\ScopedEav\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * Scoped EAV schema setup tools.
 */
class SchemaSetup
{
    private SchemaSetupInterface $setup;

    /**
     * Constructor.
     *
     * @param SchemaSetupInterface $setup Schema setup.
     */
    public function __construct(SchemaSetupInterface $setup)
    {
        $this->setup = $setup;
    }

    /**
     * Prepare an entity table with mandatory columns.
     *
     * @param string $entityTableName Entity table name.
     */
    public function getEntityTable(string $entityTableName): ?Table
    {
        $table = $this->setup->getConnection()->newTable($this->setup->getTable($entityTableName));

        $table->addColumn(
            'entity_id',
            Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Entity ID'
        )->addColumn(
            'attribute_set_id',
            Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false, 'default' => '0'],
            'Attribute Set ID'
        )->addColumn(
            'created_at',
            Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
            'Creation Time'
        )
        ->addColumn(
            'updated_at',
            Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => Table::TIMESTAMP_INIT_UPDATE],
            'Update Time'
        )
        ->addIndex($this->setup->getIdxName($entityTableName, ['attribute_set_id']), ['attribute_set_id'])
        ->addForeignKey(
            $this->setup->getFkName($entityTableName, 'attribute_set_id', 'eav_attribute_set', 'attribute_set_id'),
            'attribute_set_id',
            $this->setup->getTable('eav_attribute_set'),
            'attribute_set_id',
            Table::ACTION_CASCADE
        );

        return $table;
    }

    /**
     * Prepare an entity attribute table with mandatory columns.
     *
     * @param string $entityTableName Entity table.
     * @param string $valueType Value type.
     * @param int|string|null $size Value max size (null if unbounded).
     * @param string|null $tableSuffix Table suffix (if null $valueType is used).
     */
    public function getEntityAttributeValueTable(
        string $entityTableName,
        string $valueType,
        $size = null,
        ?string $tableSuffix = null
    ): ?Table {
        $tableName = sprintf("%s_%s", $entityTableName, $tableSuffix ?? $valueType);

        $table = $this->setup->getConnection()->newTable($this->setup->getTable($tableName))
            ->addColumn(
                'value_id',
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'nullable' => false, 'primary' => true],
                'Value ID'
            )
            ->addColumn(
                'attribute_id',
                Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'Attribute ID'
            )
            ->addColumn(
                'store_id',
                Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'Store ID'
            )
            ->addColumn(
                'entity_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'Entity ID'
            )
            ->addColumn(
                'value',
                $valueType,
                $size,
                [],
                'Value'
            )
            ->addIndex(
                $this->setup->getIdxName(
                    $tableName,
                    ['entity_id', 'attribute_id', 'store_id'],
                    \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
                ),
                ['entity_id', 'attribute_id', 'store_id'],
                ['type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE]
            )
            ->addIndex($this->setup->getIdxName($tableName, ['attribute_id']), ['attribute_id'])
            ->addIndex($this->setup->getIdxName($tableName, ['store_id']), ['store_id'])
            ->addForeignKey(
                $this->setup->getFkName($tableName, 'attribute_id', 'eav_attribute', 'attribute_id'),
                'attribute_id',
                $this->setup->getTable('eav_attribute'),
                'attribute_id',
                Table::ACTION_CASCADE
            )->addForeignKey(
                $this->setup->getFkName($tableName, 'entity_id', $entityTableName, 'entity_id'),
                'entity_id',
                $this->setup->getTable($entityTableName),
                'entity_id',
                Table::ACTION_CASCADE
            )->addForeignKey(
                $this->setup->getFkName($tableName, 'store_id', 'store', 'store_id'),
                'store_id',
                $this->setup->getTable('store'),
                'store_id',
                Table::ACTION_CASCADE
            );

        return $table;
    }

    /**
     * Prepare a website assigment table for an entity.
     *
     * @param string $entityTableName Entity table name.
     */
    public function getEntityWebsiteTable(string $entityTableName): self
    {
        $websiteTableName = sprintf('%s_website', $entityTableName);

        /** @var SchemaSetup $table */
        $table = $this->setup->getConnection()->newTable($this->setup->getTable($websiteTableName))
            ->addColumn(
                'entity_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'primary' => true],
                'Entity ID'
            )
            ->addColumn(
                'website_id',
                Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false, 'primary' => true],
                'Website ID'
            )
            ->addIndex($this->setup->getIdxName($websiteTableName, ['website_id']), ['website_id'])
            ->addForeignKey(
                $this->setup->getFkName($websiteTableName, 'website_id', 'store_website', 'website_id'),
                'website_id',
                $this->setup->getTable('store_website'),
                'website_id',
                Table::ACTION_CASCADE
            )
            ->addForeignKey(
                $this->setup->getFkName($websiteTableName, 'entity_id', $entityTableName, 'entity_id'),
                'entity_id',
                $this->setup->getTable($entityTableName),
                'entity_id',
                Table::ACTION_CASCADE
            );

        return $table;
    }

    /**
     * Prepare an additional attribute config table for an entity.
     *
     * @param string $entityTableName Entity table name.
     */
    public function getAttributeAdditionalTable(string $entityTableName): ?Table
    {
        $additionalTableName = sprintf('%s_eav_attribute', $entityTableName);

        $table = $this->setup->getConnection()->newTable($this->setup->getTable($additionalTableName))
            ->addColumn(
                'attribute_id',
                Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false, 'primary' => true],
                'Attribute ID'
            )
            ->addForeignKey(
                $this->setup->getFkName($additionalTableName, 'attribute_id', 'eav_attribute', 'attribute_id'),
                'attribute_id',
                $this->setup->getTable('eav_attribute'),
                'attribute_id',
                Table::ACTION_CASCADE
            );

        return $table;
    }
}

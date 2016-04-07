<?php

namespace i2c\SupplierBundle\Migrations\Schema\v1_1;

use Doctrine\DBAL\Schema\Schema;
use Oro\Bundle\EntityBundle\EntityConfig\DatagridScope;
use Oro\Bundle\EntityExtendBundle\EntityConfig\ExtendScope;
use Oro\Bundle\MigrationBundle\Migration\Migration;
use Oro\Bundle\MigrationBundle\Migration\QueryBag;

/**
 * Class AddIsNewBooleanMigration
 *
 * @package i2c\SupplierBundle\Migrations\Schema\v1_1
 */
class AddIsNewBooleanMigration implements Migration
{
    /**
     * @param Schema   $schema
     * @param QueryBag $queries
     *
     * @throws \Doctrine\DBAL\Schema\SchemaException
     */
    public function up(Schema $schema, QueryBag $queries)
    {
        $table = $schema->getTable('oro_business_unit');
        $table->addColumn(
            'is_new_supplier',
            'boolean',
            [
                'not_null'    => true,
                'default'     => true,
                'oro_options' => [
                    'extend'   => [
                        'is_extend' => true,
                        'owner'     => ExtendScope::OWNER_CUSTOM,
                    ],
                    'datagrid' => [
                        'is_visible'  => DatagridScope::IS_VISIBLE_MANDATORY,
                        'filterable'  => true,
                        'show_filter' => true,
                        'sortable'    => true,
                    ],
                    'merge'    => [
                        'display' => true,
                    ],
                    'entity'   => [
                        'label'       => 'New?',
                        'description' => 'Indicates if a supplier was freshly imported',
                    ],
                    'form'     => [
                        'is_enabled' => 1,
                    ],
                    'view'     => [
                        'is_displayable' => 1,
                    ],
                ],
            ]
        );
    }
}

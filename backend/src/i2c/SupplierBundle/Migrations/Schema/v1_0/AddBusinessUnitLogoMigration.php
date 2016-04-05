<?php

namespace i2c\SetupBundle\Migrations\Schema;

use Doctrine\DBAL\Schema\Schema;
use Oro\Bundle\AttachmentBundle\Migration\Extension\AttachmentExtension;
use Oro\Bundle\AttachmentBundle\Migration\Extension\AttachmentExtensionAwareInterface;
use Oro\Bundle\EntityExtendBundle\EntityConfig\ExtendScope;
use Oro\Bundle\MigrationBundle\Migration\Migration;
use Oro\Bundle\MigrationBundle\Migration\QueryBag;

/**
 * Class AddBusinessUnitLogoMigration
 *
 * @package i2c\SetupBundle\Migrations\Schema
 */
class AddBusinessUnitLogoMigration implements Migration, AttachmentExtensionAwareInterface
{
    /** @var  AttachmentExtension */
    protected $attachmentExtension;

    /**
     * Sets the AttachmentExtension
     *
     * @param AttachmentExtension $attachmentExtension
     */
    public function setAttachmentExtension(AttachmentExtension $attachmentExtension)
    {
        $this->attachmentExtension = $attachmentExtension;
    }

    /**
     * Modifies the given schema to apply necessary changes of a database
     * The given query bag can be used to apply additional SQL queries before and after schema changes
     *
     * @param Schema   $schema
     * @param QueryBag $queries
     * @return void
     */
    public function up(Schema $schema, QueryBag $queries)
    {
        $this->attachmentExtension->addImageRelation(
            $schema,
            'oro_business_unit',
            'supplier_logo',
            [
                'extend' => [
                    'origin' => ExtendScope::ORIGIN_CUSTOM,
                    'owner' => ExtendScope::OWNER_CUSTOM,
                    'state' => ExtendScope::STATE_ACTIVE
                ]
            ],
            10,
            100,
            100
        );
    }
}

<?php
namespace CleverAge\Bundle\ConnectAsBundle\Migrations\Schema;

use CleverAge\Bundle\ConnectAsBundle\Migrations\CleverConnectAsInstaller;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Schema\SchemaException;
use Oro\Bundle\MigrationBundle\Migration\Migration;
use Oro\Bundle\MigrationBundle\Migration\QueryBag;

/**
 * Class CleverAgeConnectAsBundle
 * @package CleverAge\Bundle\ConnectAsBundle\Migrations\Schema
 */
class CleverAgeConnectAsBundle implements Migration
{
    /**
     * @param Schema $schema
     * @param QueryBag $queries
     * @throws SchemaException
     */
    public function up(Schema $schema, QueryBag $queries)
    {
        // Create table to store token
        $table = $schema->createTable(CleverConnectAsInstaller::TABLE_NAME);
        $table->addColumn('id', 'integer', ['autoincrement' => true]);
        $table->addColumn('token', 'string', ['length' => 255]);
        $table->addColumn('customer_user_id', 'integer');
        $table->setPrimaryKey(['id']);
        $table->addForeignKeyConstraint(
            $schema->getTable('oro_customer_user'),
            ['customer_user_id'],
            ['id']
        );
        $table->addIndex(['customer_user_id']);
    }
}

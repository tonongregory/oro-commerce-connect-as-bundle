<?php
namespace CleverAge\Bundle\ConnectAsBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Schema\SchemaException;
use Oro\Bundle\MigrationBundle\Migration\Installation;
use Oro\Bundle\MigrationBundle\Migration\QueryBag;

/**
 * Class CleverConnectAsInstaller
 * @package CleverAge\Bundle\ConnectAsBundle\Migrations
 */
class CleverConnectAsInstaller implements Installation
{
    const TABLE_NAME = 'cleverage_connect_as_token';

    /**
     * {@inheritdoc}
     */
    public function getMigrationVersion()
    {
        return 'v1_0';
    }

    /**
     * @param Schema $schema
     * @param QueryBag $queries
     * @throws SchemaException
     */
    public function up(Schema $schema, QueryBag $queries)
    {
        // Create table to store token
        $table = $schema->createTable(self::TABLE_NAME);
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

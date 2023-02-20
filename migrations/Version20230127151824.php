<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Types\Types;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

// https://www.doctrine-project.org/projects/doctrine-migrations/en/stable/index.html

final class Version20230127151824 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $users = $schema->createTable('users');

        $users->addColumn('id', Types::INTEGER)->setAutoincrement(true);
        $users->addColumn('user_name', Types::STRING);
        $users->addColumn('created_at', Types::DATETIME_MUTABLE);

        $users->setPrimaryKey(['id']);
    }

    public function down(Schema $schema): void
    {
        $schema->dropTable('users');
    }
}

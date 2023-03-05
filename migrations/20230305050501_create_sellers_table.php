<?php

declare(strict_types=1);

use Phoenix\Migration\AbstractMigration;

final class CreateSellersTable extends AbstractMigration
{
    protected function up(): void
    {
        $this->execute('CREATE TABLE `sellers` (
                `id` int(11) PRIMARY KEY NOT NULL AUTO_INCREMENT,
                `name` varchar(255) NOT NULL,
                `email` varchar(255) NOT NULL,
                `phone` varchar(255) NOT NULL,
                `about` varchar(255) NOT NULL,
                `profile_img` varchar(255) NOT NULL,
                `created_at` datetime NOT NULL,
                `updated_at` datetime NOT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        ');
    }

    protected function down(): void
    {
        $this->table('sellers')->drop();
    }
}

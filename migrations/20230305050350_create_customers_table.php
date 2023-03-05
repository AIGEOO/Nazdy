<?php

declare(strict_types=1);

use Phoenix\Migration\AbstractMigration;

final class CreateCustomersTable extends AbstractMigration
{
    protected function up(): void
    {
        $this->execute(' CREATE TABLE `customers` (
                `id` int(11) PRIMARY KEY NOT NULL AUTO_INCREMENT,
                `name` varchar(255) NOT NULL,
                `email` varchar(255) NOT NULL,
                `password` varchar(255) NOT NULL,
                `favorites` varchar(255) NOT NULL,
                `role` varchar(255) NOT NULL,
                `profile_img` varchar(255) NOT NULL,
                `created_at` datetime NOT NULL,
                `updated_at` datetime NOT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        ');
    }

    protected function down(): void
    {
        $this->table('customers')->drop();
    }
}

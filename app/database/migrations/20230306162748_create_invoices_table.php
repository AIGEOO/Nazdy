<?php

declare(strict_types=1);

use Phoenix\Migration\AbstractMigration;

final class CreateInvoicesTable extends AbstractMigration
{
    protected function up(): void
    {
        $this->execute('CREATE TABLE `invoices` (
                `id` int(11) PRIMARY KEY NOT NULL AUTO_INCREMENT,
                `user_id` int(11) NOT NULL,
                `products_info` text NOT NULL COMMENT "names & prices & quantities in json",
                `total` float NOT NULL,
                `created_at` datetime NOT NULL,
                `updated_at` datetime NOT NULL,
                CONSTRAINT `fk_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        ');
    }

    protected function down(): void
    {
        $this->table('invoices')->drop();
    }
}

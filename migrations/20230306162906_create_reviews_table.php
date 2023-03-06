<?php

declare(strict_types=1);

use Phoenix\Migration\AbstractMigration;

final class CreateReviewsTable extends AbstractMigration
{
    protected function up(): void
    {
        $this->execute('CREATE TABLE `reviews` (
                `id` int(11) PRIMARY KEY NOT NULL AUTO_INCREMENT,
                `user_id` int(11) NOT NULL,
                `product_id` int(11) NOT NULL,
                `rate` int(11) NOT NULL,
                `text` text NOT NULL,
                `created_at` datetime NOT NULL,
                `updated_at` datetime NOT NULL,
                CONSTRAINT `fk_user_id_` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
                CONSTRAINT `fk_product_id` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        ');
    }

    protected function down(): void
    {
        $this->table('reviews')->drop();
    }
}

<?php

declare(strict_types=1);

use Phoenix\Migration\AbstractMigration;

final class CreateProductsTable extends AbstractMigration
{
    protected function up(): void
    {
        $this->execute('CREATE TABLE `products` (
                `id` INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
                `seller_id` INT NOT NULL,
                `name` VARCHAR(255) NOT NULL,
                `price` FLOAT NOT NULL,
                `category` VARCHAR(255) NOT NULL,
                `availability` VARCHAR(255) NOT NULL,
                `description` TEXT NOT NULL,
                `width` INT(11) NOT NULL,
                `height` INT(11) NOT NULL,
                `weight` INT(11) NOT NULL,
                `quantity` INT(11) NOT NULL,
                `image` VARCHAR(255) NOT NULL,
                `created_at` DATETIME NOT NULL,
                `updated_at` DATETIME NOT NULL,
                CONSTRAINT `fk_seller_id` FOREIGN KEY (`seller_id`) REFERENCES `sellers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        ');
    }

    protected function down(): void
    {
        $this->table('products')->drop();
    }
}

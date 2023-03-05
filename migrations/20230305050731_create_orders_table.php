<?php

declare(strict_types=1);

use Phoenix\Migration\AbstractMigration;

final class CreateOrdersTable extends AbstractMigration
{
    protected function up(): void
    {
        $this->execute('CREATE TABLE `orders` (
                `id` int(11) PRIMARY KEY NOT NULL AUTO_INCREMENT,
                `customer_id` int(11) NOT NULL,
                `product_id` int(11) NOT NULL,
                `quantity` int(11) NOT NULL,
                `total` float NOT NULL,
                `street` varchar(255) NOT NULL,
                `city` varchar(255) NOT NULL,
                `country` varchar(255) NOT NULL,
                `postcode` varchar(255) NOT NULL,
                `status` varchar(255) NOT NULL,
                `shipping_cost` float NOT NULL,
                `created_at` datetime NOT NULL,
                `updated_at` datetime NOT NULL,
                CONSTRAINT `fk_customer_id__` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
                CONSTRAINT `fk_product_id_` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;            
        ');
    }

    protected function down(): void
    {
        $this->table('orders')->drop();
    }
}

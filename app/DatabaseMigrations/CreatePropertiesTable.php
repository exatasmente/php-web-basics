<?php

namespace App\DatabaseMigrations;

class CreatePropertiesTable extends Migration implements MigrationInterface
{
    public function up()
    {
        $query = "
            CREATE TABLE `properties` (
                `id` INT NOT NULL AUTO_INCREMENT,
                `addr_line1` VARCHAR(191) NOT NULL,
                `addr_line2` VARCHAR(191) NOT NULL,
                `addr_city` VARCHAR(191) NOT NULL,
                `addr_state` VARCHAR(191) NOT NULL,
                `addr_neighbourhood` VARCHAR(191) NOT NULL,
                `addr_number` VARCHAR(191) NOT NULL,
                `addr_zipcode` VARCHAR(191) NOT NULL,
                `property_owner_id` INT NOT NULL,
                `created_at` DATETIME NOT NULL,
                `updated_at` DATETIME NOT NULL,
                PRIMARY KEY (`id`),
                FOREIGN KEY (`property_owner_id`) REFERENCES property_owners(`id`)
            );
        ";

        return static::raw($query);
    }

    public function down()
    {
        $query = 'DROP TABLE IF EXISTS  `properties`;';

        return static::raw($query);
    }

    public function getStep()
    {
        return 3;
    }
}
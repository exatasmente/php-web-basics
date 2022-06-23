<?php

namespace App\DatabaseMigrations;

class CreatePropertyOwnersTable extends Migration implements MigrationInterface
{

    public function up()
    {
        $query = "
            CREATE TABLE `property_owners` (
                `id` INT NOT NULL AUTO_INCREMENT,
                `name` VARCHAR(191) NOT NULL,
                `email` VARCHAR(191) NOT NULL,
                `phone_number` VARCHAR(21) NOT NULL,
                `payment_day` INT NOT NULL,
                `created_at` DATETIME NOT NULL,
                `updated_at` DATETIME NOT NULL,
                PRIMARY KEY (`id`)
            );
        ";

        return static::raw($query);
    }

    public function down()
    {
        $query = 'DROP TABLE IF EXISTS  `property_owners`;';

        return static::raw($query);
    }

    public function getStep()
    {
        return 2;
    }
}
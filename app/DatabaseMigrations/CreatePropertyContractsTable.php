<?php

namespace App\DatabaseMigrations;

class CreatePropertyContractsTable extends Migration implements MigrationInterface
{
    public function up()
    {

        $query = "
            CREATE TABLE `property_contracts` (
                `id` INT NOT NULL AUTO_INCREMENT,
                `starts_at` DATE NOT NULL,
                `ends_at` DATE NOT NULL,
                `administration_fee` INT NOT NULL,
                `rent_amount` INT NOT NULL,
                `condo_amount` INT NOT NULL,
                `iptu_amount` INT NOT NULL,
                `property_id` INT NOT NULL,
                `tenant_id` INT NOT NULL,
                `created_at` DATETIME NOT NULL,
                `updated_at` DATETIME NOT NULL,
                PRIMARY KEY (`id`),
                FOREIGN KEY (`property_id`) REFERENCES properties(`id`),
                FOREIGN KEY (`tenant_id`) REFERENCES tenants(`id`)
            );
        ";

        return static::raw($query);
    }

    public function down()
    {
        $query = 'DROP TABLE IF EXISTS  `property_contracts`;';

        return static::raw($query);
    }

    public function getStep()
    {
        return 4;
    }
}
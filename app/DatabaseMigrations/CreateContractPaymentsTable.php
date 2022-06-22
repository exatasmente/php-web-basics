<?php

namespace App\DatabaseMigrations;

class CreateContractPaymentsTable extends Migration implements MigrationInterface
{
    public function up()
    {

        $query = "
            CREATE TABLE `contract_payments` (
                `id` INT NOT NULL AUTO_INCREMENT,
                `cycle` INT NOT NULL,
                `starts_at` DATE NOT NULL,
                `ends_at` DATE NOT NULL,
                `status` VARCHAR(21) NOT NULL,
                `notes` VARCHAR(191) NOT NULL,
                `amount` INT NOT NULL,
                `property_contract_id` INT NOT NULL,
                `created_at` DATETIME NOT NULL,
                `updated_at` DATETIME NOT NULL,
                PRIMARY KEY (`id`),
                FOREIGN KEY (`property_contract_id`) REFERENCES property_contracts(`id`)
            );
        ";

        return static::raw($query);
    }

    public function down()
    {
        $query = 'DROP TABLE IF EXISTS  `contract_payments`;';

        return static::raw($query);
    }

    public function getStep()
    {
        return 5;
    }
}
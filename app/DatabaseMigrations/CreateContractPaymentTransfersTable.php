<?php

namespace App\DatabaseMigrations;

class CreateContractPaymentTransfersTable extends Migration implements MigrationInterface
{

    public function up()
    {

        $query = "
            CREATE TABLE `contract_payment_transfers` (
                `id` INT NOT NULL AUTO_INCREMENT,
                `cycle` INT NOT NULL,
                `starts_at` DATE NOT NULL,
                `ends_at` DATE NOT NULL,
                `status` VARCHAR(21) NOT NULL,
                `notes` VARCHAR(191) NOT NULL,
                `amount` INT NOT NULL,
                `contract_payment_id` INT NOT NULL,
                `created_at` DATETIME NOT NULL,
                `updated_at` DATETIME NOT NULL,
                PRIMARY KEY (`id`),
                FOREIGN KEY (`contract_payment_id`) REFERENCES contract_payments(`id`)
            );
        ";

        return static::raw($query);
    }

    public function down()
    {
        $query = 'DROP TABLE IF EXISTS  `contract_payment_transfers`;';

        return static::raw($query);
    }

    public function getStep()
    {
        return 6;
    }
}
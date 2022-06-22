<?php
namespace App\DatabaseMigrations;

class CreatePropertyOwnersTable extends Migration {


    public function up()
    {
        $query = "
            CREATE TABLE `tenants` (
            `id` INT NOT NULL AUTO_INCREMENT,
                `name` VARCHAR(191) NOT NULL,
                `email` VARCHAR(191) NOT NULL,
                `phone_number` VARCHAR(21) NOT NULL,
                PRIMARY KEY (`id`)
            );
        ";

        return $this->connection->exec($query);
    }
}
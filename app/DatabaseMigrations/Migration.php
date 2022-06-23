<?php

namespace App\DatabaseMigrations;

use App\Models\Model;

class Migration extends Model
{
    public static string $tableName = 'migrations';

    public $name;
    public $step;


    public function up()
    {
        $query = "
            CREATE TABLE `migrations` (
                `id` INT NOT NULL AUTO_INCREMENT,
                `name` VARCHAR(191) NOT NULL,
                `step` INT NOT NULL,
                `created_at` DATETIME NOT NULL,
                `updated_at` DATETIME NOT NULL,
                PRIMARY KEY (`id`)
            );
        ";

        return static::raw($query);
    }
}
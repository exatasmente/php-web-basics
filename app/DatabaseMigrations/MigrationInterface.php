<?php

namespace App\DatabaseMigrations;

interface MigrationInterface
{
    public function up();
    public function down();
    public function getStep();

}
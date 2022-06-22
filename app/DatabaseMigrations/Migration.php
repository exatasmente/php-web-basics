<?php

namespace App\DatabaseMigrations;

class Migration
{
    protected $connection;

    public function __construct($connection)
    {
        $this->connection = $connection;
    }
}
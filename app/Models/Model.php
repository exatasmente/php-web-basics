<?php

namespace App\Models;
class Model extends AbstractModel
{
    public static string $tableName;
    public $id = null;

    public function getId()
    {
        return $this->id;
    }

    public function toArray(): array
    {
        return json_decode($this->toJson(), true);
    }

    public function toJson(): string
    {
        return json_encode($this);
    }
}
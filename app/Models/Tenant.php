<?php

namespace App\Models;

class Tenant extends Model
{
    public static string $tableName = 'tenants';

    public string $name;
    public string $email;
    public string $phone_number;

}
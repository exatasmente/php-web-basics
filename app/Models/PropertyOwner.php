<?php
namespace App\Models;
class PropertyOwner extends Model
{
    public static string $tableName = 'property_owners';


    public $name;
    public $email;
    public $phone_number;
    public $payment_day;
}
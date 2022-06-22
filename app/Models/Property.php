<?php
namespace App\Models;
use App\Models\Traits\HasPropertyOwner;

class Property extends Model
{
    use HasPropertyOwner;

    public static string $tableName = 'properties';

    public $addr_line1;
    public $addr_line2;
    public $addr_city;
    public $addr_state;
    public $addr_neighbourhood;
    public $addr_number;
    public $addr_zipcode;
    public $property_owner_id;



}
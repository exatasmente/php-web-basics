<?php
namespace App\Models;
use App\Models\Traits\HasProperty;
use App\Models\Traits\HasPropertyOwner;
use App\Models\Traits\HasTenant;

class PropertyContract extends Model
{
    use HasTenant;
    use HasProperty;

    public static string $tableName = 'property_contracts';

    public $starts_at;
    public $ends_at;
    public string $administration_fee;
    public int $rent_amount;
    public int $condo_amount;
    public int $iptu_amount;

    public $property_id;
    public $tenant_id;

    protected $tenant;
    protected $property;

}
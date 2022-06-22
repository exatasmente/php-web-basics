<?php
namespace App\Models;
use App\Models\Traits\HasProperty;
use App\Models\Traits\HasPropertyOwner;
use App\Models\Traits\HasTenant;

class PropertyContract extends Model
{
    use HasPropertyOwner;
    use HasTenant;
    use HasProperty;

    public static string $tableName = 'property_contracts';

    public $starts_at;
    public $ends_at;
    public $adminstration_fee;
    public $rent_amount;
    public $condo_amount;
    public $iptu_amount;

    public $property_id;
    public $property_owner_id;
    public $tenant_id;

    protected $tenant;
    protected $property_owner;
    protected $property;

}
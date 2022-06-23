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
    public int $administration_fee;
    public int $rent_amount;
    public int $condo_amount;
    public int $iptu_amount;

    public $property_id;
    public $tenant_id;

    protected $tenant;
    protected $property;

    public function getRentTotalAmount()
    {
        return $this->rent_amount + $this->getExtrasTotalAmount();
    }

    public function getExtrasTotalAmount()
    {
        return $this->condo_amount + $this->iptu_amount;
    }

    public function getTransferAmountFor($amount)
    {
        return ($this->administration_fee / 100) * $amount;
    }

    public function getTransferAmount()
    {
        return $this->getTransferAmountFor($this->getRentTotalAmount() - $this->condo_amount);
    }

}
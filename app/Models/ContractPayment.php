<?php
namespace App\Models;
use App\Models\Traits\HasPropertyContract;

class ContractPayment extends Model
{
    use HasPropertyContract;

    public static string $tableName = 'contract_payments';

    const STATUS_PAID = 'paid';
    const STATUS_PENDING = 'pending';

    public $cycle;
    public $starts_at;
    public $ends_at;
    public $status;
    public $notes;
    public $amount;
    public $property_contract_id;

    public static function isValidStatus(string $status)
    {
        return in_array($status,[self::STATUS_PENDING, self::STATUS_PAID]);
    }

    public function getTransferAmount()
    {
        $propertyContract = $this->getPropertyContract();

        return $propertyContract->getTransferAmountFor($this->amount);
    }


}
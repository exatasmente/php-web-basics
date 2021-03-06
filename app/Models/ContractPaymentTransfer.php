<?php

namespace App\Models;

use App\Models\Traits\HasContractPayment;

class ContractPaymentTransfer extends Model
{
    use HasContractPayment;

    const STATUS_TRANSFERRED = 'transferred';
    const STATUS_PENDING = 'pending';
    public static string $tableName = 'contract_payment_transfers';
    public $cycle;
    public $starts_at;
    public $ends_at;
    public $status;
    public $notes;
    public $amount;
    public $contract_payment_id;

    public static function isValidStatus(string $status)
    {
        return in_array($status, [self::STATUS_PENDING, self::STATUS_TRANSFERRED]);
    }
}
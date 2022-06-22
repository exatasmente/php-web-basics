<?php

namespace App\Models;

use App\Models\Traits\HasContractPayment;

class ContractPaymentTransfer
{
    use HasContractPayment;

    public static string $tableName = 'contract_payment_transfers';

    const STATUS_PAID = 'paid';
    const STATUS_PENDING = 'pending';

    public $cycle;
    public $starts_at;
    public $ends_at;
    public $status;
    public $notes;
    public $amount;
    public $contract_payment_id;

    protected $contract_payment;
}
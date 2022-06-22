<?php

namespace App\Models\Traits;

use App\Models\ContractPayment;

trait HasContractPayment
{
    public function getContractPayment()
    {
        if (!$this->contract_payment) {
            $this->contract_payment = ContractPayment::find(['id' => $this->contract_payment_id], 1);
        }

        return $this->contract_payment;
    }
}
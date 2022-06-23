<?php

namespace App\Models\Traits;

use App\Models\PropertyContract;

trait HasPropertyContract
{
    public function getPropertyContract(): PropertyContract
    {
        if (!isset($this->property_contract) || !$this->property_contract) {
            $this->property_contract = PropertyContract::find(['id' => $this->property_contract_id], 1);
        }

        return $this->property_contract;
    }

    public function setPropertyContract(PropertyContract $propertyContract)
    {
        $this->property_contract = $propertyContract;
    }
}
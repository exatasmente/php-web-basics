<?php

namespace App\Requests;

use App\Exceptions\BusinessException;
use App\Exceptions\HttpValidationException;
use App\Models\Property;
use App\Models\PropertyContract;
use App\Models\Tenant;
use App\Response;
use Cassandra\Exception\ValidationException;

class StorePropertyContractRequest extends Request
{

    public function getData()
    {
        return [
            'property_id' => (int) $this->getRouteParam('id'),
            'tenant_id' => $this->getAttribute('tenant_id'),
            'starts_at' => $this->getAttribute('starts_at'),
            'ends_at' => $this->getAttribute('ends_at'),
            'administration_fee' => $this->getAttribute('administration_fee'),
            'rent_amount' => $this->getAttribute('rent_amount'),
            'condo_amount' => $this->getAttribute('condo_amount'),
            'iptu_amount' => $this->getAttribute('iptu_amount'),
        ];
    }

    /**
     * @throws \ReflectionException
     * @throws HttpValidationException
     */
    public function validateRequest()
    {

        $exists = PropertyContract::find($this->getData(), 1);

        if ($exists) {
            throw new HttpValidationException('Unable to save PropertyContract, already exists an PropertyContract with the same property, tenant, property owner, start date, end date, administration fee, rend amount, condo amount and iptu amount', 422);
        }
        
    }
}
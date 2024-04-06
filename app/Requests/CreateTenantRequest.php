<?php

namespace App\Requests;

use App\Exceptions\HttpValidationException;
use App\Models\Tenant;

class CreateTenantRequest extends Request
{

    /**
     * @throws \ReflectionException
     * @throws HttpValidationException
     */
    public function validateRequest()
    {
        $data = $this->getData();

        foreach ($data as $key => $value) {
            echo $key . ' ' . $value;
            if (empty($value)) {
                throw new HttpValidationException("{$key} is required", 422);
            }
        }
        
        $exists = Tenant::find($data, 1);

        if ($exists) {
            throw new HttpValidationException('Unable to save Tenant, already exists an Tenant with the same email', 422);
        }

    }

    public function getData()
    {
        return [
            'email' => $this->getAttribute('email'),
            'phone_number' => $this->getAttribute('phone_number'),
            'name' => $this->getAttribute('name'),
        ];
    }
}
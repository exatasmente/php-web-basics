<?php

namespace App\Models\Traits;

use App\Models\PropertyOwner;

trait HasPropertyOwner
{

    public function getPropertyOwner()
    {
        if (!$this->property_owner) {
            $this->property_owner = PropertyOwner::find(['id' => $this->property_owner_id], 1);
        }

        return $this->property_owner;
    }
}
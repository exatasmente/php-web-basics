<?php

namespace App\Models\Traits;

use App\Models\Property;

trait HasProperty
{

    public function getProperty()
    {
        if (!$this->property) {
            $this->property = Property::find(['id' => $this->property_id], 1);
        }

        return $this->property;
    }
}
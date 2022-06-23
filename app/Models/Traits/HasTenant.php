<?php

namespace App\Models\Traits;

use App\Models\Tenant;

trait HasTenant
{
    public function getTenant()
    {
        if (!isset($this->tenant) || !$this->tenant) {
            $this->tenant = Tenant::find(['id' => $this->tenant_id], 1);
        }

        return $this->tenant;
    }
}
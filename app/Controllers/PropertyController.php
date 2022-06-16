<?php

namespace App\Controllers;

use App\Controllers\Contracts\BaseController;
use App\Requests\Request;

class PropertyController extends BaseController
{

    public function getOwnerProperties(Request $request, $id)
    {
        echo  json_encode($request->getAttributes()) . ' ' . $id;
    }

    public function getOwnerProperty(Request $request, $ownerId, $propertyId)
    {
        echo  json_encode($request->getAttributes()) . ' ' . $ownerId . ' ' . $propertyId;
    }
}
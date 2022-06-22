<?php

namespace App\Controllers;

use App\Controllers\Contracts\BaseController;
use App\Models\PropertyOwner;
use App\Requests\Request;
use App\Response;

class PropertyOwnerController extends BaseController
{
    public function all(Request $request)
    {
        $propertyOwners = PropertyOwner::find();

        if (!$propertyOwners) {
            return Response::json([])->send();
        }

        $propertyOwners = array_map(function ($propertyOwner) {
            return $propertyOwner->toArray();
        }, $propertyOwners);

        return Response::json($propertyOwners)->send();

    }

    /**
     * @param Request $request
     * @param $id
     * @return Response
     * @throws \ReflectionException
     */
    public function show(Request $request, $id): Response
    {

        $PropertyOwner = PropertyOwner::find(['id' => intval($id)], 1);

        if ($PropertyOwner) {
            return Response::json($PropertyOwner->toArray())->send();
        }

        return Response::json(['message' => 'PropertyOwner not found'], 404)->send();
    }

    public function store(Request $request)
    {
        $data = [
            'name' => $request->getAttribute('name'),
            'email' => $request->getAttribute('email'),
            'phone_number' => $request->getAttribute('phone_number'),
            'payment_day' => $request->getAttribute('payment_day'),
        ];

        $exist = PropertyOwner::find($data, 1);

        if ($exist) {
            return Response::json(['message' => 'Unable to save PropertyOwner, already exists an PropertyOwner with the same name, email, phone_number and payment_day'], 422)->send();
        }

        $propertyOwner = new PropertyOwner();
        $propertyOwner->name = $request->getAttribute('name');
        $propertyOwner->email = $request->getAttribute('email');
        $propertyOwner->payment_day = $request->getAttribute('payment_day');
        $propertyOwner->phone_number = $request->getAttribute('phone_number');
        $propertyOwner->save();

        return Response::json($propertyOwner->toArray(), 201)->send();

    }

    public function update(Request $request, $id)
    {
        $propertyOwner = PropertyOwner::find(['id' => intval($id)], 1);

        if (!$propertyOwner) {
            return Response::json(['message' => 'PropertyOwner not found'], 404)->send();
        }

        $propertyOwner->name = $request->getAttribute('name', $propertyOwner->name);
        $propertyOwner->email = $request->getAttribute('email', $propertyOwner->email);
        $propertyOwner->payment_day = $request->getAttribute('payment_day', $propertyOwner->payment_day);
        $propertyOwner->phone_number = $request->getAttribute('phone_number', $propertyOwner->phone_number);
        $propertyOwner->save();

        return Response::json($propertyOwner->toArray())->send();

    }

    public function delete(Request $request, $id)
    {
        /** @var PropertyOwner|null $propertyOwner */
        $propertyOwner = PropertyOwner::find(['id' => intval($id)], 1);

        if (!$propertyOwner) {
            return Response::json(['message' => 'PropertyOwner not found'], 404)->send();
        }

        $propertyOwner->delete();

        return Response::json($propertyOwner->toArray())->send();
    }
}
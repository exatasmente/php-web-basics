<?php

namespace App\Controllers;

use App\Controllers\Contracts\BaseController;
use App\Models\ContractPayment;
use App\Models\Property;
use App\Models\PropertyContract;
use App\Requests\Request;
use App\Response;

class PropertyController extends BaseController
{

    public function all(Request $request)
    {
        $properties = Property::find();

        if (!$properties) {
            return Response::json([])->send();
        }

        $properties = array_map(function ($property) {
            return $property->toArray();
        }, $properties);

        return Response::json($properties)->send();

    }

    /**
     * @param Request $request
     * @param $id
     * @return Response
     * @throws \ReflectionException
     */
    public function show(Request $request, $id): Response
    {
        /** @var Property $property */
        $property = Property::find(['id' => intval($id)], 1);

        if ($property) {

            return Response::json($property->toArray())->send();
        }

        return Response::json(['message' => 'Property not found'], 404)->send();
    }

    public function store(Request $request)
    {
        $data = [
            'addr_line1' => $request->getAttribute('addr_line1'),
            'addr_line2' => $request->getAttribute('addr_line2'),
            'addr_city' => $request->getAttribute('addr_city'),
            'addr_state' => $request->getAttribute('addr_state'),
            'addr_number' => $request->getAttribute('addr_number'),
            'addr_neighbourhood' => $request->getAttribute('addr_neighbourhood'),
            'addr_zipcode' => $request->getAttribute('addr_zipcode'),
            'property_owner_id' => $request->getAttribute('property_owner_id')
        ];

        $exist = Property::find($data, 1);

        if ($exist) {
            return Response::json(['message' => 'Unable to save Property, already exists an Property with the same name, email, phone_number and payment_day'], 422)->send();
        }

        $property = new Property();
        $property = $property->morph($data);
        $property->save();

        return Response::json($property->toArray(), 201)->send();

    }

    public function update(Request $request, $id)
    {
        $data = [
            'addr_line1' => $request->getAttribute('addr_line1'),
            'addr_line2' => $request->getAttribute('addr_line2'),
            'addr_city' => $request->getAttribute('addr_city'),
            'addr_state' => $request->getAttribute('addr_state'),
            'addr_number' => $request->getAttribute('addr_number'),
            'addr_neighbourhood' => $request->getAttribute('addr_neighbourhood'),
            'addr_zipcode' => $request->getAttribute('addr_zipcode'),
            'property_owner_id' => $request->getAttribute('property_owner_id'),
            'id' => $id,
        ];

        $property = Property::find(['id' => intval($id)], 1);

        if (!$property) {
            return Response::json(['message' => 'Property not found'], 404)->send();
        }

        $property = $property->morph($data);
        $property->save();

        return Response::json($property->toArray())->send();

    }

    public function delete(Request $request, $id)
    {
        /** @var Property|null $property */
        $property = Property::find(['id' => intval($id)], 1);

        if (!$property) {
            return Response::json(['message' => 'Property not found'], 404)->send();
        }

        $property->delete();

        return Response::json($property->toArray())->send();
    }

    public function storeContract(Request $request, $id)
    {
        /** @var Property|null $property */
        $property = Property::find(['id' => intval($id)], 1);

        if (!$property) {
            return Response::json(['message' => 'Property not found'], 404)->send();
        }

        $data = [
            'property_id' => $id,
            'tenant_id' => $request->getAttribute('tenant_id'),
            'starts_at' => $request->getAttribute('starts_at'),
            'ends_at' => $request->getAttribute('ends_at'),
            'administration_fee' => $request->getAttribute('administration_fee'),
            'rent_amount' => $request->getAttribute('rent_amount'),
            'condo_amount' => $request->getAttribute('condo_amount'),
            'iptu_amount' => $request->getAttribute('iptu_amount'),
        ];

        $exists = PropertyContract::find($data, 1);

        if ($exists) {
            return Response::json(['message' => 'Unable to save PropertyContract, already exists an PropertyContract with the same property, tenant, property owner, start date, end date, administration fee, rend amount, condo amount and iptu amount'], 422)->send();
        }

        $propertyContract = new PropertyContract();
        $propertyContract = $propertyContract->morph($data);
        $propertyContract->save();

        return Response::json($propertyContract->toArray())->send();
    }

    public function getContracts(Request $request, $id)
    {
        $contracts = PropertyContract::find(['property_id' => intval($id)]);

        $contracts = array_map(function ($contract) {
            return $contract->toArray();
        }, $contracts);

        return Response::json($contracts)->send();
    }

    public function getContract(Request $request, $id, $contractId)
    {
        $contract = PropertyContract::find(['property_id' => intval($id), 'id' => intval($contractId)]);

        if (!$contract) {
            return Response::json(['message' => 'PropertyContract not found to Property'], 404)->send();
        }

        return Response::json($contract->toArray())->send();
    }

    public function getContractPayments(Request $request, $id, $contractId)
    {
        $contract = PropertyContract::find(['property_id' => intval($id), 'id' => intval($contractId)], 1);

        if (!$contract) {
            return Response::json(['message' => 'PropertyContract not found to Property'], 404)->send();
        }

        $payments = ContractPayment::find(['property_contract_id' => intval($contractId)]);

        $payments = array_map(function ($payment) {
            return $payment->toArray();
        }, $payments);

        return Response::json($payments)->send();
    }

    public function getContractPayment(Request $request, $id, $contractId, $paymentId)
    {
        $contract = PropertyContract::find(['property_id' => intval($id), 'id' => intval($contractId)], 1);

        if (!$contract) {
            return Response::json(['message' => 'PropertyContract not found to Property'], 404)->send();
        }

        $payment = ContractPayment::find(['id' => intval($paymentId), 'property_contract_id' => intval($contractId)]);

        if (!$payment) {
            return Response::json(['message' => 'ContractPayment not found to PropertyContract'], 404)->send();
        }

        return Response::json($payment->toArray())->send();
    }
}
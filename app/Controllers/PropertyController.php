<?php

namespace App\Controllers;

use App\Controllers\Contracts\BaseController;
use App\Models\ContractPayment;
use App\Models\Property;
use App\Models\PropertyContract;
use App\Models\Tenant;
use App\Requests\Request;
use App\Requests\StorePropertyContractRequest;
use App\Response;
use App\Services\PropertyContractService;

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

    public function storeContract(StorePropertyContractRequest $request, $id)
    {

        /** @var Property|null $property */
        $property = Property::find(['id' => intval($request->getRouteParam('id'))], 1);
        $tenant = Tenant::find(['id' => intval($request->getAttribute('tenant_id'))], 1);

        if (!$property) {
            return Response::json(['message' => 'Property not found'], 404)->send();
        }

        if (!$tenant) {
            return Response::json(['message' => 'Tenant not found'], 404)->send();
        }

        $service = new PropertyContractService();
        $propertyContract = $service->createPropertyContract($property, $tenant, $request->getData());


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

    public function getContract(Request $request, $id, $contract_id)
    {
        $contract = PropertyContract::find(['property_id' => intval($id), 'id' => intval($contract_id)], 1);

        if (!$contract) {
            return Response::json(['message' => 'PropertyContract not found to Property'], 404)->send();
        }

        return Response::json($contract->toArray())->send();
    }

    public function getContractPayments(Request $request, $id, $contract_id)
    {
        $contract = PropertyContract::find(['property_id' => intval($id), 'id' => intval($contract_id)], 1);

        if (!$contract) {
            return Response::json(['message' => 'PropertyContract not found to Property'], 404)->send();
        }

        $payments = ContractPayment::find(['property_contract_id' => intval($contract_id)]);

        $payments = array_map(function ($payment) {
            return $payment->toArray();
        }, $payments);

        return Response::json($payments)->send();
    }

    public function getContractPayment(Request $request, $id, $contract_id, $payment_id)
    {
        $contract = PropertyContract::find(['property_id' => intval($id), 'id' => intval($contract_id)], 1);

        if (!$contract) {
            return Response::json(['message' => 'PropertyContract not found to Property'], 404)->send();
        }

        $payment = ContractPayment::find(['id' => intval($payment_id), 'property_contract_id' => intval($contract_id)]);

        if (!$payment) {
            return Response::json(['message' => 'ContractPayment not found to PropertyContract'], 404)->send();
        }

        return Response::json($payment->toArray())->send();
    }
}
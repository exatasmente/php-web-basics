<?php

namespace App\Controllers;

use App\Controllers\Contracts\BaseController;
use App\Requests\Request;
use App\Response;
use ReflectionException;
use App\Models\Tenant;

class TenantController extends BaseController
{
    public function all(Request $request)
    {
        $tenants = Tenant::find();

        if (!$tenants) {
            return Response::json([])->send();
        }

        $tenants = array_map(function($tenant) {
            return $tenant->toArray();
        }, $tenants);
        $tenants[] = [
            'count' => Tenant::count(),
        ];
        return Response::json($tenants)->send();

    }

    /**
     * @throws ReflectionException
     */
    public function show(Request $request, $id): Response
    {

        $tenant = Tenant::find(['id' => intval($id)], 1);
        if ($tenant) {
            return Response::json($tenant->toArray())->send();
        }

        return Response::json(['message' => 'tenant not found'], 404)->send();
    }

    public function store(Request $request)
    {
        $data = [
            'name' => $request->getAttribute('name'),
            'email' => $request->getAttribute('email'),
            'phone_number' => $request->getAttribute('phone_number')
        ];

        $exist = Tenant::find($data, 1);

        if ($exist) {
            return Response::json(['message' => 'Unable to save tenant, already exists an tenant with the same name, email and phone_number'], 422)->send();
        }

        $tenant = new Tenant();
        $tenant->name = $request->getAttribute('name');
        $tenant->email = $request->getAttribute('email');
        $tenant->phone_number = $request->getAttribute('phone_number');
        $tenant->save();

        return Response::json($tenant->toArray(), 201)->send();

    }

    public function update(Request $request, $id)
    {
        $tenant = Tenant::find(['id' => intval($id)], 1);

        if (!$tenant) {
            return Response::json(['message' => 'tenant not found'], 404)->send();
        }

        $tenant->name = $request->getAttribute('name', $tenant->name);
        $tenant->email = $request->getAttribute('email', $tenant->email);
        $tenant->phone_number = $request->getAttribute('phone_number', $tenant->phone_number);
        $tenant->save();

        return Response::json($tenant->toArray(), 200)->send();

    }

    public function delete(Request $request, $id)
    {
        /** @var Tenant|null $tenant */
        $tenant = Tenant::find(['id' => intval($id)], 1);

        if (!$tenant) {
            return Response::json(['message' => 'tenant not found'], 404)->send();
        }

        $tenant->delete();

        return Response::json($tenant->toArray(), 200)->send();
    }
}
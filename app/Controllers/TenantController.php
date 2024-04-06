<?php

namespace App\Controllers;

use App\Controllers\Contracts\BaseController;
use App\Models\Tenant;
use App\Requests\Request;
use App\Requests\CreateTenantRequest;
use App\Response;
use ReflectionException;

class TenantController extends BaseController
{
    public function all(Request $request)
    {
        $tenants = Tenant::find();

        if (!$tenants) {
            return Response::json([]);
        }

        $tenants = array_map(function ($tenant) {
            return $tenant->toArray();
        }, $tenants);

        return Response::json($tenants);

    }

    /**
     * @throws ReflectionException
     */
    public function show(Request $request, $id): Response
    {

        $tenant = Tenant::find(['id' => intval($id)], 1);
        if ($tenant) {
            return Response::json($tenant->toArray());
        }

        return Response::json(['message' => 'tenant not found'], 404);
    }

    public function store(CreateTenantRequest $request)
    {
        $tenant = new Tenant();
        $tenant->name = $request->getAttribute('name');
        $tenant->email = $request->getAttribute('email');
        $tenant->phone_number = $request->getAttribute('phone_number');
        $tenant->save();

        return Response::json($tenant->toArray(), 201);

    }

    public function update(Request $request, $id)
    {
        $tenant = Tenant::find(['id' => intval($id)], 1);

        if (!$tenant) {
            return Response::json(['message' => 'tenant not found'], 404);
        }

        $tenant->name = $request->getAttribute('name', $tenant->name);
        $tenant->email = $request->getAttribute('email', $tenant->email);
        $tenant->phone_number = $request->getAttribute('phone_number', $tenant->phone_number);
        $tenant->save();

        return Response::json($tenant->toArray(), 200);

    }

    public function delete(Request $request, $id)
    {
        /** @var Tenant|null $tenant */
        $tenant = Tenant::find(['id' => intval($id)], 1);

        if (!$tenant) {
            return Response::json(['message' => 'tenant not found'], 404);
        }

        $tenant->delete();

        return Response::json($tenant->toArray(), 200);
    }
}
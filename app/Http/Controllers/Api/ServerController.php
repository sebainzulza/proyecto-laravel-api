<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreServerRequest;
use App\Http\Requests\UpdateServerRequest;
use App\Models\Server;
use Illuminate\Http\JsonResponse;

class ServerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $servers = Server::all();

        return response()->json([
            'data' => $servers,
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreServerRequest $request): JsonResponse
    {
        $server = Server::create($request->validated());

        return response()->json([
            'data' => $server,
            'message' => 'Servidor creado exitosamente',
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Server $server): JsonResponse
    {
        return response()->json([
            'data' => $server,
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateServerRequest $request, Server $server): JsonResponse
    {
        $server->update($request->validated());

        return response()->json([
            'data' => $server,
            'message' => 'Servidor actualizado exitosamente',
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Server $server): JsonResponse
    {
        $server->delete();

        return response()->json([
            'message' => 'Servidor eliminado exitosamente',
        ], 200);
    }
}

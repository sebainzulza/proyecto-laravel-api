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
     * Listar todos los servidores.
     */
    public function index(): JsonResponse
    {
        $servers = Server::all();

        return response()->json([
            'data' => $servers,
            'total' => $servers->count(),
            'version' => 'v2.0',
        ], 200);
    }

    /**
     * Crear un nuevo servidor.
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
     * Mostrar un servidor específico.
     */
    public function show(Server $server): JsonResponse
    {
        return response()->json([
            'data' => $server,
        ], 200);
    }

    /**
     * Actualizar un servidor existente.
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
     * Eliminar un servidor.
     */
    public function destroy(Server $server): JsonResponse
    {
        $server->delete();

        return response()->json([
            'message' => 'Servidor eliminado exitosamente',
        ], 200);
    }
}

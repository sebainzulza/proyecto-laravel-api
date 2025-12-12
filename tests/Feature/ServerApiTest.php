<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Server;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ServerApiTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test listing all servers.
     */
    public function test_can_list_servers(): void
    {
        Server::factory()->count(3)->create();

        $response = $this->getJson('/api/servers');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'name', 'ip_address', 'status', 'created_at', 'updated_at'],
                ],
            ])
            ->assertJsonCount(3, 'data');
    }

    /**
     * Test creating a server successfully.
     */
    public function test_can_create_server(): void
    {
        $serverData = [
            'name' => 'Production Server',
            'ip_address' => '192.168.1.100',
            'status' => true,
        ];

        $response = $this->postJson('/api/servers', $serverData);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => ['id', 'name', 'ip_address', 'status', 'created_at', 'updated_at'],
                'message',
            ])
            ->assertJson([
                'data' => [
                    'name' => 'Production Server',
                    'ip_address' => '192.168.1.100',
                    'status' => true,
                ],
                'message' => 'Servidor creado exitosamente',
            ]);

        $this->assertDatabaseHas('servers', [
            'name' => 'Production Server',
            'ip_address' => '192.168.1.100',
        ]);
    }

    /**
     * Test validation error when creating server without name.
     */
    public function test_create_server_validation_fails_without_name(): void
    {
        $serverData = [
            'ip_address' => '192.168.1.100',
        ];

        $response = $this->postJson('/api/servers', $serverData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name']);
    }

    /**
     * Test validation error when creating server with invalid IP.
     */
    public function test_create_server_validation_fails_with_invalid_ip(): void
    {
        $serverData = [
            'name' => 'Test Server',
            'ip_address' => 'invalid-ip',
        ];

        $response = $this->postJson('/api/servers', $serverData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['ip_address']);
    }

    /**
     * Test validation error when creating server with duplicate name.
     */
    public function test_create_server_validation_fails_with_duplicate_name(): void
    {
        Server::factory()->create(['name' => 'Duplicate Server']);

        $serverData = [
            'name' => 'Duplicate Server',
            'ip_address' => '192.168.1.100',
        ];

        $response = $this->postJson('/api/servers', $serverData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name']);
    }

    /**
     * Test showing a single server.
     */
    public function test_can_show_server(): void
    {
        $server = Server::factory()->create();

        $response = $this->getJson("/api/servers/{$server->id}");

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $server->id,
                    'name' => $server->name,
                    'ip_address' => $server->ip_address,
                ],
            ]);
    }

    /**
     * Test showing a non-existent server returns 404.
     */
    public function test_show_server_returns_404_when_not_found(): void
    {
        $response = $this->getJson('/api/servers/999');

        $response->assertStatus(404);
    }

    /**
     * Test updating a server.
     */
    public function test_can_update_server(): void
    {
        $server = Server::factory()->create();

        $updateData = [
            'name' => 'Updated Server',
            'ip_address' => '192.168.1.200',
        ];

        $response = $this->putJson("/api/servers/{$server->id}", $updateData);

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'name' => 'Updated Server',
                    'ip_address' => '192.168.1.200',
                ],
                'message' => 'Servidor actualizado exitosamente',
            ]);

        $this->assertDatabaseHas('servers', [
            'id' => $server->id,
            'name' => 'Updated Server',
            'ip_address' => '192.168.1.200',
        ]);
    }

    /**
     * Test deleting a server.
     */
    public function test_can_delete_server(): void
    {
        $server = Server::factory()->create();

        $response = $this->deleteJson("/api/servers/{$server->id}");

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Servidor eliminado exitosamente',
            ]);

        $this->assertDatabaseMissing('servers', [
            'id' => $server->id,
        ]);
    }

    /**
     * Test deleting a non-existent server returns 404.
     */
    public function test_delete_server_returns_404_when_not_found(): void
    {
        $response = $this->deleteJson('/api/servers/999');

        $response->assertStatus(404);
    }
}

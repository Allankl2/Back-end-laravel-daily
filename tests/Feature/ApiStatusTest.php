<?php

namespace Tests\Feature;

use Tests\TestCase;

class ApiStatusTest extends TestCase
{
    public function test_retorna_status_200(): void
    {
        $response = $this->getJson('/api/');

        $response->assertStatus(200);
    }

    public function test_retorna_json(): void
    {
        $response = $this->getJson('/api/');

        $response->assertHeader('Content-Type', 'application/json');
    }

    public function test_retorna_estrutura_correta(): void
    {
        $response = $this->getJson('/api/');

        $response->assertJsonStructure([
            'status',
            'message',
        ]);
    }

    public function test_retorna_valores_corretos(): void
    {
        $response = $this->getJson('/api/');

        $response->assertJson([
            'status'  => 'ok',
            'message' => 'API funcionando',
        ]);
    }
}

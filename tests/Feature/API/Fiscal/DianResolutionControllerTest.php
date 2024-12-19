<?php

namespace Tests\Feature\API\Fiscal;

use Tests\TestCase;
use App\Models\Fiscal\DianResolution;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

class DianResolutionControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Crear y autenticar un usuario para las pruebas
        $user = User::factory()->create();
        Sanctum::actingAs($user);
    }

    /** @test */
    public function it_can_list_resolutions()
    {
        // Arrange
        DianResolution::factory()->count(3)->create();

        // Act
        $response = $this->getJson('/api/fiscal/resolutions');

        // Assert
        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'resolution_number',
                        'start_date',
                        'end_date',
                        'created_at',
                        'updated_at'
                    ]
                ],
                'meta' => [
                    'current_page',
                    'from',
                    'last_page',
                    'per_page',
                    'to',
                    'total'
                ]
            ]);
    }

    /** @test */
    public function it_can_create_resolution()
    {
        // Arrange
        $resolutionData = [
            'resolution_number' => '18760000001',
            'start_date' => '2024-01-01',
            'end_date' => '2024-12-31'
        ];

        // Act
        $response = $this->postJson('/api/fiscal/resolutions', $resolutionData);

        // Assert
        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'resolution_number',
                    'start_date',
                    'end_date',
                    'created_at',
                    'updated_at'
                ]
            ]);

        $this->assertDatabaseHas('dian_resolutions', $resolutionData);
    }

    /** @test */
    public function it_validates_required_fields_when_creating_resolution()
    {
        // Act
        $response = $this->postJson('/api/fiscal/resolutions', []);

        // Assert
        $response->assertStatus(422)
            ->assertJsonValidationErrors([
                'resolution_number',
                'start_date',
                'end_date'
            ]);
    }
}

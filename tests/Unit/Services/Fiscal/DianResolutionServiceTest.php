<?php

namespace Tests\Unit\Services\Fiscal;

use Tests\TestCase;
use App\Services\Fiscal\DianResolutionService;
use App\Repositories\Contracts\Fiscal\DianResolutionRepositoryInterface;
use Mockery;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\Fiscal\DianResolution;

class DianResolutionServiceTest extends TestCase
{
    protected $service;
    protected $repository;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Mock del repositorio
        $this->repository = Mockery::mock(DianResolutionRepositoryInterface::class);
        
        // Inyección del mock en el servicio
        $this->service = new DianResolutionService($this->repository);
    }

    /** @test */
    public function it_can_get_all_resolutions()
    {
        // Arrange
        $expectedResolutions = new LengthAwarePaginator(
            collect([new DianResolution()]),
            1,
            15,
            1
        );

        // Configurar el mock
        $this->repository->shouldReceive('getAll')
            ->once()
            ->with([])
            ->andReturn($expectedResolutions);

        // Act
        $result = $this->service->getAll();

        // Assert
        $this->assertEquals($expectedResolutions, $result);
    }

    /** @test */
    public function it_can_find_resolution_by_id()
    {
        // Arrange
        $id = 1;
        $expectedResolution = new DianResolution();
        
        // Configurar el mock
        $this->repository->shouldReceive('findById')
            ->once()
            ->with($id)
            ->andReturn($expectedResolution);

        // Act
        $result = $this->service->findById($id);

        // Assert
        $this->assertEquals($expectedResolution, $result);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}

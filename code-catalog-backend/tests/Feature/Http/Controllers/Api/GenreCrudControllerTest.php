<?php

namespace Tests\Feature\Http\Controllers\Api;

use Tests\TestCase;
use App\Http\Controllers\Api\BasicCrudController;
use Tests\Stubs\Controllers\GenreControllerStub;
use Tests\Stubs\Models\GenreStub;

use Tests\Traits\BasicCrudControllerValidations;

class GenreCrudControllerTest extends TestCase
{   
    use BasicCrudControllerValidations;

    protected function setUp(): void
    {
        parent::setUp();
        GenreStub::dropTable();
        GenreStub::createTable();
        $this->controller = new GenreControllerStub();
        $this->reflectionClass = new \ReflectionClass(BasicCrudController::class);
    }
    
    protected function tearDown(): void
    {
        GenreStub::dropTable();
        parent::tearDown();
    }

    public function getNewModelStub()
    {
        return GenreStub::create(
            ['name' => 'test name', 'is_active' => true]
        );
    }

    public function model()
    {
        return GenreStub::class;
    }

    public function getFindModelStubArray($id)
    {
        return GenreStub::find($id)->toArray();
    }

    public function testIndex()
    {
        $this->assertIndex();
    }

    public function testInvalidationData()
    {   
        $this->assertInvalidationData(['name' => '']);
        $this->assertInvalidationData(['name' => 'test name', 'is_active' => 'test']);
    }

    public function testStore()
    {   
        $this->assertStore(['name' => 'test name', 'is_active' => false]);
    }

    public function testIFFindOrFailFetchModel()
    {   
        $this->assertIFFindOrFailFetchModel();
    }

    public function testIFFindOrFailThrowExceptionNotFoundModel()
    {   
        $this->assertIFFindOrFailThrowExceptionNotFoundModel();
    }

    public function testShow()
    {
        $this->assertShow();
    }

    public function testDestroy()
    {
        $this->assertDestroy();
    }
}

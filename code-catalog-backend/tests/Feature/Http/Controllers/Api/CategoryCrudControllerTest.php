<?php

namespace Tests\Feature\Http\Controllers\Api;

use Tests\TestCase;
use App\Http\Controllers\Api\BasicCrudController;
use Tests\Stubs\Controllers\CategoryControllerStub;
use Tests\Stubs\Models\CategoryStub;

use Tests\Traits\BasicCrudControllerValidations;

class CategoryCrudControllerTest extends TestCase
{   
    use BasicCrudControllerValidations;

    protected function setUp(): void
    {
        parent::setUp();
        CategoryStub::dropTable();
        CategoryStub::createTable();
        $this->controller = new CategoryControllerStub();
        $this->reflectionClass = new \ReflectionClass(BasicCrudController::class);
    }
    
    protected function tearDown(): void
    {
        CategoryStub::dropTable();
        parent::tearDown();
    }

    public function getNewModelStub()
    {
        return CategoryStub::create(
            ['name' => 'test name', 'description' => 'test description']
        );
    }

    public function model()
    {
        return CategoryStub::class;
    }

    public function getFindModelStubArray($id)
    {
        return CategoryStub::find($id)->toArray();
    }

    public function testIndex()
    {
        $this->assertIndex();
    }

    public function testInvalidationData()
    {   
        $this->assertInvalidationData(['name' => '']);
    }

    public function testStore()
    {   
        $this->assertStore(['name' => 'test name', 'description' => 'test description']);
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

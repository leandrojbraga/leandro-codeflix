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

    private $sendData;

    protected function setUp(): void
    {
        parent::setUp();
        CategoryStub::dropTable();
        CategoryStub::createTable();
        $this->controller = new CategoryControllerStub();
        $this->reflectionClass = new \ReflectionClass(BasicCrudController::class);
        $this->sendData = [
            'name' => 'test name',
            'description' => 'test description'
        ];
    }
    
    protected function tearDown(): void
    {
        CategoryStub::dropTable();
        parent::tearDown();
    }

    public function getNewModelStub()
    {
        return CategoryStub::create($this->sendData);
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
        $this->assertInvalidationData([]);

        $data = array_replace($this->sendData, ['name' => '']);
        $this->assertInvalidationData($data);

        $data = array_replace($this->sendData, ['name' => str_repeat('t', 500)]);
        $this->assertInvalidationData($data);
    }

    public function testStore()
    {   
        $this->assertStore($this->sendData);
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

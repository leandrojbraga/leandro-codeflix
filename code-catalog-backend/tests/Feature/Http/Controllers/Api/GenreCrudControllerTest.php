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

    private $sendData;

    protected function setUp(): void
    {
        parent::setUp();
        GenreStub::dropTable();
        GenreStub::createTable();
        $this->controller = new GenreControllerStub();
        $this->reflectionClass = new \ReflectionClass(BasicCrudController::class);
        $this->sendData = ['name' => 'test name'];
    }
    
    protected function tearDown(): void
    {
        GenreStub::dropTable();
        parent::tearDown();
    }

    public function getNewModelStub()
    {
        return GenreStub::create($this->sendData);
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
        $this->assertInvalidationData([]);

        $data = array_replace($this->sendData, ['name' => '']);
        $this->assertInvalidationData($data);

        $data = array_replace($this->sendData, ['name' => str_repeat('t', 500)]);
        $this->assertInvalidationData($data);

        $data = array_replace($this->sendData, ['is_active' => 'test']);
        $this->assertInvalidationData($data);
    }

    public function testStore()
    {   
        $this->assertStore(
            $this->sendData + ['is_active' => false]
        );
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

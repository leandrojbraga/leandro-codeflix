<?php

namespace Tests\Feature\Http\Controllers\Api;

use Tests\TestCase;
use App\Http\Controllers\Api\BasicCrudController;
use Tests\Stubs\Controllers\ContentDescriptorControllerStub;
use Tests\Stubs\Models\ContentDescriptorStub;

use Tests\Traits\BasicCrudControllerValidations;

class ContentDescriptorCrudControllerTest extends TestCase
{   
    use BasicCrudControllerValidations;

    private $sendData;

    protected function setUp(): void
    {
        parent::setUp();
        ContentDescriptorStub::dropTable();
        ContentDescriptorStub::createTable();
        $this->controller = new ContentDescriptorControllerStub();
        $this->reflectionClass = new \ReflectionClass(BasicCrudController::class);
        $this->sendData = ['name' => 'test name'];
    }
    
    protected function tearDown(): void
    {
        ContentDescriptorStub::dropTable();
        parent::tearDown();
    }

    public function getNewModelStub()
    {
        return ContentDescriptorStub::create($this->sendData);
    }

    public function model()
    {
        return ContentDescriptorStub::class;
    }

    public function getFindModelStubArray($id)
    {
        return ContentDescriptorStub::find($id)->toArray();
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

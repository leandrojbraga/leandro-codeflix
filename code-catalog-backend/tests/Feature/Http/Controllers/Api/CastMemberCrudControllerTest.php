<?php

namespace Tests\Feature\Http\Controllers\Api;

use Tests\TestCase;
use App\Http\Controllers\Api\BasicCrudController;
use Tests\Stubs\Controllers\CastMemberControllerStub;
use Tests\Stubs\Models\CastMemberStub;

use Tests\Traits\BasicCrudControllerValidations;

class CastMemberCrudControllerTest extends TestCase
{   
    use BasicCrudControllerValidations;

    protected function setUp(): void
    {
        parent::setUp();
        CastMemberStub::dropTable();
        CastMemberStub::createTable();
        $this->controller = new CastMemberControllerStub();
        $this->reflectionClass = new \ReflectionClass(BasicCrudController::class);
    }
    
    protected function tearDown(): void
    {
        CastMemberStub::dropTable();
        parent::tearDown();
    }

    public function getNewModelStub()
    {
        return CastMemberStub::create(
            ['name' => 'test name', 'type' => 1]
        );
    }

    public function model()
    {
        return CastMemberStub::class;
    }

    public function getFindModelStubArray($id)
    {
        return CastMemberStub::find($id)->toArray();
    }

    public function testIndex()
    {
        $this->assertIndex();
    }

    public function testInvalidationData()
    {   
        $data = ['name' => '', 'type' => 1];
        $this->assertInvalidationData($data);
        
        $data = ['name' => 'test name'];
        $this->assertInvalidationData($data);
    }

    public function testStore()
    {   
        $this->assertStore(['name' => 'test name', 'type' => 1]);
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

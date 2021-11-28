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

    private $sendData;

    protected function setUp(): void
    {
        parent::setUp();
        CastMemberStub::dropTable();
        CastMemberStub::createTable();
        $this->controller = new CastMemberControllerStub();
        $this->reflectionClass = new \ReflectionClass(BasicCrudController::class);
        $this->sendData = [
            'name' => 'test name',
            'type' => CastMemberStub::TYPE_DIRECTOR
        ];
    }
    
    protected function tearDown(): void
    {
        CastMemberStub::dropTable();
        parent::tearDown();
    }

    public function getNewModelStub()
    {
        return CastMemberStub::create($this->sendData);
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
        $this->assertInvalidationData([]);

        $data = array_replace($this->sendData, ['name' => '']);
        $this->assertInvalidationData($data);
        
        $this->assertInvalidationData([]);

        $data = array_replace($this->sendData, ['type' => 'a']);
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

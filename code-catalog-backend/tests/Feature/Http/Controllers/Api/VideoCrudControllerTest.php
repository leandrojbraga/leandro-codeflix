<?php

namespace Tests\Feature\Http\Controllers\Api;

use Tests\TestCase;
use App\Http\Controllers\Api\BasicCrudController;
use Tests\Stubs\Controllers\VideoControllerStub;
use Tests\Stubs\Models\VideoStub;

use Tests\Traits\BasicCrudControllerValidations;

class VideoCrudControllerTest extends TestCase
{   
    use BasicCrudControllerValidations;

    private $sendData;

    protected function setUp(): void
    {
        parent::setUp();
        VideoStub::dropTable();
        VideoStub::createTable();
        $this->controller = new VideoControllerStub();
        $this->reflectionClass = new \ReflectionClass(BasicCrudController::class);
        $this->sendData = [
            'title' => 'Video title',
            'year_launched' => 2020,
            'rating' => VideoStub::RATING_GENERAL_AUDIENCE
        ];
    }
    
    protected function tearDown(): void
    {
        VideoStub::dropTable();
        parent::tearDown();
    }

    public function getNewModelStub()
    {
        return VideoStub::create($this->sendData);
    }

    public function model()
    {
        return VideoStub::class;
    }

    public function getFindModelStubArray($id)
    {
        return VideoStub::find($id)->toArray();
    }

    public function testIndex()
    {
        $this->assertIndex();
    }

    public function testInvalidationData()
    {   
        $this->assertInvalidationData([]);

        $data = array_replace($this->sendData, ['title' => '']);
        $this->assertInvalidationData($data);

        $data = array_replace($this->sendData, ['title' => str_repeat('t', 500)]);
        $this->assertInvalidationData($data);
        
        $data = array_replace($this->sendData, ['year_launched' => null]);
        $this->assertInvalidationData($data);

        $data = array_replace($this->sendData, ['year_launched' => 99]);
        $this->assertInvalidationData($data);

        $data = array_replace($this->sendData, ['rating' => '']);
        $this->assertInvalidationData($data);

        $data = array_replace($this->sendData, ['rating' => 'a']);
        $this->assertInvalidationData($data);

        $data = $this->sendData + ['opened' => 'b'];
        $this->assertInvalidationData($data);
    }

    public function testStore()
    {   
        $this->assertStore(
            $this->sendData + ['opened' => true]
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

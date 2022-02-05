<?php

namespace Tests\Traits;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

trait BasicCrudControllerValidations
{   
    private $controller;
    private $reflectionClass;

    public abstract function model();

    public abstract function getNewModelStub();

    public abstract function getFindModelStubArray($id);

    public function assertIndex()
    {
        $model = $this->getNewModelStub();
        $model->refresh();

        $this->assertEquals(
            [$model->toArray()],
            $this->controller->index()->toArray(0)
        );
    }

    public function assertInvalidationData(array $requestReturn)
    {   
        $this->expectException(ValidationException::class);

        $request = \Mockery::mock(Request::class);
        $request->shouldReceive('all')
            ->once()
            ->andReturn($requestReturn);
        
        $reflectionMethod = $this->reflectionClass->getMethod('validateRequestData');
        $reflectionMethod->setAccessible(true);

        $reflectionMethod->invokeArgs($this->controller, [$request]);
    }

    public function assertStore(array $requestReturn)
    {
        $request = \Mockery::mock(Request::class);
        $request->shouldReceive('all')
            ->once()
            ->andReturn($requestReturn);
            
        $obj = $this->controller->store($request);

        $this->assertEquals(
            $this->getFindModelStubArray(1),
            $obj->toArray(0)
        );
    }

    public function assertIFFindOrFailFetchModel()
    {   
        $model = $this->getNewModelStub();

        $reflectionMethod = $this->reflectionClass->getMethod('findOrFail');
        $reflectionMethod->setAccessible(true);

        $result = $reflectionMethod->invokeArgs($this->controller, [$model->id]);
        $this->assertInstanceOf($this->model(), $result);
    }

    public function assertIFFindOrFailThrowExceptionNotFoundModel()
    {   
        $this->expectException(ModelNotFoundException::class);

        $reflectionMethod = $this->reflectionClass->getMethod('findOrFail');
        $reflectionMethod->setAccessible(true);

        $reflectionMethod->invokeArgs($this->controller, [0]);
    }

    public function assertShow()
    {
        $model = $this->getNewModelStub();

        $obj = $this->controller->show($model->id);

        $this->assertEquals(
            $this->getFindModelStubArray($model->id),
            $obj->toArray(0)
        );
    }

    public function assertDestroy()
    {
        $model = $this->getNewModelStub();

        $obj = $this->controller->destroy($model->id);
        
        $this->createTestResponse($obj)
            ->assertStatus(Response::HTTP_NO_CONTENT);

        $this->assertCount(0, (new $model())::all());
    }
}
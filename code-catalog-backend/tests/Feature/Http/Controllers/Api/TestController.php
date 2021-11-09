<?php

namespace Tests\Feature\Http\Controllers\Api;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Ramsey\Uuid\Uuid;
use Tests\TestCase;

class TestController extends TestCase
{   
    use DatabaseMigrations;
    
    protected $class;
    protected $model;

    public function getResponse($method, $route, $data = []) {
        $headers = ['Accept' => 'application/json'];

        return $this->json(
            $method,
            $route,
            $data,
            $headers);
    }

    public function validateIndex($route)
    {   
        $response = $this->getResponse('GET', $route);

        $response
            ->assertStatus(200)
            ->assertJson([$this->model->toArray()]);
    }

    public function validateShow($route)
    {   
        $response = $this->getResponse('GET', $route);

        $response
            ->assertStatus(200)
            ->assertJson($this->model->toArray());
    }

    public function validateShowNotFound($route)
    {   
        $response = $this->getResponse('GET', $route);

        $response
            ->assertStatus(404)
            ->assertSee("No query results for model");
    }

    public function validateInvalidationData(
        $method, $route, $data, $validationErros, $fragment)
    {   
        $response = $this->getResponse($method, $route, $data);

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors($validationErros)
            ->assertJsonFragment($fragment);
    }

    public function validateInvalidationDataNotRequired(
        $method, $route, $data, $missingValidationErros)
    {   
        $response = $this->getResponse($method, $route, $data);

        $response
            ->assertStatus(422)
            ->assertJsonMissingValidationErrors($missingValidationErros);
    }

    public function validateStore($route, $data, $AttrsValidade, $validateId = False)
    {   
        $response = $this->getResponse('POST', $route, $data);

        $this->validateResponseAttrs($response, 201, $AttrsValidade);

        if ($validateId) {
            $this->validateIdisUuid4($response->json('id'));
        }
    }

    public function validateIdisUuid4($id) {        
        $this->assertTrue(Uuid::isValid($id));
        $this->assertEquals(
            Uuid::fromString($id)->getVersion(), 
            Uuid::UUID_TYPE_RANDOM
        );
    }

    public function validateUpdate($route, $data)
    {   
        $response = $this->getResponse('PUT', $route, $data);

        $this->validateResponseAttrs($response, 200, $data);
    }

    public function validateResponseAttrs($response, $statusCode, $AttrsValidade) {
        $id = $response->json('id');
        $newModel = $this->class::find($id);

        $response
            ->assertStatus($statusCode)
            ->assertJson($newModel->toArray());

        foreach ($AttrsValidade as $key => $value) {
            $response->assertJsonFragment([
                $key => $value
            ]);
        }
    }

    public function validateDestroy($route) {
        $response = $this->getResponse("DELETE", $route);
        
        $response->assertStatus(204);

        $response = $this->getResponse("DELETE", $route);
        
        $response
            ->assertStatus(404)
            ->assertSee("No query results for model");
    }
}
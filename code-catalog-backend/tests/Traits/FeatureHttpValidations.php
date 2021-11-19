<?php

namespace Tests\Traits;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Lang;

trait FeatureHttpValidations
{   
    use UuidValidations, DatabaseValidations;

    private $response;

    protected abstract function model();

    protected abstract function setFactoryModel();

    protected abstract function getFactoryModel();

    protected abstract function setRoute(string $routeSuffix, array $params = []);

    protected abstract function getRoute();


    public function sendRequest(string $method = "GET", $data = []) {
        $headers = ['Accept' => 'application/json'];

        $this->response = $this->json(
            $method,
            $this->getRoute(),
            $data,
            $headers);
    }

    public function assertRequestAndStatusCode(
        int $statusCode, string $method = "GET", $data = [])
    {
        $this->sendRequest($method, $data);

        $this->response->assertStatus($statusCode);
    }

    public function assertIndex()
    {   
        $this->assertRequestAndStatusCode(Response::HTTP_OK);
    
        $this->response->assertJson([$this->getFactoryModel()->toArray()]);
    }

    public function assertShow()
    {   
        $this->assertRequestAndStatusCode(Response::HTTP_OK);

        $this->response->assertJson($this->getFactoryModel()->toArray());
    }

    public function assertShowNotFound()
    {   
        $this->assertRequestAndStatusCode(Response::HTTP_NOT_FOUND);

        $this->response->assertSee("No query results for model");
    }

    public function assertInvalidationDataRequest($method, $data) {
        $this->assertRequestAndStatusCode(
            Response::HTTP_UNPROCESSABLE_ENTITY,
            $method,
            $data
        );
    }
    public function assertInvalidationData(
        string $method, array $data,
        string $attribute, object $validation)
    {   
        $this->assertInvalidationDataRequest($method, $data);
        
        $attributeName = str_replace('_', ' ', $attribute);

        $this->response
            ->assertJsonValidationErrors([$attribute])
            ->assertJsonFragment([ 
                Lang::get(
                    'validation.'.$validation->key, 
                    ['attribute' => $attributeName] + $validation->replace)   
            ]);
    }

    public function assertMissingValidationDataNotRequired(
        string $method, array $data, array $attributes)
    {   
        $this->assertInvalidationDataRequest($method, $data);

        $this->response->assertJsonMissingValidationErrors($attributes);
    }
    
    public function assertStore($data, $validadeData)
    {   
        $this->assertRequestAndStatusCode(
            Response::HTTP_CREATED,
            'POST',
            $data
        );
        
        $this->assertDatabaseData($validadeData);
    }

    public function assertUpdate($data)
    {   
        $this->assertRequestAndStatusCode(
            Response::HTTP_OK,
            'PUT',
            $data
        );
        
        $this->assertDatabaseData($data);
    }   

    public function assertDestroy() {
        $this->assertRequestAndStatusCode(
            Response::HTTP_NO_CONTENT,
            'DELETE'
        );

        $this->assertRequestAndStatusCode(
            Response::HTTP_NOT_FOUND,
            'DELETE'
        );

        $this->response->assertSee("No query results for model");
    }

    public function getRequestId() {
        return $this->response->json('id');
    }

}
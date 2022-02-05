<?php

namespace Tests\Traits;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Lang;

trait FeatureHttpValidations
{   
    use UuidValidations, DatabaseValidations, RelationsSerializedFields;

    
    protected $response;

    protected $relations = [];

    protected abstract function model();

    protected abstract function modelResource();

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

    public function assertModelResourceJson($model)
    {
        $resource = $this->modelResource();
        if ($resource) {
            $modelResponseArray = (new $resource($model))->response()->getData(true);
            $this->response->assertJson($modelResponseArray);
        }        
    }

    public function assertModelResourceCollectJson($modelArray)
    {
        $resource = $this->modelResource();
        if ($resource) {
            $modelResponseArray = $resource::collection(collect($modelArray))->response()->getData(true);
            $this->response->assertJson($modelResponseArray);
        }        
    }

    public function assertIndex($perPage = 15)
    {   
        $this->assertRequestAndStatusCode(Response::HTTP_OK);

        if ($this->response->json('data')) {
            $fieldStructure = [
                'data' => [
                    '*' => $this->getModelSerializedFields()
                ],
                'links' => [],
                'meta' => []
            ];
            $this->response
                ->assertJson([
                    'meta' => ['per_page' => $perPage]
                ])
                ->assertJsonStructure($fieldStructure);

        } else {
            $this->response->assertJsonStructure(
                $this->getModelSerializedFields()
            );
        }
        
        $this->assertModelResourceCollectJson(
            [$this->getFactoryModel()]
        );
    }

    public function assertShow()
    {   
        $this->assertRequestAndStatusCode(Response::HTTP_OK);

        $this->response->assertJsonStructure(
            $this->getModelResponseSerializedFields()
        );

        $this->assertModelResourceJson(
            $this->getFactoryModel()
        );
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
        string $validationRule, array $attributeRuleReplaces = [])
    {   
        $this->assertInvalidationDataRequest($method, $data);

        foreach(array_keys($data) as $attribute) {
            $attributeName = str_replace('_', ' ', $attribute);
            $replace = isset($attributeRuleReplaces[$attribute]) 
                ? $attributeRuleReplaces[$attribute] 
                : [];

            $this->response
            ->assertJsonValidationErrors([$attribute])
            ->assertJsonFragment([ 
                Lang::get(
                    'validation.'.$validationRule, 
                    ['attribute' => $attributeName] + $replace)   
            ]);
        }
    }

    public function assertMissingValidationDataNotRequired(
        string $method, array $data, array $attributes)
    {   
        $this->assertInvalidationDataRequest($method, $data);

        $this->response->assertJsonMissingValidationErrors($attributes);
    }
    
    public function assertData($validadeData)
    {
        $this->assertDatabaseData(
            $validadeData + ['id' => $this->getRequestId()]
        );

        $this->response->assertJsonStructure(
            $this->getModelResponseSerializedFields()
        );

        $this->assertModelResourceJson(
            $this->model()::find($this->getRequestId())
        );
    }

    public function assertStore($data, $validadeData)
    {   
        $this->assertRequestAndStatusCode(
            Response::HTTP_CREATED,
            'POST',
            $data
        );
        
        $this->assertData($validadeData);
        
    }

    public function assertUpdate($data, $validadeData)
    {   
        $this->assertRequestAndStatusCode(
            Response::HTTP_OK,
            'PUT',
            $data
        );
        
        $this->assertData($validadeData);
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
        return $this->response->json('id') ?? $this->response->json('data.id');
    }

    protected function getModelSerializedFields() {
        $model = $this->model();
        $class = new $model;

        $fields = array_merge(
            ['id'],
            array_values($class->getFillable()),
            array_values($class->getDates()),
            $this->getRelationsSerializedFields($this->relations)
        ); 

        return $fields;
    }

    public function getModelResponseSerializedFields() {
        if ($this->response->json('data')) {
            return [ 'data' => $this->getModelSerializedFields() ];
        }
        
        return $this->getModelSerializedFields();
    }

}
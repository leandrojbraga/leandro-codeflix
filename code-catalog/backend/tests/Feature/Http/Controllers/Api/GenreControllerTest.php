<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Http\Controllers\Api\GenreController;
use App\Http\Resources\GenreResource;
use App\Models\Category;
use App\Models\Genre;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Tests\Traits\FeatureHttpValidations;
use Tests\Exceptions\TestException;
use Illuminate\Http\Request;

class GenreControllerTest extends TestCase
{ 

    use DatabaseMigrations, FeatureHttpValidations;
    
    private $factoryModel;
    private $route;
    private $sendData;
    private $factoryCategory;
    private $sendConstrains;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setFactoryModel();
        $this->sendData = [ 'name' => 'Genre name' ];

        $this->factoryCategory = factory(Category::class)->create();

        $this->sendConstrains = [
            'categories_id' => [$this->factoryCategory->id]
        ];

        $this->relations = ['categories'];
    }

    protected function model() {
        return Genre::class;
    }

    protected function modelResource() {
        return GenreResource::class;
    }

    protected function setFactoryModel() {
        $model = $this->model();
        $this->factoryModel = factory($model)->create();
    }

    protected function getFactoryModel() {
        return $this->factoryModel;
    }

    protected function setRoute(string $routeSuffix, array $params = []) {
        $routePrefix = "genres";
        $this->route = route($routePrefix.'.'.$routeSuffix, $params);
    }

    protected function getRoute() {
        return $this->route;
    }

    public function testIndex()
    {   
        $this->setRoute('index');
        $this->assertIndex();
    }

    public function testShow()
    {   
        $this->setRoute('show', ['genre' => $this->getFactoryModel()->id]);
        $this->assertShow();

        $this->setRoute('show', ['genre' => 0]);
        $this->assertShowNotFound();
    }
    
    public function testInvalidationData()
    {   
        // Test create
        $this->setRoute('store');
        $this->assertInvalidationDataByAttribute('POST');

        // Test update
        $this->setRoute('update', ['genre' => $this->getFactoryModel()->id]);
        $this->assertInvalidationDataByAttribute('PUT');
    }

    public function assertInvalidationRequired($method) {   
        $data = [
            'name' => '',
            'categories_id' => ''
        ];

        $this->assertInvalidationData(
            $method, $data, 'required'
        );
    }

    public function assertInvalidationLength($method) {
        $data = [
            'name' => str_repeat('G', 256)
        ];
        $attributeRuleReplaces = [
            'name' => [ 'max' => 255 ]
        ];

        $this->assertInvalidationData(
            $method, $data, 'max.string', $attributeRuleReplaces
        );


        $data = [
            'name' => 'G'
        ];
        $attributeRuleReplaces = [
            'name' => [ 'min' => 3 ]
        ];

        $this->assertInvalidationData(
            $method, $data, 'min.string', $attributeRuleReplaces
        );
    }

    public function assertInvalidationBoolean($method){
        $data = [
            'is_active' => 'g'
        ];

        $this->assertInvalidationData(
            $method, $data, 'boolean'
        );
    }

    public function assertInvalidationArray($method){
        $data = [
            'categories_id' => 'test'
        ];

        $this->assertInvalidationData(
            $method, $data, 'array'
        );
    }

    public function assertInvalidationConstraintsExists($method){
        $data = [
            'categories_id' => ['test']
        ];

        $this->assertInvalidationData(
            $method, $data, 'exists'
        );
        
        $category = factory(Category::class)->create();
        $category->delete();
        $data = [
            'categories_id' => [$category->id]
        ];

        $this->assertInvalidationData(
            $method, $data, 'exists'
        );
    }

    public function assertInvalidationDataByAttribute($method)
    {   
        $this->assertInvalidationRequired($method);

        $this->assertInvalidationLength($method);

        $this->assertInvalidationBoolean($method);

        $this->assertInvalidationArray($method);

        $this->assertInvalidationConstraintsExists($method);

        $this->assertMissingValidationDataNotRequired(
            $method,
            [],
            ['is_active']
        );
    }

    public function assertSaveConstrains() {       
        $genre = $this->model()::find($this->getRequestId());

        $this->assertCount(1,$genre->categories);
        $this->assertDatabaseHas('category_genre',
            [
                'genre_id' => $genre->id,
                'category_id' => $this->factoryCategory->id,
            ]
        );
    }

    public function testSave()
    {
        $data = [
            [   // Validate a default create
                'send_data' => $this->sendData + $this->sendConstrains,
                'test_data' => $this->sendData + ['is_active' => true]
            ],
            [
                'send_data' => $this->sendData +
                                $this->sendConstrains + ['is_active' => false],
                'test_data' => $this->sendData + ['is_active' => false]
            ],
            [
                'send_data' => $this->sendData + 
                                $this->sendConstrains + ['is_active' => true],
                'test_data' => $this->sendData + ['is_active' => true]
            ]
        ];

        foreach($data as $key => $value) {
            $this->setRoute('store');
            $this->assertStore(
                $value['send_data'],
                $value['test_data'] + ['deleted_at' => null]
            );
            $this->assertSaveConstrains();

            $this->setRoute('update', ['genre' => $this->getRequestId()]);
            $updateSendData = array_replace(
                $value['send_data'],
                ['name' => 'Updating name']
            );
            $updateTestData = array_replace(
                $value['test_data'],
                ['name' => 'Updating name']
            );
            $this->assertUpdate(
                $updateSendData,
                $updateTestData + ['deleted_at' => null]
            );
            $this->assertSaveConstrains();

            $model = $this->model()::find($this->getRequestId());
            $model->delete();
        }
    }

    public function testUuid4()
    {
        $this->setRoute('store');
        $this->assertStore(
            $this->sendData + $this->sendConstrains,
            $this->sendData
        );

        $this->assertIdIsUuid4($this->getRequestId());
    }

    public function testDestroy()
    {
        $this->setRoute('destroy', ['genre' => $this->getFactoryModel()->id]);
        $this->assertDestroy();
    }
    
}

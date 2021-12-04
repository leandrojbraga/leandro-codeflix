<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\Category;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Tests\Traits\FeatureHttpValidations;

class CategoryControllerTest extends TestCase
{   
    use DatabaseMigrations, FeatureHttpValidations;

    private $factoryModel;
    private $route;
    private $sendData;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setFactoryModel();
        $this->sendData = [ 'name' => 'Category name' ];
    }

    protected function model() {
        return Category::class;
    }

    protected function setFactoryModel() {
        $model = $this->model();
        $this->factoryModel = factory($model)->create();
    }

    protected function getFactoryModel() {
        return $this->factoryModel;
    }

    protected function setRoute(string $routeSuffix, array $params = []) {
        $routePrefix = "categories";
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
        $this->setRoute('show', ['category' => $this->getFactoryModel()->id]);
        $this->assertShow();

        $this->setRoute('show', ['category' => 0]);
        $this->assertShowNotFound();
    }

    public function testInvalidationData()
    {   
        // Test create
        $this->setRoute('store');
        $this->assertInvalidationDataByAttribute('POST');

        // Test update
        $this->setRoute('update', ['category' => $this->getFactoryModel()->id]);
        $this->assertInvalidationDataByAttribute('PUT');
    }
    
    public function assertInvalidationRequired($method) {   
        $data = [
            'name' => ''
        ];

        $this->assertInvalidationData(
            $method, $data, 'required'
        );
    }

    public function assertInvalidationLength($method) {
        $data = [
            'name' => str_repeat('C', 256)
        ];
        $attributeRuleReplaces = [
            'name' => [ 'max' => 255 ]
        ];

        $this->assertInvalidationData(
            $method, $data, 'max.string', $attributeRuleReplaces
        );


        $data = [
            'name' => 'C'
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
            'is_active' => 'c'
        ];

        $this->assertInvalidationData(
            $method, $data, 'boolean'
        );
    }

    public function assertInvalidationDataByAttribute($method)
    {   
        $this->assertInvalidationRequired($method);

        $this->assertInvalidationLength($method);

        $this->assertInvalidationBoolean($method);

        $this->assertMissingValidationDataNotRequired(
            $method,
            [],
            ['description', 'is_active']
        );
    }

    public function testSave()
    {
        $data = [
            [
                'send_data' => $this->sendData,
                'test_data' => $this->sendData + [ 'description' => null,
                                                'is_active' => true]
            ],
            [
                'send_data' => $this->sendData + ['description' => null],
                'test_data' => $this->sendData + ['description' => null]
            ],
            [
                'send_data' => $this->sendData + ['description' => 'test description'],
                'test_data' => $this->sendData + ['description' => 'test description']
            ],
            [
                'send_data' => $this->sendData + ['description' => 'test description'],
                'test_data' => $this->sendData + ['description' => 'test description']
            ],
            [
                'send_data' => $this->sendData + ['is_active' => false],
                'test_data' => $this->sendData + ['is_active' => false]
            ],
            [
                'send_data' => $this->sendData + ['is_active' => true],
                'test_data' => $this->sendData + ['is_active' => true]
            ],
        ];

        foreach($data as $key => $value) {
            $this->setRoute('store');
            $this->assertStore(
                $value['send_data'],
                $value['test_data'] + ['deleted_at' => null]
            );

            $this->setRoute('update', ['category' => $this->getRequestId()]);
            $update_data = array_replace(
                $value['send_data'],
                ['name' => 'Updating category']
            );
            $this->assertUpdate(
                $update_data,
                $update_data + ['deleted_at' => null]
            );

            $model = $this->model()::find($this->getRequestId());
            $model->delete();
        }
    }

    public function testUuid4()
    {
        $this->setRoute('store');
        $this->assertStore($this->sendData, $this->sendData);

        $this->assertIdIsUuid4($this->getRequestId());
    }

    public function testDestroy()
    {
        $this->setRoute('destroy', ['category' => $this->getFactoryModel()->id]);
        $this->assertDestroy();
    }
}

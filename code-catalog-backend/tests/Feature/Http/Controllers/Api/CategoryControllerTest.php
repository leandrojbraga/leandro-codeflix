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

    protected function setUp(): void
    {
        parent::setUp();
        $this->setFactoryModel();
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
    
    public function assertInvalidationDataByAttribute($method)
    {   
        $validateAttributes = [
            [   'attribute' => 'name',
                'content' => [],
                'validation' => [
                    'key' => 'required',
                    'replace' => []
                ]
            ],
            [   'attribute' => 'name',
                'content' => ['name' => str_repeat('C', 256)],
                'validation' => [
                    'key' => 'max.string',
                    'replace' => [ 'max' => 255 ]
                ]
            ],
            [   'attribute' => 'name',
                'content' => ['name' => 'C'],
                'validation' => [
                    'key' => 'min.string',
                    'replace' => [ 'min' => 3 ]
                ]
            ],
            [   'attribute' => 'is_active',
                'content' => ['name' => 'Category', 'is_active' => 'C'],
                'validation' => [
                    'key' => 'boolean',
                    'replace' => []
                ]
            ]
        ];
        foreach($validateAttributes as $validateAttribute) {
            $this->assertInvalidationData(
                $method,
                $validateAttribute['content'],
                $validateAttribute['attribute'],
                (object) $validateAttribute['validation']
            );
        }

        $this->assertMissingValidationDataNotRequired(
            $method,
            [],
            ['description', 'is_active']
        );
    }

    public function testStore()
    {
        $name = 'Category Test';
        $description = 'Category test description';
        
        $this->setRoute('store');

        // Validate a default create
        $data = [ 'name' => $name ];
        $this->assertStore(
            $data,
            $data + [
                'description' => null,
                'is_active' => true
            ],
            true
        );

        //Validate id is Uuid4
        $this->assertIdIsUuid4($this->getRequestId());

        // Validate description null
        $data = [
            'name' => $name,
            'description' => '',
        ];
        $this->assertStore(
            $data,
            [   
                'description' => null
            ]
        );

        // Validate description NOT null
        $data = [
            'name' => $name,
            'description' => $description
        ];
        $this->assertStore($data, $data);

        // Validate is_active false
        $data = [
            'name' => $name,
            'is_active' => false
        ];
        $this->assertStore($data, $data);

        // Validate is_active true
        $data = [
            'name' => $name,
            'is_active' => true
        ];
        $this->assertStore($data, $data);
    }

    public function testUpdate()
    {
        $this->setRoute('update', ['category' => $this->getFactoryModel()->id]);

        $this->assertUpdate(
            [   
                'name' => 'Category Test',
                'description' => 'Category test description',
                'is_active' => false
            ]
        );

        $this->assertUpdate(
            [   
                'name' => 'Category',
                'description' => null
            ]
        );
    }

    public function testDestroy()
    {
        $this->setRoute('destroy', ['category' => $this->getFactoryModel()->id]);
        $this->assertDestroy();
    }
}

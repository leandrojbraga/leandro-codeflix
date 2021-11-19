<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\Genre;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Tests\Traits\FeatureHttpValidations;

class GenreControllerTest extends TestCase
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
        return Genre::class;
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
                'content' => ['name' => str_repeat('G', 256)],
                'validation' => [
                    'key' => 'max.string',
                    'replace' => [ 'max' => 255 ]
                ]
            ],
            [   'attribute' => 'is_active',
                'content' => ['name' => 'Genre', 'is_active' => 'C'],
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
            ['is_active']
        );
    }

    public function testStore()
    {
        $name = 'Genre Test';
        
        $this->setRoute('store');

        // Validate a default create
        $data = [ 'name' => $name ];
        $this->assertStore(
            $data,
            $data + [
                'is_active' => true
            ],
            true
        );

        //Validate id is Uuid4
        $this->assertIdIsUuid4($this->getRequestId());

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
        $this->setRoute('update', ['genre' => $this->getFactoryModel()->id]);

        $this->assertUpdate(
            [   
                'name' => 'Genre Test',
                'is_active' => false
            ]
        );
    }

    public function testDestroy()
    {
        $this->setRoute('destroy', ['genre' => $this->getFactoryModel()->id]);
        $this->assertDestroy();
    }
    
}

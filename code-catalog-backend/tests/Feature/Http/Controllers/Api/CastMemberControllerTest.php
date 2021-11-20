<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\CastMember;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Tests\Traits\FeatureHttpValidations;

class CastMemberControllerTest extends TestCase
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
        return CastMember::class;
    }

    protected function setFactoryModel() {
        $model = $this->model();
        $this->factoryModel = factory($model)->create();
    }

    protected function getFactoryModel() {
        return $this->factoryModel;
    }

    protected function setRoute(string $routeSuffix, array $params = []) {
        $routePrefix = "cast-members";
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
        $this->setRoute('show', ['cast_member' => $this->getFactoryModel()->id]);
        $this->assertShow();

        $this->setRoute('show', ['cast_member' => 0]);
        $this->assertShowNotFound();
    }
    
    public function testInvalidationData()
    {   
        // Test create
        $this->setRoute('store');
        $this->assertInvalidationDataByAttribute('POST');

        // Test update
        $this->setRoute('update', ['cast_member' => $this->getFactoryModel()->id]);
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
                'content' => ['name' => str_repeat('CM', 256)],
                'validation' => [
                    'key' => 'max.string',
                    'replace' => [ 'max' => 255 ]
                ]
            ],
            [   'attribute' => 'name',
                'content' => ['name' => 'CM'],
                'validation' => [
                    'key' => 'min.string',
                    'replace' => [ 'min' => 3 ]
                ]
            ],
            [   'attribute' => 'type',
                'content' => [],
                'validation' => [
                    'key' => 'required',
                    'replace' => []
                ]
            ],
            [   'attribute' => 'type',
                'content' => ['type' => 0],
                'validation' => [
                    'key' => 'not_in',
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
    }

    public function testStore()
    {
        $name = 'CastMember Test';
        $type = 1;
        
        $this->setRoute('store');

        // Validate a default create
        $data = [ 
            'name' => $name,
            'type' => $type
        ];
        $this->assertStore(
            $data,
            $data
        );

        //Validate id is Uuid4
        $this->assertIdIsUuid4($this->getRequestId());
    }
    
    public function testUpdate()
    {
        $this->setRoute('update', ['cast_member' => $this->getFactoryModel()->id]);

        $this->assertUpdate(
            [   
                'name' => 'CastMember Test',
                'type' => 2
            ]
        );
    }

    public function testDestroy()
    {
        $this->setRoute('destroy', ['cast_member' => $this->getFactoryModel()->id]);
        $this->assertDestroy();
    }
    
}

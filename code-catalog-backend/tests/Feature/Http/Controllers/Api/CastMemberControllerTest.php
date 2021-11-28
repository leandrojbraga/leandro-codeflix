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
    private $sendData;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setFactoryModel();
        $this->sendData = [ 
            'name' => 'CastMember name',
            'type' => CastMember::TYPE_DIRECTOR
        ];
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

    public function assertInvalidationRequired($method)
    {   
        $data = [
            'name' => '',
            'type' => '',
        ];

        $this->assertInvalidationData(
            $method, $data, 'required'
        );
    }

    public function assertInvalidationLength($method){
        $data = [
            'name' => str_repeat('CM', 256)
        ];
        $attributeRuleReplaces = [
            'name' => [ 'max' => 255 ]
        ];

        $this->assertInvalidationData(
            $method, $data, 'max.string', $attributeRuleReplaces
        );


        $data = [
            'name' => 'CM'
        ];
        $attributeRuleReplaces = [
            'name' => [ 'min' => 3 ]
        ];

        $this->assertInvalidationData(
            $method, $data, 'min.string', $attributeRuleReplaces
        );
    }

    public function assertInvalidationInList($method){
        $data = [
            'type' => 0
        ];

        $this->assertInvalidationData(
            $method, $data, 'in'
        );
    }

    public function assertInvalidationDataByAttribute($method)
    {   
        $this->assertInvalidationRequired($method);

        $this->assertInvalidationLength($method);

        $this->assertInvalidationInList($method);
    }

    public function testSave()
    {
        $data = [
            [
                'send_data' => $this->sendData,
                'test_data' => $this->sendData
            ],
            [
                'send_data' => array_replace(
                                    $this->sendData,
                                    ['type' => CastMember::TYPE_ACTOR]
                                ),
                'test_data' => array_replace(
                                    $this->sendData,
                                    ['type' => CastMember::TYPE_ACTOR]
                                )
            ]
        ];

        foreach($data as $key => $value) {
            $this->setRoute('store');
            $this->assertStore(
                $value['send_data'],
                $value['test_data'] + ['deleted_at' => null]
            );

            $this->setRoute('update', ['cast_member' => $this->getRequestId()]);
            $update_data = array_replace(
                $value['send_data'], ['name' => 'Updating cast member']
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
        $this->setRoute('destroy', ['cast_member' => $this->getFactoryModel()->id]);
        $this->assertDestroy();
    }
    
}

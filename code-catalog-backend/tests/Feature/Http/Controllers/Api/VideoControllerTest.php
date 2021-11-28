<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\Video;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Tests\Traits\FeatureHttpValidations;

class VideoControllerTest extends TestCase
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
            'title' => 'Video title',
            'description' => 'Video description',
            'year_launched' => 2020,
            'rating' => Video::RATINGS[0],
            'duration' => 90
        ];
    }

    protected function model() {
        return Video::class;
    }

    protected function setFactoryModel() {
        $model = $this->model();
        $this->factoryModel = factory($model)->create();
    }

    protected function getFactoryModel() {
        return $this->factoryModel;
    }

    protected function setRoute(string $routeSuffix, array $params = []) {
        $routePrefix = "videos";
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
        $this->setRoute('show', ['video' => $this->getFactoryModel()->id]);
        $this->assertShow();

        $this->setRoute('show', ['video' => 0]);
        $this->assertShowNotFound();
    }

    public function testInvalidationData()
    {   
        // Test create
        $this->setRoute('store');
        $this->assertInvalidationDataByAttribute('POST');

        // Test update
        $this->setRoute('update', ['video' => $this->getFactoryModel()->id]);
        $this->assertInvalidationDataByAttribute('PUT');
    }
    
    public function assertInvalidationRequired($method)
    {   
        $data = [
            'title' => '',
            'description' => '',
            'year_launched' => '',
            'rating' => '',
            'duration' => ''
        ];

        $this->assertInvalidationData(
            $method, $data, 'required'
        );
    }
    
    public function assertInvalidationLength($method){
        $data = [
            'title' => str_repeat('V', 256)
        ];
        $attributeRuleReplaces = [
            'title' => [ 'max' => 255 ]
        ];

        $this->assertInvalidationData(
            $method, $data, 'max.string', $attributeRuleReplaces
        );


        $data = [
            'title' => 'V',
            'description' => 'Vídeo'
        ];
        $attributeRuleReplaces = [
            'title' => [ 'min' => 3 ],
            'description' => [ 'min' => 10 ]
        ];

        $this->assertInvalidationData(
            $method, $data, 'min.string', $attributeRuleReplaces
        );
    }

    public function assertInvalidationBoolean($method){
        $data = [
            'opened' => 'v'
        ];

        $this->assertInvalidationData(
            $method, $data, 'boolean'
        );
    }

    public function assertInvalidationNumber($method){
        $data = [
            'duration' => 'video'
        ];

        $this->assertInvalidationData(
            $method, $data, 'integer'
        );
    }

    public function assertInvalidationDate($method){
        $data = [
            'year_launched' => 'video'
        ];
        $attributeRuleReplaces = [
            'year_launched' => [ 'format' => 'Y' ]
        ];

        $this->assertInvalidationData(
            $method, $data, 'date_format', $attributeRuleReplaces
        );
    }

    public function assertInvalidationInList($method){
        $data = [
            'rating' => 'A'
        ];

        $this->assertInvalidationData(
            $method, $data, 'in'
        );
    }
    
    public function assertInvalidationDataByAttribute($method)
    {   
        $this->assertInvalidationRequired($method);

        $this->assertInvalidationLength($method);
        
        $this->assertInvalidationBoolean($method);
        
        $this->assertInvalidationNumber($method);

        $this->assertInvalidationDate($method);

        $this->assertInvalidationInList($method);

        $this->assertMissingValidationDataNotRequired(
            $method, [], ['opened']
        );
    }

    public function testSave()
    {
        $data = [
            [
                'send_data' => $this->sendData,
                'test_data' => $this->sendData + ['opened' => false]
            ],
            [
                'send_data' =>  $this->sendData + ['opened' => false],
                'test_data' =>  $this->sendData + ['opened' => false]
            ],
            [
                'send_data' =>  $this->sendData + ['opened' => true],
                'test_data' =>  $this->sendData + ['opened' => true]
            ],
            [
                'send_data' =>  array_replace(
                                    $this->sendData,
                                    ['rating' => Video::RATINGS[3]]
                                ),
                'test_data' =>  array_replace(
                                    $this->sendData,
                                    ['rating' => Video::RATINGS[3], 'opened' => false]
                                )
            ]
        ];

        foreach($data as $key => $value) {
            $this->setRoute('store');
            $this->assertStore(
                $value['send_data'],
                $value['test_data'] + ['deleted_at' => null]
            );

            $this->setRoute('update', ['video' => $this->getRequestId()]);
            $update_data = array_replace(
                $value['send_data'],
                ['title' => 'Updating title']
            );
            $this->assertUpdate(
                $update_data,
                $update_data + ['deleted_at' => null]
            );

            $this->model()::truncate();
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
        $this->setRoute('destroy', ['video' => $this->getFactoryModel()->id]);
        $this->assertDestroy();
    }
}

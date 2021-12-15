<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Http\Controllers\Api\VideoController;
use App\Models\Category;
use App\Models\ContentDescriptor;
use App\Models\Genre;
use App\Models\Video;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\Exceptions\TestException;
use Tests\TestCase;
use Tests\Traits\FeatureHttpValidations;
use Illuminate\Http\Request;

class VideoControllerTest extends TestCase
{   
    use DatabaseMigrations, FeatureHttpValidations;

    private $factoryModel;
    private $route;
    private $sendData;
    private $factoryCategory;
    private $factoryGenre;
    private $factoryContentDescriptor;
    private $sendConstrains;

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
        
        $this->factoryCategory = factory(Category::class)->create();
        $this->factoryGenre = factory(Genre::class)->create();
        $this->factoryGenre->categories()->sync([$this->factoryCategory->id]);
        $this->factoryContentDescriptor = factory(ContentDescriptor::class)->create();

        $this->sendConstrains = [
            'categories_id' => [$this->factoryCategory->id],
            'genres_id' => [$this->factoryGenre->id],
            'content_descriptors_id' => [$this->factoryContentDescriptor->id]
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
            'duration' => '',
            'categories_id' => '',
            'genres_id' => ''
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

    public function assertInvalidationArray($method){
        $data = [
            'categories_id' => 'test',
            'genres_id' => 'test',
            'content_descriptors_id' => 'test'
        ];

        $this->assertInvalidationData(
            $method, $data, 'array'
        );
    }

    public function assertInvalidationConstraintsExists($method){
        $data = [
            'categories_id' => ['test'],
            'genres_id' => ['test'],
            'content_descriptors_id' => ['test']
        ];

        $this->assertInvalidationData(
            $method, $data, 'exists'
        );

        $category = factory(Category::class)->create();
        $category->delete();
        $genre = factory(Genre::class)->create();
        $genre->delete();
        $contentDescriptor = factory(ContentDescriptor::class)->create();
        $contentDescriptor->delete();
        $data = [
            'categories_id' => [$category->id],
            'genres_id' => [$genre->id],
            'content_descriptors_id' => [$contentDescriptor->id]
        ];

        $this->assertInvalidationData(
            $method, $data, 'exists'
        );
    }

    public function assertInvalidationRelatedExists($method){
        $categoryId = factory(Category::class)->create()->id;
        $genreId = factory(Genre::class)->create()->id;
        
        $data = [
            'categories_id' => [$categoryId],
            'genres_id' => [$genreId]
        ];

        $this->assertInvalidationDataRequest($method, $data);

        $this->response
        ->assertJsonValidationErrors(['genres_id'])
        ->assertJsonFragment([ 
            \Lang::get(
                'validation.related_attribute', 
                ['attribute' => 'genres id', 'relationship' => 'category id'])   
        ]);
    }
    
    public function assertInvalidationDataByAttribute($method)
    {   
        $this->assertInvalidationRequired($method);

        $this->assertInvalidationLength($method);
        
        $this->assertInvalidationBoolean($method);
        
        $this->assertInvalidationNumber($method);

        $this->assertInvalidationDate($method);

        $this->assertInvalidationInList($method);

        $this->assertInvalidationArray($method);

        $this->assertInvalidationConstraintsExists($method);

        $this->assertInvalidationRelatedExists($method);

        $this->assertMissingValidationDataNotRequired(
            $method, [], ['opened']
        );
    }

    public function assertSaveConstrains() {       
        $video = $this->model()::find($this->getRequestId());

        $this->assertCount(1,$video->categories);
        $this->assertDatabaseHas('category_video',
            [
                'video_id' => $video->id,
                'category_id' => $this->factoryCategory->id,
            ]
        );

        $this->assertCount(1,$video->genres);
        $this->assertDatabaseHas('genre_video',
            [
                'video_id' => $video->id,
                'genre_id' => $this->factoryGenre->id,
            ]
        );

        $this->assertCount(1,$video->genres);
        $this->assertDatabaseHas('content_descriptor_video',
            [
                'video_id' => $video->id,
                'content_descriptor_id' => $this->factoryContentDescriptor->id,
            ]
        );
    }

    public function testSave()
    {   
        $data = [
            [
                'send_data' => $this->sendData + $this->sendConstrains,
                'test_data' => $this->sendData + ['opened' => false]
            ],
            [
                'send_data' =>  $this->sendData + 
                                $this->sendConstrains + ['opened' => false],
                'test_data' =>  $this->sendData + ['opened' => false]
            ],
            [
                'send_data' =>  $this->sendData +
                                $this->sendConstrains + ['opened' => true],
                'test_data' =>  $this->sendData + ['opened' => true]
            ],
            [
                'send_data' =>  array_replace(
                                    $this->sendData + $this->sendConstrains,
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
            $this->assertSaveConstrains();

            $this->setRoute('update', ['video' => $this->getRequestId()]);
            $updateSendData = array_replace(
                $value['send_data'],
                ['title' => 'Updating title']
            );
            $updateTestData = array_replace(
                $value['test_data'],
                ['title' => 'Updating title']
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
            $this->sendData);

        $this->assertIdIsUuid4($this->getRequestId());
    }

    private function getFactoryConstrainsToTestSync() {
        $categoriesId = factory(Category::class, 3)
            ->create()->pluck('id')->toArray();
        
        $genresId = factory(Genre::class, 3)->create()
            ->each(function(Genre $genre) use ($categoriesId) {
              $genre->categories()->sync($categoriesId);
            })
            ->pluck('id')->toArray();

        
        $contentDescriptorsId = factory(ContentDescriptor::class, 3)
            ->create()->pluck('id')->toArray();

        return [$categoriesId, $genresId, $contentDescriptorsId];
    }

    public function testSyncConstrains() {
        
        list($categoriesId, $genresId,
            $contentDescriptorsId) = $this->getFactoryConstrainsToTestSync();

        $this->setRoute('store');
        $this->sendRequest(
            'POST',
            $this->sendData + [
                'categories_id' => [$categoriesId[0]],
                'genres_id' => [$genresId[0]],
                'content_descriptors_id' => [$contentDescriptorsId[0]]
            ]
        );

        $this->assertDatabaseHas('category_video',
            [
                'video_id' => $this->getRequestId(),
                'category_id' => $categoriesId[0]
            ]
        );
        $this->assertDatabaseHas('genre_video',
            [
                'video_id' => $this->getRequestId(),
                'genre_id' => $genresId[0],
            ]
        );
        $this->assertDatabaseHas('content_descriptor_video',
            [
                'video_id' => $this->getRequestId(),
                'content_descriptor_id' => $contentDescriptorsId[0]
            ]
        );


        $this->setRoute('update', ['video' => $this->getRequestId()]);
        $this->sendRequest(
            'PUT',
            $this->sendData + [
                'categories_id' => array_slice($categoriesId,1),
                'genres_id' => array_slice($genresId,1),
                'content_descriptors_id' => array_slice($contentDescriptorsId,1)
            ]
        );

        $this->assertDatabaseMissing('category_video',
            [
                'video_id' => $this->getRequestId(),
                'category_id' => $categoriesId[0]
            ]
        );
        $this->assertDatabaseMissing('genre_video',
            [
                'video_id' => $this->getRequestId(),
                'genre_id' => $genresId[0],
            ]
        );
        $this->assertDatabaseMissing('content_descriptor_video',
            [
                'video_id' => $this->getRequestId(),
                'content_descriptor_id' => $contentDescriptorsId[0]
            ]
        );


        $this->assertDatabaseHas('category_video',
            [
                'video_id' => $this->getRequestId(),
                'category_id' => $categoriesId[1]
            ]
        );
        $this->assertDatabaseHas('category_video',
            [
                'video_id' => $this->getRequestId(),
                'category_id' => $categoriesId[2]
            ]
        );

        $this->assertDatabaseHas('genre_video',
            [
                'video_id' => $this->getRequestId(),
                'genre_id' => $genresId[1],
            ]
        );
        $this->assertDatabaseHas('genre_video',
            [
                'video_id' => $this->getRequestId(),
                'genre_id' => $genresId[2],
            ]
        );

        $this->assertDatabaseHas('content_descriptor_video',
            [
                'video_id' => $this->getRequestId(),
                'content_descriptor_id' => $contentDescriptorsId[1]
            ]
        );
        $this->assertDatabaseHas('content_descriptor_video',
            [
                'video_id' => $this->getRequestId(),
                'content_descriptor_id' => $contentDescriptorsId[2]
            ]
        );
    }

    private function mockVideoController() {
        $controller = \Mockery::mock(VideoController::class)
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();
        
        $controller
            ->shouldReceive('findOrFail')
            ->withAnyArgs()
            ->andReturn($this->getFactoryModel());

        $controller
            ->shouldReceive('validationRules')
            ->withAnyArgs()
            ->andReturn([]);

        $controller
            ->shouldReceive('validateRequestData')
            ->withAnyArgs()
            ->andReturn($this->sendData);
      

        $controller
            ->shouldReceive('handleRelations')
            ->once()
            ->andThrow(new TestException());
        
        return $controller;
    }

    public function testRollback()
    {
        $request = \Mockery::mock(Request::class);

        $controller =  $this->mockVideoController();
        $hasError = false;
        try {
            $controller->store($request);
        } catch (TestException $exception) {
            $this->assertCount(1, Video::all());
            $hasError = true;
        }
        $this->assertTrue($hasError);
        
        $id = $this->getFactoryModel()->id;
        $updatedAt = $this->getFactoryModel()->updated_at;
        
        $controller =  $this->mockVideoController();
        $hasError = false;
        try {
            $controller->update($request, $id);
        } catch (TestException $exception) {
            $this->assertEquals(
                $updatedAt,
                Video::find($id)->updated_at);
            $hasError = true;
        }
        $this->assertTrue($hasError);
    }

    public function testDestroy()
    {
        $this->setRoute('destroy', ['video' => $this->getFactoryModel()->id]);
        $this->assertDestroy();
    }
}

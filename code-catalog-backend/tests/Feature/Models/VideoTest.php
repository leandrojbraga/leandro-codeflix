<?php

namespace Tests\Feature\Models;

use App\Models\Category;
use App\Models\ContentDescriptor;
use App\Models\Genre;
use App\Models\Video;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use Tests\Traits\FeatureModelsValidations;

class VideoTest extends TestCase
{   
    use DatabaseMigrations, FeatureModelsValidations;

    private $sendData;

    protected function setUp(): void
    {
        parent::setUp();
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

    public function testList()
    {   
        $this->assertList();
    }

    public function testAllAttributes()
    {   
        $this->assertAttributes(
            [
                'id', 'title', 'description', 'year_launched',
                'opened', 'rating', 'duration', 'movie_file',
                'created_at', 'updated_at', 'deleted_at'
            ]
        );
    }

    public function testSaveBasic()
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
                                    ['rating' => Video::RATINGS[3],
                                        'opened' => false]
                                )
            ]
        ];

        foreach($data as $key => $value) {
            $this->assertCreate(
                $value['send_data'],
                $value['test_data'] + ['deleted_at' => null]
            );

            $update_data = array_replace(
                $value['send_data'],
                ['title' => 'Updating title']
            );
            $this->assertEdit(
                $update_data,
                $update_data + ['deleted_at' => null]
            );

            
            $model = $this->model()::all()->first();
            $model->delete();
        }
    }

    private function getFactoryToTestSaveRelations() {
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

    private function assertDatabaseRelations($table, $videoId, $relationKey, $relationsId, $missing = False) {
        foreach ($relationsId as $relationId) {
            $data = [
                'video_id' => $videoId,
                $relationKey => $relationId
            ];

            if ($missing) {
                $this->assertDatabaseMissing($table, $data);
            } else {
                $this->assertDatabaseHas($table, $data);
            }
            
        }
    }

    public function testSaveRelations() {
        list($categoriesId, $genresId,
            $contentDescriptorsId) = $this->getFactoryToTestSaveRelations();

        $video = $this->getModelCreated(
            $this->sendData + [
                'categories_id' => [$categoriesId[0]],
                'genres_id' => [$genresId[0]],
                'content_descriptors_id' => [$contentDescriptorsId[0]]
            ]
        );


        $this->assertDatabaseRelations(
            'category_video', $video->id, 'category_id', [$categoriesId[0]]
        );

        $this->assertDatabaseRelations(
            'genre_video', $video->id, 'genre_id', [$genresId[0]]
        );

        $this->assertDatabaseRelations(
            'content_descriptor_video', $video->id, 'content_descriptor_id', [$contentDescriptorsId[0]]
        );

        $video->update(
            $this->sendData + [
                'categories_id' => array_slice($categoriesId,1),
                'genres_id' => array_slice($genresId,1),
                'content_descriptors_id' => array_slice($contentDescriptorsId,1)
            ]
        );
        
        $this->assertDatabaseRelations(
            'category_video', $video->id, 'category_id', [$categoriesId[0]], True
        );
        $this->assertDatabaseRelations(
            'category_video', $video->id, 'category_id', array_slice($categoriesId,1)
        );

        $this->assertDatabaseRelations(
            'genre_video', $video->id, 'genre_id', [$genresId[0]], True
        );
        $this->assertDatabaseRelations(
            'genre_video', $video->id, 'genre_id', array_slice($genresId,1)
        );

        $this->assertDatabaseRelations(
            'content_descriptor_video', $video->id, 'content_descriptor_id', [$contentDescriptorsId[0]],True
        );
        $this->assertDatabaseRelations(
            'content_descriptor_video', $video->id, 'content_descriptor_id', array_slice($contentDescriptorsId,1)
        );
    }

    public function testUuid4()
    {
        $this->assertIdIsUuid4(
            $this->getModelCreated($this->sendData)->id
        );
    }

    public function testHandleRelations() {
        $video = factory(Video::class)->create();
        
        Video::handleRelations($video, []);
        $video->refresh();
        $this->assertCount(0, $video->categories);
        $this->assertCount(0, $video->genres);
        $this->assertCount(0, $video->content_descriptors);

        $category = factory(Category::class)->create();
        Video::handleRelations($video, [
            'categories_id' => [$category->id]
        ]);
        $video->refresh();
        $this->assertCount(1, $video->categories);
        $this->assertCount(0, $video->genres);
        $this->assertCount(0, $video->content_descriptors);

        $genre = factory(Genre::class)->create();
        Video::handleRelations($video, [
            'genres_id' => [$genre->id]
        ]);
        $video->refresh();
        $this->assertCount(1, $video->categories);
        $this->assertCount(1, $video->genres);
        $this->assertCount(0, $video->content_descriptors);

        $content_descriptor = factory(ContentDescriptor::class)->create();
        Video::handleRelations($video, [
            'content_descriptors_id' => [$content_descriptor->id]
        ]);
        $video->refresh();
        $this->assertCount(1, $video->categories);
        $this->assertCount(1, $video->genres);
        $this->assertCount(1, $video->content_descriptors);

        $video->categories()->sync([]);
        $video->genres()->sync([]);
        $video->content_descriptors()->sync([]);
        $video->refresh();
        $this->assertCount(0, $video->categories);
        $this->assertCount(0, $video->genres);
        $this->assertCount(0, $video->content_descriptors);

        Video::handleRelations($video, [
            'categories_id' => [$category->id],
            'genres_id' => [$genre->id],
            'content_descriptors_id' => [$content_descriptor->id]
        ]);
        $video->refresh();
        $this->assertCount(1, $video->categories);
        $this->assertCount(1, $video->genres);
        $this->assertCount(1, $video->content_descriptors);
    }

    public function testUploadFile() {
        Storage::fake();
        $file = UploadedFile::fake()->create('video.mp4');

        $model = $this->getModelCreated(
            $this->sendData + ['movie_file' => $file]
        );
        
        Storage::assertExists("{$model->id}/{$file->hashName()}");
    }

    public function testRollbackCreate()
    {
        $hasError = false;
        try {
            $this->getModelCreated(
                $this->sendData  + ['categories_id' => [0, 1]]
            );
        } catch (QueryException $exception) {
            $this->assertCount(0, Video::all());
            $hasError = true;
        }
        $this->assertTrue($hasError);

        $hasError = false;
        try {
            $this->getModelCreated(
                $this->sendData  + ['genres_id' => [0, 1]]
            );
        } catch (QueryException $exception) {
            $this->assertCount(0, Video::all());
            $hasError = true;
        }
        $this->assertTrue($hasError);

        $hasError = false;
        try {
            $this->getModelCreated(
                $this->sendData  + ['content_descriptors_id' => [0, 1]]
            );
        } catch (QueryException $exception) {
            $this->assertCount(0, Video::all());
            $hasError = true;
        }
        $this->assertTrue($hasError);
    }

    public function testRollbackUpdate()
    {   
        $video = $this->getModelCreated($this->sendData);
        $id = $video->id;
        $updatedAt = $video->updated_at;

        $hasError = false;
        try {
            $video->update(
                $this->sendData  + ['categories_id' => [0, 1]]
            );
        } catch (QueryException $exception) {
            $this->assertEquals(
                $updatedAt,
                Video::find($id)->updated_at);
            $hasError = true;
        }
        $this->assertTrue($hasError);

        $hasError = false;
        try {
            $video->update(
                $this->sendData  + ['genres_id' => [0, 1]]
            );
        } catch (QueryException $exception) {
            $this->assertEquals(
                $updatedAt,
                Video::find($id)->updated_at);
            $hasError = true;
        }
        $this->assertTrue($hasError);

        $hasError = false;
        try {
            $video->update(
                $this->sendData  + ['content_descriptors_id' => [0, 1]]
            );
        } catch (QueryException $exception) {
            $this->assertEquals(
                $updatedAt,
                Video::find($id)->updated_at);
            $hasError = true;
        }
        $this->assertTrue($hasError);
    }

    public function testSoftDelete() {
        $modelCreated = $this->getModelCreated(
            $this->sendData + ['is_active' => true]
        );

        $this->assertSoftDelete($modelCreated->id);
    }
}

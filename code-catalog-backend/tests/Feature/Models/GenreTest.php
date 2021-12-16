<?php

namespace Tests\Feature\Models;

use App\Models\Category;
use App\Models\Genre;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Tests\Traits\FeatureModelsValidations;

class GenreTest extends TestCase
{   
    use DatabaseMigrations, FeatureModelsValidations;

    private $sendData;

    protected function setUp(): void
    {
        parent::setUp();
        $this->sendData = [ 'name' => 'Genre Test' ];
    }

    protected function model() {
       return Genre::class;
    }

    public function testList()
    {   
        $this->assertList();
    }

    public function testAllAttributes()
    {   
        $this->assertAttributes(
            [
                'id', 'name', 'is_active',
                'created_at', 'updated_at', 'deleted_at'
            ]
        );
    }

    public function testSaveBasic()
    {
        $data = [
            [
                'send_data' => $this->sendData,
                'test_data' => $this->sendData + ['is_active' => true]
            ],
            [
                'send_data' => $this->sendData + ['is_active' => false],
                'test_data' => $this->sendData + ['is_active' => false]
            ],
            [
                'send_data' => $this->sendData + ['is_active' => true],
                'test_data' => $this->sendData + ['is_active' => true]
            ]
        ];

        foreach($data as $key => $value) {
            $this->assertCreate(
                $value['send_data'],
                $value['test_data'] + ['deleted_at' => null]
            );

            $update_data = array_replace(
                $value['send_data'],
                ['name' => 'Updating name']
            );
            $this->assertEdit(
                $update_data,
                $update_data + ['deleted_at' => null]
            );

            $model = $this->model()::all()->first();
            $model->delete();
        }
    }

    private function assertDatabaseCategoryGenre($genreId, $categoriesId, $missing = False) {
        foreach ($categoriesId as $categoryId) {
            $table = 'category_genre';
            $data = [
                'genre_id' => $genreId,
                'category_id' => $categoryId
            ];

            if ($missing) {
                $this->assertDatabaseMissing($table, $data);
            } else {
                $this->assertDatabaseHas($table, $data);
            }
            
        }
    }

    public function testSaveRelations() {
        $categoriesId = factory(Category::class, 3)
            ->create()->pluck('id')->toArray();

        $genre = $this->getModelCreated(
            $this->sendData + [
                'categories_id' => [$categoriesId[0]]
            ]
        );

        $this->assertDatabaseCategoryGenre(
            $genre->id, [$categoriesId[0]]
        );

        $genre->update(
            $this->sendData + [
                'categories_id' => array_slice($categoriesId,1)
            ]
        );
        
        $this->assertDatabaseCategoryGenre(
            $genre->id, [$categoriesId[0]], True
        );

        $this->assertDatabaseCategoryGenre(
            $genre->id, array_slice($categoriesId,1)
        );
    }

    public function testUuid4()
    {
        $this->assertIdIsUuid4(
            $this->getModelCreated($this->sendData)->id
        );
    }

    public function testHandleRelations() {
        $genre = factory(Genre::class)->create();
        
        Genre::handleRelations($genre, []);
        $genre->refresh();
        $this->assertCount(0, $genre->categories);

        $category = factory(Category::class)->create();
        Genre::handleRelations($genre, [
            'categories_id' => [$category->id]
        ]);
        $genre->refresh();
        $this->assertCount(1, $genre->categories);
    }

    public function testRollbackCreate()
    {
        $hasError = false;
        try {
            $this->getModelCreated(
                $this->sendData + ['categories_id' => [0, 1]]
            );
        } catch (QueryException $exception) {
            $this->assertCount(0, Genre::all());
            $hasError = true;
        }
        $this->assertTrue($hasError);
    }

    public function testRollbackUpdate()
    {   
        $genre = $this->getModelCreated($this->sendData);
        $id = $genre->id;
        $updatedAt = $genre->updated_at;

        $hasError = false;
        try {
            $genre->update(
                $this->sendData + ['categories_id' => [0, 1]]
            );
        } catch (QueryException $exception) {
            $this->assertEquals(
                $updatedAt,
                Genre::find($id)->updated_at);
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

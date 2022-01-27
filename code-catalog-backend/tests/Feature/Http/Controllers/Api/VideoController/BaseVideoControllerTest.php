<?php

namespace Tests\Feature\Http\Controllers\Api\VideoController;

use App\Models\Category;
use App\Models\ContentDescriptor;
use App\Models\Genre;
use App\Models\Video;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Tests\Traits\FeatureHttpValidations;

abstract class BaseVideoControllerTest extends TestCase
{   
    use DatabaseMigrations, FeatureHttpValidations;

    protected $factoryModel;
    protected $route;
    protected $sendData;
    protected $factoryCategory;
    protected $factoryGenre;
    protected $factoryContentDescriptor;
    protected $sendConstrains;

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
}

<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\Genre;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Lang;

class GenreControllerTest extends TestController
{   
    private $routePrefix = "genres";

    protected function setUp(): void
    {
        parent::setUp();
        $this->class = Genre::class;
        $this->model = factory($this->class)->create();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }

    public function testIndex()
    {
        $this->validateIndex(
            route($this->routePrefix.'.index')
        );
    }

    public function testShow()
    {
        $this->validateShow(
            route(
                $this->routePrefix.'.show',
                ['genre' => $this->model->id]
            )
        );

        $this->validateShowNotFound(
            route(
                $this->routePrefix.'.show',
                ['genre' => 0]
            )
        );
    }

    public function testInvalidationData()
    {   
        // Test create
        $this->assertInvalidationData(
            'POST',
            route($this->routePrefix.'.store')
        );

        // Test update
        $this->assertInvalidationData(
            'PUT',
            route($this->routePrefix.'.update', ['genre' => $this->model->id])
        );
    }
    public function assertInvalidationData($method, $route)
    {   
        // Validate name required
        $requiredAttributes = ['name'];
        
        foreach ($requiredAttributes as $attribute) {
            $this->validateInvalidationData(
                $method,
                $route,
                [],
                [ $attribute ],
                [ Lang::get('validation.required', ['attribute' => $attribute]) ]
            );
        }

        // Validate name max length
        $validateAttributes = collect([
            'name' => (object)[
                'content' => ['name' => str_repeat('C', 256)],
                'error' => 'validation.max.string',
                'msgReplace' => [
                        'attribute' => 'name',
                        'max' => 255
                    ]
                ],
            'is_active' => (object)[
                'content' => ['name' => 'Genre', 'is_active' => 'C'],
                'error' => 'validation.boolean',
                'msgReplace' => [
                        'attribute' => 'is active'
                    ]
                ],
        ]);

        foreach($validateAttributes as $key => $lang) {
            $this->validateInvalidationData(
                $method,
                $route,
                $lang->content,
                [ $key ],
                [ Lang::get($lang->error, $lang->msgReplace)]
            );
        }

        // Validate not required attributes missing validation errors 
        $this->validateInvalidationDataNotRequired(
            $method,
            $route,
            [],
            ['is_active']
        );
    }

    public function testStore()
    {
        $name = 'Genre Test';
                
        // Validate a default create          
        $this->validateStore(
            route($this->routePrefix.'.store'),
            [
                'name' => $name
            ],
            [   
                'name' => $name,
                'is_active' => true
            ],
            true
        );

        // Validate is_active false
        $this->validateStore(
            route($this->routePrefix.'.store'),
            [
                'name' => $name,
                'is_active' => false
            ],
            [   
                'is_active' => false
            ]
        );

        // Validate is_active true
        $this->validateStore(
            route($this->routePrefix.'.store'),
            [
                'name' => $name,
                'is_active' => true
            ],
            [   
                'is_active' => true
            ]
        );
    }

    public function testUpdate()
    {     
        $this->validateUpdate(
            route($this->routePrefix.'.update', ['genre' => $this->model->id]),
            [   
                'name' => 'Genre Test',
                'is_active' => false
            ]
        );
    }

    public function testDestroy()
    {     
        $this->validateDestroy(
            route($this->routePrefix.'.destroy', ['genre' => $this->model->id])
        );
    }
    
}

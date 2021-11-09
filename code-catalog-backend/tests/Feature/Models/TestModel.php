<?php

namespace Tests\Feature\Models;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Ramsey\Uuid\Uuid;
use Tests\TestCase;

class TestModel extends TestCase
{   
    use DatabaseMigrations;

    protected $table;

    public function validateList()
    {   
        $model = factory($this->table, 1)->create();
        $modelList = $this->table::all();

        $this->assertCount(1, $modelList);
    }

    public function validateAllAttributes($attributes)
    {   
        $model = factory($this->table, 1)->create();
        $modelList = $this->table::all();
        $modelAttrs = array_keys($modelList->first()->getAttributes());

        $this->assertEqualsCanonicalizing($attributes, $modelAttrs);
    }

    public function validateCreate($data, $fieldsValidade, $validateId = False) {        
        $model = $this->table::create($data);
        $model->refresh();

        foreach ($fieldsValidade as $key => $value) {
            $this->assertEquals($value, $model->{$key});
        }
        
        if ($validateId) {
            $this->validateIdisUuid4($model->id);
        }
    }

    public function validateIdisUuid4($id) {        
        $this->assertTrue(Uuid::isValid($id));
        $this->assertEquals(
            Uuid::fromString($id)->getVersion(), 
            Uuid::UUID_TYPE_RANDOM
        );
    }

    public function validateEdit($oldData, $newData) {        
        $model = $this->table::create($oldData);
        
        $model->update($newData);

        foreach ($newData as $key => $value) {
            $this->assertEquals($value, $model->{$key});
        }        
    }

    public function validateSoftDelete($data) {       
        $modelCreated = $this->table::create($data);
        $modelCreated->refresh();

        $modelId = $modelCreated->id;

        $modelToDelete = $this->table::find($modelId);
        $deleted = $modelToDelete->delete();
        $this->assertTrue($deleted);
        
        $this->assertNull($this->table::find($modelToDelete->id));

        $this->assertNotNull($this->table::onlyTrashed()->find($modelToDelete->id));

        $modelToDelete->restore();
        $this->assertNotNull($this->table::find($modelToDelete->id));

    }
    
}
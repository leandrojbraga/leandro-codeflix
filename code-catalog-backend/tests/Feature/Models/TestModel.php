<?php

namespace Tests\Feature\Models;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
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

    public function validateCreate($data, $fieldsValidade) {        
        $model = $this->table::create($data);
        $model->refresh();

        foreach ($fieldsValidade as $key => $value) {
            $this->assertEquals($value, $model->{$key});
        }
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

        $modelDeleted = $this->table::onlyTrashed()->find($modelId);

        $this->assertEquals($modelId, $modelDeleted->id);
    }
    
}
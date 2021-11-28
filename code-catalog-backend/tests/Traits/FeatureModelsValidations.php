<?php

namespace Tests\Traits;

trait FeatureModelsValidations
{   
    use UuidValidations, DatabaseValidations;

    protected abstract function model();

    public function factoryCreateModel() {
        return factory($this->model(), 1)->create();
    }

    public function getFactoryListModel() {
        $this->factoryCreateModel();
        return $this->model()::all();
    }

    public function assertList()
    {   
        $modelList = $this->getFactoryListModel();
        $this->assertCount(1, $modelList);
    }

    public function assertAttributes(array $attributes)
    {   
        $modelList = $this->getFactoryListModel();
        $modelAttrs = array_keys($modelList->first()->getAttributes());

        $this->assertEqualsCanonicalizing($attributes, $modelAttrs);
    }

    public function getModelCreated(array $data) {
        $model = $this->model()::create($data);
        $model->refresh();
        return $model;
    }
    
    public function assertCreate(array $data, array $validadeData) {
        $model = $this->getModelCreated($data);

        $this->assertDatabaseData($validadeData);
    }  

    public function assertEdit(array $data, array $validadeData) {        
        $model = $this->model()::all()->first();
        
        $model->update($data);

        $this->assertDatabaseData($validadeData);
    }

    public function assertSoftDelete(string $id) {       
        $modelToDelete = $this->model()::find($id);
        $deleted = $modelToDelete->delete();
        $this->assertTrue($deleted);
        $this->assertNotNull($modelToDelete->deleted_at);
        $this->assertDatabaseData([]);
        $this->assertNotNull($this->model()::onlyTrashed()->find($id));

        $modelToDelete->restore();
        $this->assertNull($modelToDelete->deleted_at);
        $this->assertNotNull($this->model()::find($id));
    }
}
<?php

namespace Tests\Traits;

trait FeatureModelsValidations
{   
    use UuidValidations, DatabaseValidations;

    public function factoryCreateModel() {
        return factory($this->getModel(), 1)->create();
    }

    public function getFactoryListModel() {
        $this->factoryCreateModel();
        return $this->getModel()::all();
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
        $model = $this->getModel()::create($data);
        $model->refresh();
        return $model;
    }
    
    public function assertCreate(array $data, array $validadeData) {
        $model = $this->getModelCreated($data);

        $this->assertDatabaseData($validadeData);
    }  

    public function assertEdit(array $oldData, array $newData) {        
        $model = $this->getModelCreated($oldData);
        
        $model->update($newData);

        $this->assertDatabaseData($newData);
    }

    public function assertSoftDelete(string $id) {       
        $modelToDelete = $this->getModel()::find($id);
        $deleted = $modelToDelete->delete();
        $this->assertTrue($deleted);
        $this->assertNotNull($modelToDelete->deleted_at);
        $this->assertDatabaseData([]);
        $this->assertNotNull($this->getModel()::onlyTrashed()->find($id));

        $modelToDelete->restore();
        $this->assertNull($modelToDelete->deleted_at);
        $this->assertNotNull($this->getModel()::find($id));
    }
}
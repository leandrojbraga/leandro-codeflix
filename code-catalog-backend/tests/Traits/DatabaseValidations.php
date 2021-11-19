<?php

namespace Tests\Traits;

trait DatabaseValidations
{   
    public function assertDatabaseData($validadeData) {
        $model = $this->model();
        $table = (new $model)->getTable();
        $this->assertDatabaseHas($table, $validadeData);
    }
}
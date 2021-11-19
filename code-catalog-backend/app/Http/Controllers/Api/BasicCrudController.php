<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

abstract class BasicCrudController extends Controller
{
    protected abstract function model();

    protected abstract function simpleValidationRules();

    // Display a listing of the resource.
    // GET -> api/{model}/
    public function index()
    {
        return $this->model()::all();
    }

    protected function validateRequestData(Request $request)
    {
        return $this->validate($request, $this->simpleValidationRules());
    }

    // Store a newly created resource in storage.
    //POST  -> api/{model}/
    //public function store(CategoryRequest $request) -> validate with request
    public function store(Request $request)
    {
        $validateData = $this->validateRequestData($request);
        $obj = $this->model()::create($validateData);
        $obj->refresh();
        return $obj;
    }

    protected function findOrFail($key)
    {
        $model = $this->model();
        $keyName = (new $model)->getRouteKeyName();
        
        return $this->model()::where($keyName, $key)->firstOrFail();
    }

    // Display the specified resource.
    // Route Model Binding
    // GET -> api/{model}/{id}
    public function show($id)
    {   
        return $this->findOrFail($id);
    }

    // Update the specified resource in storage.
    // PUT -> api/{model}/{id}
    public function update(Request $request, $id)
    {
        $validateData = $this->validateRequestData($request);
        $category = $this->findOrFail($id);
        
        $category->update($validateData);
        return $category;
    }

    // Remove the specified resource from storage.
    // DELETE -> api/{model}/{id}
    public function destroy($id)
    {   
        $category = $this->findOrFail($id);
        
        $category->delete();
        return response()->noContent(); //204 - No content
    }
}

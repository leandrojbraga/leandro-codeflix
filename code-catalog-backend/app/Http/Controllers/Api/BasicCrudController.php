<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Http\Resources\Json\ResourceCollection;

abstract class BasicCrudController extends Controller
{   
    protected $paginationSize = 15;

    protected abstract function model();

    protected abstract function validationRules($request);

    protected abstract function resource();

    protected abstract function resourceCollection();

    private function getResourceCollection($data) {
        $collection = $this->resourceCollection();
        $collectionClass = new \ReflectionClass($collection);
         
        return $collectionClass->isSubclassOf(ResourceCollection::class)
            ? new $collection($data)
            : $collection::collection($data);
    }

    // Display a listing of the resource.
    // GET -> api/{model}/
    public function index()
    {
        $data = !$this->paginationSize
            ? $this->model()::all()
            : $this->model()::paginate($this->paginationSize);

        return $this->getResourceCollection($data);
    }

    protected function validateRequestData(Request $request)
    {
        return $this->validate($request, $this->validationRules($request));
    }

    private function getObjectResource($obj) {
        $resource = $this->resource();
        return new $resource($obj);
    }

    // Store a newly created resource in storage.
    //POST  -> api/{model}/
    //public function store(CategoryRequest $request) -> validate with request
    public function store(Request $request)
    {
        $validateData = $this->validateRequestData($request);
        $obj = $this->model()::create($validateData);        
        $obj->refresh();

        return $this->getObjectResource($obj);
        
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
        $obj = $this->findOrFail($id);

        return $this->getObjectResource($obj);
    }

    // Update the specified resource in storage.
    // PUT -> api/{model}/{id}
    public function update(Request $request, $id)
    {
        $obj = $this->findOrFail($id);
        $validateData = $this->validateRequestData($request);
        $obj->update($validateData);

        return $this->getObjectResource($obj);
    }

    // Remove the specified resource from storage.
    // DELETE -> api/{model}/{id}
    public function destroy($id)
    {   
        $obj = $this->findOrFail($id);
        
        $obj->delete();
        return response()->noContent(); //204 - No content
    }
}

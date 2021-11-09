<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    // Display a listing of the resource.
    // GET -> api/categories/
    public function index()
    {
        return Category::all();
    }

    // Store a newly created resource in storage.
    //POST  -> api/categories/
    //public function store(CategoryRequest $request) -> validate with request
    public function store(Request $request)
    {
        $this->validate($request, $this->simpleValidationRules);
        $category = Category::create($request->all());
        $category->refresh();
        return $category;
    }

    // Display the specified resource.
    // Route Model Binding
    // GET -> api/categories/{id}
    public function show(Category $category)
    {
        return $category;
    }

    // Update the specified resource in storage.
    // PUT -> api/categories/{id}
    public function update(Request $request, Category $category)
    {
        $this->validate($request, $this->simpleValidationRules);
        // $category->fill($request->all());
        // $category->save();
        $category->update($request->all());
        return $category;
    }

    // Remove the specified resource from storage.
    // DELETE -> api/categories/{id}
    public function destroy(Category $category)
    {
        $category->delete();
        return response()->noContent(); //204 - No content
    }
}

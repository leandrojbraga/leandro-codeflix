<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Genre;
use Illuminate\Http\Request;

class GenreController extends Controller
{

    public function index()
    {
        return Genre::all();
    }

    public function store(Request $request)
    {
        $this->validate($request, $this->simpleValidationRules);
        $genre = Genre::create($request->all());
        $genre->refresh();
        return $genre;
    }

    public function show(Genre $genre)
    {
        return $genre;
    }

    public function update(Request $request, Genre $genre)
    {
        $this->validate($request, $this->simpleValidationRules);
        $genre->update($request->all());
        return $genre;
    }

    public function destroy(Genre $genre)
    {
        $genre->delete();
        return response()->noContent(); //204 - No content
    }
}

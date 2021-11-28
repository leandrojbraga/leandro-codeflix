<?php

namespace App\Http\Controllers\Api;

use App\Models\Video;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VideoController extends BasicCrudController
{
    protected function model() {
        return Video::class;
    }

    protected function validationRules() {
        return [
            'title' => 'required|min:3|max:255',
            'description' => 'required|min:10',
            'year_launched' => 'required|date_format:Y',
            'opened' => 'boolean',
            'rating' => 'required|in:' . implode(',', Video::RATINGS),
            'duration' => 'required|integer',
            'categories_id' => 'required|array|exists:categories,id',
            'genres_id' => 'required|array|exists:genres,id'
        ];
    }

    public function store(Request $request)
    {
        $validateData = $this->validateRequestData($request);
        $self = $this;

        $obj= DB::transaction(function () use ($request, $validateData, $self){
            $transaction = $this->model()::create($validateData);
            $self->handleRelations($transaction, $request);
            return $transaction;
        });
        
        $obj->refresh();
        return $obj;
    }

    public function update(Request $request, $id)
    {
        $validateData = $this->validateRequestData($request);
        $self = $this;

        $obj = DB::transaction(function () use ($request, $id, $validateData, $self)
        {
            $transaction = $self->findOrFail($id);
            $transaction->update($validateData);
            $self->handleRelations($transaction, $request);            
            return $transaction;
        });

        return $obj;
    }

    protected function handleRelations($transaction, Request $request)
    {
        $transaction->categories()->sync($request->get('categories_id'));
        $transaction->genres()->sync($request->get('genres_id'));
    }
}

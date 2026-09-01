<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\IngredientTypeResource;
use App\Models\IngredientType;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class IngredientTypeController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        return IngredientTypeResource::collection(
            IngredientType::query()->orderBy('title')->get()
        );
    }
}

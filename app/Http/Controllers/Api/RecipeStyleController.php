<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLookupRequest;
use App\Http\Requests\UpdateLookupRequest;
use App\Http\Resources\RecipeStyleResource;
use App\Models\RecipeStyle;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class RecipeStyleController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        return RecipeStyleResource::collection(RecipeStyle::query()->orderBy('title')->get());
    }

    public function store(StoreLookupRequest $request): RecipeStyleResource
    {
        return new RecipeStyleResource(RecipeStyle::query()->create($request->validated()));
    }

    public function show(RecipeStyle $recipeStyle): RecipeStyleResource
    {
        return new RecipeStyleResource($recipeStyle);
    }

    public function update(UpdateLookupRequest $request, RecipeStyle $recipeStyle): RecipeStyleResource
    {
        $recipeStyle->update($request->validated());

        return new RecipeStyleResource($recipeStyle);
    }

    public function destroy(RecipeStyle $recipeStyle): JsonResponse
    {
        $recipeStyle->delete();

        return response()->json(null, 204);
    }
}

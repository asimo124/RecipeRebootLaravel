<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreIngredientRequest;
use App\Http\Requests\StoreRelatedIngredientRequest;
use App\Http\Requests\UpdateIngredientRequest;
use App\Http\Resources\IngredientResource;
use App\Models\Ingredient;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class IngredientController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Ingredient::query()->with('type')->orderBy('title');

        if ($search = $request->query('search')) {
            $query->where('title', 'like', '%'.$search.'%');
        }

        $limit = min((int) $request->query('limit', 50), 100);

        return IngredientResource::collection($query->limit($limit)->get());
    }

    public function store(StoreIngredientRequest $request): IngredientResource
    {
        $ingredient = Ingredient::query()->create($request->validated());
        $ingredient->load('type');

        return new IngredientResource($ingredient);
    }

    public function show(Ingredient $ingredient): IngredientResource
    {
        $ingredient->load(['type', 'parents', 'children']);

        return new IngredientResource($ingredient);
    }

    public function update(UpdateIngredientRequest $request, Ingredient $ingredient): IngredientResource
    {
        $ingredient->update($request->validated());
        $ingredient->load('type');

        return new IngredientResource($ingredient);
    }

    public function destroy(Ingredient $ingredient): JsonResponse
    {
        $ingredient->delete();

        return response()->json(null, 204);
    }

    public function related(Ingredient $ingredient): AnonymousResourceCollection
    {
        $ingredient->load('parents');

        return IngredientResource::collection($ingredient->parents);
    }

    public function storeRelated(StoreRelatedIngredientRequest $request, Ingredient $ingredient): IngredientResource
    {
        $parentId = $request->validated()['related_ingredient_id'];
        $ingredient->parents()->syncWithoutDetaching([$parentId]);
        $ingredient->load(['type', 'parents', 'children']);

        return new IngredientResource($ingredient);
    }

    public function destroyRelated(Ingredient $ingredient, int $relatedId): IngredientResource
    {
        $ingredient->parents()->detach($relatedId);
        $ingredient->load(['type', 'parents', 'children']);

        return new IngredientResource($ingredient);
    }
}

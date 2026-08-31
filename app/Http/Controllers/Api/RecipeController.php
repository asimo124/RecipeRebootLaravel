<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRecipeRequest;
use App\Http\Requests\UpdateRecipeRequest;
use App\Http\Resources\RecipeResource;
use App\Models\Recipe;
use App\Models\RecipeModel;
use App\Services\IngredientAvailabilityService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class RecipeController extends Controller
{
    public function __construct(private IngredientAvailabilityService $availability)
    {
    }

    public function index(): AnonymousResourceCollection
    {
        $recipes = Recipe::query()
            ->with(['protein', 'style', 'attributes'])
            ->withMax('attributes', 'severity_level')
            ->orderByRaw('"attributes_max_severity_level" ASC NULLS FIRST')
            ->orderBy('title')
            ->get();

        return RecipeResource::collection($recipes);
    }

    public function store(StoreRecipeRequest $request): RecipeResource
    {
        $data = $request->validated();
        $ingredientIds = $data['ingredient_ids'] ?? null;
        $attributeIds = $data['attribute_ids'] ?? null;
        unset($data['ingredient_ids'], $data['attribute_ids']);

        $recipe = Recipe::query()->create($data);

        if (is_array($ingredientIds)) {
            $recipe->ingredients()->sync($ingredientIds);
        }

        if (is_array($attributeIds)) {
            $recipe->attributes()->sync($attributeIds);
        }

        $recipe->load(['protein', 'style', 'ingredients.type', 'attributes']);

        return new RecipeResource($recipe);
    }

    public function show(Recipe $recipe): RecipeResource
    {
        $recipe->load(['protein', 'style', 'ingredients.type', 'attributes']);
        $recipe->availability = $this->availability->forRecipe($recipe);

        return new RecipeResource($recipe);
    }

    public function update(UpdateRecipeRequest $request, Recipe $recipe): RecipeResource
    {
        $data = $request->validated();
        $ingredientIds = $data['ingredient_ids'] ?? null;
        $attributeIds = $data['attribute_ids'] ?? null;
        unset($data['ingredient_ids'], $data['attribute_ids']);

        $recipe->update($data);

        if (is_array($ingredientIds)) {
            $recipe->ingredients()->sync($ingredientIds);
        }

        if (is_array($attributeIds)) {
            $recipe->attributes()->sync($attributeIds);
        }

        $recipe->load(['protein', 'style', 'ingredients.type', 'attributes']);
        $recipe->availability = $this->availability->forRecipe($recipe);

        return new RecipeResource($recipe);
    }

    public function destroy(Recipe $recipe): JsonResponse
    {
        $recipe->delete();

        return response()->json(null, 204);
    }

    public function attachIngredient(Request $request, Recipe $recipe): RecipeResource
    {
        $data = $request->validate([
            'ingredient_id' => ['required', 'integer', RecipeModel::existsRule('ri_ingredient')],
        ]);

        $recipe->ingredients()->syncWithoutDetaching([$data['ingredient_id']]);
        $recipe->load(['protein', 'style', 'ingredients.type', 'attributes']);

        return new RecipeResource($recipe);
    }

    public function detachIngredient(Recipe $recipe, int $ingredientId): RecipeResource
    {
        $recipe->ingredients()->detach($ingredientId);
        $recipe->load(['protein', 'style', 'ingredients.type', 'attributes']);

        return new RecipeResource($recipe);
    }
}

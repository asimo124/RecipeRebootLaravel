<?php

use App\Http\Controllers\Api\AttributeController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\IngredientController;
use App\Http\Controllers\Api\InventoryController;
use App\Http\Controllers\Api\ProteinController;
use App\Http\Controllers\Api\RecipeController;
use App\Http\Controllers\Api\RecipeStyleController;
use Illuminate\Support\Facades\Route;

Route::post('login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('me', [AuthController::class, 'me']);
    Route::post('logout', [AuthController::class, 'logout']);

    Route::apiResource('recipes', RecipeController::class);
    Route::post('recipes/{recipe}/ingredients', [RecipeController::class, 'attachIngredient']);
    Route::delete('recipes/{recipe}/ingredients/{ingredientId}', [RecipeController::class, 'detachIngredient']);

    Route::get('ingredients', [IngredientController::class, 'index']);
    Route::post('ingredients', [IngredientController::class, 'store']);
    Route::get('ingredients/{ingredient}', [IngredientController::class, 'show']);
    Route::put('ingredients/{ingredient}', [IngredientController::class, 'update']);
    Route::delete('ingredients/{ingredient}', [IngredientController::class, 'destroy']);
    Route::get('ingredients/{ingredient}/related', [IngredientController::class, 'related']);
    Route::post('ingredients/{ingredient}/related', [IngredientController::class, 'storeRelated']);
    Route::delete('ingredients/{ingredient}/related/{relatedId}', [IngredientController::class, 'destroyRelated']);

    Route::get('inventory', [InventoryController::class, 'index']);
    Route::post('inventory', [InventoryController::class, 'store']);
    Route::delete('inventory/{inventory}', [InventoryController::class, 'destroy']);

    Route::apiResource('proteins', ProteinController::class);
    Route::apiResource('recipe-styles', RecipeStyleController::class);
    Route::get('attributes', [AttributeController::class, 'index']);
});

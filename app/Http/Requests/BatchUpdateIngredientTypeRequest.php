<?php

namespace App\Http\Requests;

use App\Models\RecipeModel;
use Illuminate\Foundation\Http\FormRequest;

class BatchUpdateIngredientTypeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['integer', RecipeModel::existsRule('ri_ingredient')],
            'ingredient_type_id' => ['nullable', 'integer', RecipeModel::existsRule('ri_ingredient_type')],
        ];
    }
}

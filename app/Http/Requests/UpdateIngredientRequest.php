<?php

namespace App\Http\Requests;

use App\Models\RecipeModel;
use Illuminate\Foundation\Http\FormRequest;

class UpdateIngredientRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            'ingredient_type_id' => ['nullable', 'integer', RecipeModel::existsRule('ri_ingredient_type')],
        ];
    }
}

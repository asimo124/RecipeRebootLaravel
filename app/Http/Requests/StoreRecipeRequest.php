<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRecipeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'last_date_made' => ['nullable', 'date'],
            'contains_gluten' => ['nullable', 'boolean'],
            'image_path' => ['nullable', 'string', 'max:255'],
            'protein_id' => ['nullable', 'integer', 'exists:ri_protein,id'],
            'recipe_style_id' => ['nullable', 'integer', 'exists:ri_recipe_style,id'],
            'recipe_link' => ['nullable', 'string', 'max:255'],
            'ingredient_ids' => ['sometimes', 'array'],
            'ingredient_ids.*' => ['integer', 'exists:ri_ingredient,id'],
            'attribute_ids' => ['sometimes', 'array'],
            'attribute_ids.*' => ['integer', 'exists:ri_attribute,id'],
        ];
    }
}

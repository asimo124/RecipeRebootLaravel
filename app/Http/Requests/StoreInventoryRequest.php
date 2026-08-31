<?php

namespace App\Http\Requests;

use App\Models\RecipeModel;
use Illuminate\Foundation\Http\FormRequest;

class StoreInventoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'ingredient_id' => ['required', 'integer', RecipeModel::existsRule('ri_ingredient')],
        ];
    }
}

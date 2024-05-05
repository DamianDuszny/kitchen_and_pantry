<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RecipeAccessRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $recipeId = $this->route('recipe_id') ?: $this->request->get('recipe_id');
        /** @var \App\Models\user $user */
        $user = $this->user();
        return !empty($user->recipes()->find($recipeId));
    }
}

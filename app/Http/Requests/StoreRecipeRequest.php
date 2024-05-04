<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRecipeRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'recipe_name' => ['max:20', 'min:3', 'required'],
            'description' => ['string', 'required', 'max:5000'],
            'instruction' => ['string', 'required', 'max:5000'],
            'preparation_time' => ['int', 'required'],
            'recipe_type_id' => ['int', 'required'],
            'kcal' => ['int'],
            'portion_for_how_many' => ['int'],
            'complexity' => ['int']
        ];
    }
}

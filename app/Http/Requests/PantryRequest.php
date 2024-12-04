<?php

namespace App\Http\Requests;

use App\Dictionary\PantryRoles\PantryRole;
use Illuminate\Foundation\Http\FormRequest;

class PantryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = auth('sanctum')->user();
        return $user
            && $user
                ->pantries()
                ->where('pantry_id', $this->input('pantry_id'))
                ->wherePivotIn('role_id', array_map(fn($x) => $x->value, $this->getNeededPermissionLevel()))
                ->exists();
    }

    protected function getNeededPermissionLevel(): array {
        return PantryRole::getRolesThatHaveReadPermission();
    }

    public function rules(): array
    {
        return [
            'pantry_id' => ['required', 'numeric']
        ];
    }
}

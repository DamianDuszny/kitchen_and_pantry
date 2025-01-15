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

        if (!$user) {
            return false; // Brak uwierzytelnionego użytkownika
        }

        // Pobierz pantry_id z trasy (lub ustaw null, jeśli go brak)
        $pantryId = $this->route('pantry_id');

        // Jeśli pantry_id jest podane, sprawdź dostęp do konkretnej spiżarni
        if ($pantryId) {
            return $user->pantries()
                ->where('pantry_id', $pantryId)
                ->wherePivotIn(
                    'role_id',
                    array_map(fn($x) => $x->value, $this->getNeededPermissionLevel())
                )
                ->exists();
        }

        // Jeśli pantry_id nie jest podane, sprawdź, czy użytkownik ma dostęp do jakichkolwiek spiżarni
        return $user->pantries()
            ->wherePivotIn(
                'role_id',
                array_map(fn($x) => $x->value, $this->getNeededPermissionLevel())
            )
            ->exists();
    }
    protected function getNeededPermissionLevel(): array {
        return PantryRole::rolesWithReadPerm();
    }

    public function validationData()
    {
        // Pobranie danych wejściowych i dodanie 'pantry_id' z trasy
        return array_merge($this->all(), [
            'pantry_id' => $this->route('pantry_id'),
        ]);
    }

    public function rules(): array
    {
        return [
            'pantry_id' => ['nullable', 'numeric'] //@todo array of pantries ids
        ];
    }
}

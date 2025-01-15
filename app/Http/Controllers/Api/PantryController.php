<?php

namespace App\Http\Controllers\Api;
use App\Dictionary\PantryRoles\PantryRole;
use App\Dictionary\PantryRoles\PantryRoleTxt;
use App\Http\Controllers\Controller;
use App\Http\Requests\PantryRequest;
use App\Models\pantry_users_access;
use App\Models\user;
use Illuminate\Http\Request;

/**
 * @todo move logic to service
 */
class PantryController extends Controller
{
    public function list(PantryRequest $request) {
        /** @var user $user */
        $user = $request->user();
        $pantriesData = $user->load(['pantries.users_privileges', 'pantries.users'])->pantries;

        foreach ($pantriesData as $pantryData) {
            $privilegesByUserId = $pantryData->users_privileges->keyBy('users_id');
            unset($pantryData->users_privileges);
            foreach ($pantryData->users as $user) {
                $userPrivilege = $privilegesByUserId->get($user->id);
                $user->desc = $userPrivilege ? PantryRoleTxt::getRoleName(PantryRole::from($userPrivilege->role_id)) : null;
            }
        }

        return $pantriesData;
    }

    public function createPantry(Request $request) {
        $pantry = new \App\Models\pantry;
        $pantry->description = $request->post('description');
        $pantry->name = $request->post('name');
        $pantry->save();
        $pantry_access = new pantry_users_access();
        $pantry_access->users_id = $request->user()->id;
        $pantry_access->pantry_id = $pantry->id;
        $pantry_access->role_id = PantryRole::CREATOR;
        $pantry_access->save();
        $pantry->setRelation('users', $pantry_access);
        return $pantry;
    }

    //@todo return data of an pantry. Name, users, maybe extra data created by user
    public function index() {

    }

    public function edit() {

    }
}

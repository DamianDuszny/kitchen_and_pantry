<?php

namespace App\Http\Controllers\Api;
use App\Dictionary\PantryRoles\PantryRole;
use App\Http\Controllers\Controller;
use App\Models\pantry_users_access;
use App\Models\user;
use Illuminate\Http\Request;

class Pantry extends Controller
{
    public function list(Request $request) {
        /** @var user $user */
        $user = $request->user();
        $pantriesData = $user->load(['pantries.users_privileges', 'pantries.users'])->pantries;
        foreach($pantriesData as $pantryData) {
            foreach($pantryData->users_privileges as $privilege) {

            }
        }
    }

    public function createPantry(Request $request) {
        $pantry = new \App\Models\pantry;
        $pantry->description = $request->post('description');
        $pantry->name = $request->post('name');
        $pantry->save();
        $pantry_access = new pantry_users_access();
        $pantry_access->users_id = $request->user()->id;
        $pantry_access->pantry_id = $pantry->id;
        $pantry_access->role_id = PantryRole::OWNER;
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

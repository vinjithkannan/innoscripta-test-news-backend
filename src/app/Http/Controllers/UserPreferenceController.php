<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserPreferenceRequest;
use App\Http\Requests\UpdateUserPreferenceRequest;
use App\Http\Resources\UserPreferenceResource;
use App\Models\UserPreference;

class UserPreferenceController extends Controller
{

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserPreferenceRequest $request)
    {
        $data = $request->validated();
        $userId = $request->user()->id;

        $userPreference = UserPreference::create([
            'user_id' => $userId,
            'source' => json_encode($data['source']),
            'category' => json_encode($data['category']),
            'author' => json_encode($data['author']),
        ]);

        return response()->json([
            'user_preference' => new UserPreferenceResource($userPreference),
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(UserPreference $userPreference)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserPreferenceRequest $request, UserPreference $userPreference)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(UserPreference $userPreference)
    {
        //
    }
}

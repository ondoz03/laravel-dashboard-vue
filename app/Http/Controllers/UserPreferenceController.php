<?php

namespace App\Http\Controllers;

use App\Models\UserPreference;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class UserPreferenceController extends Controller
{
    /**
     * Store or update a user preference.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'preference_type' => ['required', 'string'],
            'page' => ['required', 'string'],
            'settings' => ['required'],
        ]);

        // Find existing preference or create a new one
        $preference = UserPreference::updateOrCreate(
            [
                'user_id' => Auth::id(),
                'preference_type' => $validated['preference_type'],
                'page' => $validated['page'],
            ],
            [
                'settings' => $validated['settings'],
            ]
        );

        return back()->with('success', 'Preferences saved successfully.');
    }

    /**
     * Get a user preference.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        $validated = $request->validate([
            'preference_type' => ['required', 'string'],
            'page' => ['required', 'string'],
        ]);

        $preference = UserPreference::where([
            'user_id' => Auth::id(),
            'preference_type' => $validated['preference_type'],
            'page' => $validated['page'],
        ])->first();

        return Inertia::render($request->header('X-Inertia-Partial-Component'), [
            'preferences' => $preference,
        ]);
    }
}

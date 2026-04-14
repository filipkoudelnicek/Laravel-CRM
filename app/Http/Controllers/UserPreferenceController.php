<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserPreferenceController extends Controller
{
    public function toggleDarkMode(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = auth()->user();

        if ($request->has('dark_mode')) {
            $user->dark_mode = (bool) $request->boolean('dark_mode');
        } else {
            $user->dark_mode = !$user->dark_mode;
        }

        $user->save();

        return response()->json(['dark_mode' => $user->dark_mode]);
    }
}

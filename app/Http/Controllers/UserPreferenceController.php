<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;

class UserPreferenceController extends Controller
{
    public function toggleDarkMode(): JsonResponse
    {
        /** @var User $user */
        $user = auth()->user();
        $user->dark_mode = !$user->dark_mode;
        $user->save();

        return response()->json(['dark_mode' => $user->dark_mode]);
    }
}

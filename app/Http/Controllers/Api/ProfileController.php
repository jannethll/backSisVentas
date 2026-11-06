<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Profile\ChangePasswordRequest;
use App\Http\Requests\Profile\UpdateProfileRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class ProfileController extends Controller
{
    // GET /api/v1/profile
    public function show()
    {
        return response()->json(Auth::guard('api')->user());
    }

    // PATCH /api/v1/profile
    public function update(UpdateProfileRequest $request)
    {
        $user = Auth::guard('api')->user();
        $user->update($request->validated());

        return response()->json($user->fresh());
    }

    // PATCH /api/v1/profile/password
    public function changePassword(ChangePasswordRequest $request)
    {
        $user = Auth::guard('api')->user();

        if (! Hash::check($request->current_password, $user->password)) {
            return response()->json(['message' => 'La contraseña actual no es correcta.'], 422);
        }

        $user->update(['password' => $request->new_password]);

        // Invalida el token actual: fuerza a loguearse de nuevo con la nueva contraseña
        JWTAuth::invalidate(JWTAuth::getToken());

        return response()->json(['message' => 'Contraseña actualizada. Por favor, inicia sesión de nuevo.']);
    }

    // PATCH /api/v1/profile/notification-preferences
    public function updateNotificationPreferences(Request $request)
    {
        $validated = $request->validate([
            'stock_bajo' => ['required', 'boolean'],
            'vencimientos' => ['required', 'boolean'],
            'nuevos_pedidos' => ['required', 'boolean'],
        ]);

        $user = Auth::guard('api')->user();
        $user->update(['notification_preferences' => json_encode($validated)]);

        return response()->json($user->fresh());
    }

    // POST /api/v1/profile/avatar
public function updateAvatar(Request $request)
{
    $request->validate([
        'avatar' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
    ]);

    $user = Auth::guard('api')->user();

    if ($user->avatar_path) {
        Storage::disk('public')->delete($user->avatar_path);
    }

    $path = $request->file('avatar')->store('avatars', 'public');
    $user->update(['avatar_path' => $path]);

    return response()->json($user->fresh());
}
}
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    // ──────────────────────────────────────────────────────────────────
    // POST /admin/auth/login  (no auth required)
    // ──────────────────────────────────────────────────────────────────
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email'       => 'required|email',
            'password'    => 'required|string',
            'device_name' => 'nullable|string|max:150',
        ]);

        $admin = Admin::where('email', $request->email)
                      ->where('is_admin', true)
                      ->first();

        if (! $admin || ! Hash::check($request->password, $admin->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        // Generate independent device token (does NOT revoke other devices)
        $deviceName = $request->input('device_name')
            ?: ($request->header('User-Agent') ? substr($request->header('User-Agent'), 0, 100) : 'admin-device');

        $token = $admin->createToken($deviceName)->accessToken;

        return response()->json([
            'success'     => true,
            'message'     => 'Login successful.',
            'device_name' => $deviceName,
            'token'       => $token,
            'admin'       => $this->adminResource($admin),
        ], 200);
    }

    // ──────────────────────────────────────────────────────────────────
    // POST /admin/auth/logout  (auth required)
    // ──────────────────────────────────────────────────────────────────
    public function logout(Request $request): JsonResponse
    {
        // If all_devices=true is passed, log out everywhere; otherwise log out only THIS device
        if ($request->boolean('all_devices')) {
            $request->user()->tokens()->delete();
            return response()->json([
                'success' => true,
                'message' => 'All device sessions logged out successfully.',
            ], 200);
        }

        // Revoke only the current device's active access token
        $request->user()->token()->revoke();

        return response()->json([
            'success' => true,
            'message' => 'Current device logged out successfully.',
        ], 200);
    }

    // ──────────────────────────────────────────────────────────────────
    // GET /admin/auth/me  (auth required)
    // ──────────────────────────────────────────────────────────────────
    public function me(Request $request): JsonResponse
    {
        return response()->json([
            'admin' => $this->adminResource($request->user()),
        ], 200);
    }

    // ──────────────────────────────────────────────────────────────────
    // PUT/POST /admin/auth/profile  (auth required)
    // Update: name, email, phone, bio, avatar / profile_image
    // ──────────────────────────────────────────────────────────────────
    public function updateProfile(Request $request): JsonResponse
    {
        /** @var Admin $admin */
        $admin = $request->user();

        $request->validate([
            'name'          => 'sometimes|required|string|max:255',
            'email'         => 'sometimes|required|email|unique:users,email,' . $admin->id,
            'phone'         => 'sometimes|nullable|string|max:20',
            'bio'           => 'sometimes|nullable|string|max:1000',
            'avatar'        => 'sometimes|nullable',
            'profile_image' => 'sometimes|nullable',
        ]);

        $data = $request->only(['name', 'email', 'phone', 'bio']);

        // Handle profile image file upload (accepts either field name: profile_image or avatar)
        if ($request->hasFile('profile_image')) {
            if ($admin->avatar && ! Str::startsWith($admin->avatar, ['http://', 'https://'])) {
                Storage::disk('public')->delete($admin->avatar);
            }
            $data['avatar'] = $request->file('profile_image')->store('avatars', 'public');
        } elseif ($request->hasFile('avatar')) {
            if ($admin->avatar && ! Str::startsWith($admin->avatar, ['http://', 'https://'])) {
                Storage::disk('public')->delete($admin->avatar);
            }
            $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
        } elseif ($request->filled('profile_image')) {
            $data['avatar'] = $request->input('profile_image');
        } elseif ($request->filled('avatar')) {
            $data['avatar'] = $request->input('avatar');
        }

        $admin->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully.',
            'admin'   => $this->adminResource($admin->fresh()),
        ], 200);
    }

    // ──────────────────────────────────────────────────────────────────
    // PUT/POST /admin/auth/change-password  (auth required)
    // ──────────────────────────────────────────────────────────────────
    public function changePassword(Request $request): JsonResponse
    {
        $request->validate([
            'current_password'          => 'required|string',
            'password'                  => 'required|string|min:8|confirmed',
            'password_confirmation'     => 'required|string',
        ]);

        /** @var Admin $admin */
        $admin = $request->user();

        if (! Hash::check($request->current_password, $admin->password)) {
            throw ValidationException::withMessages([
                'current_password' => ['The current password is incorrect.'],
            ]);
        }

        $admin->update([
            'password' => Hash::make($request->password),
        ]);

        // Revoke existing tokens for security
        $admin->tokens()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Password changed successfully. Please log in again.',
        ], 200);
    }

    // ──────────────────────────────────────────────────────────────────
    // Helper: format admin data for responses
    // ──────────────────────────────────────────────────────────────────
    private function adminResource(Admin $admin): array
    {
        $avatarUrl = null;
        if ($admin->avatar) {
            $avatarUrl = Str::startsWith($admin->avatar, ['http://', 'https://'])
                ? $admin->avatar
                : asset('storage/' . $admin->avatar);
        }

        return [
            'id'            => $admin->id,
            'name'          => $admin->name,
            'email'         => $admin->email,
            'phone'         => $admin->phone,
            'bio'           => $admin->bio,
            'avatar'        => $avatarUrl,
            'profile_image' => $avatarUrl,
            'is_admin'      => (bool) $admin->is_admin,
            'created_at'    => $admin->created_at,
            'updated_at'    => $admin->updated_at,
        ];
    }
}


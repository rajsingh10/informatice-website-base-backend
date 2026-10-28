<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    // ──────────────────────────────────────────────────────────────────
    // POST /admin/auth/login  (no auth required)
    // ──────────────────────────────────────────────────────────────────
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        $admin = Admin::where('email', $request->email)
                      ->where('is_admin', true)
                      ->first();

        if (! $admin || ! Hash::check($request->password, $admin->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        // Revoke old tokens so only one session is active
        $admin->tokens()->delete();

        $token = $admin->createToken('admin-token')->accessToken;

        return response()->json([
            'message' => 'Login successful.',
            'token'   => $token,
            'admin'   => $this->adminResource($admin),
        ], 200);
    }

    // ──────────────────────────────────────────────────────────────────
    // POST /admin/auth/logout  (auth required)
    // ──────────────────────────────────────────────────────────────────
    public function logout(Request $request): JsonResponse
    {
        // Revoke the current access token
        $request->user()->token()->revoke();

        return response()->json([
            'message' => 'Session invalidated. Logged out successfully.',
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
    // PUT /admin/auth/profile  (auth required)
    // Update: name, email, phone, bio, avatar
    // ──────────────────────────────────────────────────────────────────
    public function updateProfile(Request $request): JsonResponse
    {
        /** @var Admin $admin */
        $admin = $request->user();

        $request->validate([
            'name'   => 'sometimes|required|string|max:255',
            'email'  => 'sometimes|required|email|unique:users,email,' . $admin->id,
            'phone'  => 'sometimes|nullable|string|max:20',
            'bio'    => 'sometimes|nullable|string|max:1000',
            'avatar' => 'sometimes|nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $data = $request->only(['name', 'email', 'phone', 'bio']);

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            // Delete old avatar if it exists
            if ($admin->avatar) {
                Storage::disk('public')->delete($admin->avatar);
            }
            $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        $admin->update($data);

        return response()->json([
            'message' => 'Profile updated successfully.',
            'admin'   => $this->adminResource($admin->fresh()),
        ], 200);
    }

    // ──────────────────────────────────────────────────────────────────
    // PUT /admin/auth/change-password  (auth required)
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

        // Revoke all tokens so admin must re-login
        $admin->tokens()->delete();

        return response()->json([
            'message' => 'Password changed successfully. Please log in again.',
        ], 200);
    }

    // ──────────────────────────────────────────────────────────────────
    // Helper: format admin data for responses
    // ──────────────────────────────────────────────────────────────────
    private function adminResource(Admin $admin): array
    {
        return [
            'id'         => $admin->id,
            'name'       => $admin->name,
            'email'      => $admin->email,
            'phone'      => $admin->phone,
            'bio'        => $admin->bio,
            'avatar'     => $admin->avatar
                                ? asset('storage/' . $admin->avatar)
                                : null,
            'is_admin'   => $admin->is_admin,
            'created_at' => $admin->created_at,
            'updated_at' => $admin->updated_at,
        ];
    }
}


<?php

namespace App\Http\Controllers;

use App\Models\Aktivitas;
use Illuminate\Http\Request;
use illuminate\support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\PersonalAccessToken;

class UserController extends Controller
{
    public function users()
    {
        $user = User::all();
        return response()->json($user);
    }

    public function user(Request $request)
    {
        return response()->json($request->user());
    }


    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'message' => 'User registered successfully',
            'user' => $user,
            'token' => $token
        ]);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

//        $user = User::where('email', $request->email)->first();
//
//        if (!$user || !Hash::check($request->password, $user->password)) {
//            return response()->json(['message' => 'Invalid credentials'], 401);
//        }

        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $user = Auth::user();
        $token = $user->createToken('auth-token')->plainTextToken;

//        $accessToken = $user->createToken('access-token')->plainTextToken;
//        $refreshToken = $user->createToken('refresh-token')->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token,
//            'access_token' => $accessToken,
//            'refresh_token' => $refreshToken,
        ]);
    }

    public function refresh(Request $request)
    {
        $refreshToken = $request->input('refresh_token');
        $tokenModel = PersonalAccessToken::findToken($refreshToken);
        if (!$tokenModel) return response()->json(['message' => 'Unauthorized'], 401);

        $user = $tokenModel->tokenable;
        $newAccessToken = $user->createToken('access-token')->plainTextToken;

        return response()->json(['access_token' => $newAccessToken]);
    }

    public function updateProfile(Request $request)
    {
        $rules = [
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'password' => 'nullable|string|min:8|confirmed',
            'current_password' => 'required_with:password|string',
        ];

        $validated = $request->validate($rules);
        $user = $request->user();

        if ($request->hasFile('avatar')) {
            if ($user->avatar) Storage::disk('public')->delete($user->avatar);
            $validated['avatar'] = $request->file('avatar')->store('foto', 'public');
        }

        if ($request->filled('password')) {
            if (!Hash::check($request->current_password, $user->password)) {
                return response()->json(['message' => 'Password saat ini tidak valid'], 401);
            }
            $validated['password'] = Hash::make($request->password);
        }

        $user->update($validated);

        return response()->json([
            'message' => 'Profile updated successfully',
            'user' => $user,
        ]);
    }




    public function deleteUser($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return response()->json([
            'message' => 'User deleted successfully'
        ]);
    }

    public function logout(Request $request)
    {
        // hapus token saat ini
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out successfully'])
            ->withoutCookie('token'); // hapus cookie
    }
}

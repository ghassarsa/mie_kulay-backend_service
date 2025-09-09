<?php

namespace App\Http\Controllers;

use App\Models\Aktivitas;
use App\Models\Pengeluaran;
use Illuminate\Http\Request;
use illuminate\support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\Redis;

class UserController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif,svg',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role' => 'required|string',
        ]);

        $avatarPath = null;
        if ($request->hasFile('avatar')) {
            // Simpan file di storage/app/public/foto
            $avatarPath = $request->file('avatar')->store('foto', 'public');
        }

        $user = User::create([
            'name' => $request->name,
            'avatar' => $avatarPath,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
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
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string'
        ]);

        if (!Auth::attempt($credentials)) {
            return response()->json([
                'message' => 'Invalid login credentials',
            ], 401);
        }

        $user = Auth::user();

        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'message' => 'login successful',
            'user' => [
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'token' => $token
            ],
        ]);
    }

    public function updateProfile(Request $request)
    {
        $request->validate([
            'avatar' => 'required!image|mimes:jpeg,png,jpg,gif,svg',
            'current_password' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($request->hasFile('avatar')) {
            $path = $request->file('avatar')->store('foto', 'public');
            $request['avatar'] = $path;
        }

        if (!Hash::check($request->current_password, auth()->user()->password)) {
            return response()->json([
                'message' => 'Password saat ini tidak valid',
            ], 401);
        }

        auth()->user()->update([
            'password' => Hash::make($request->password),
        ]);

        return response()->json([
            'message' => 'Profile updated successfully',
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logout successful'
        ]);
    }

    public function pengeluaran(Request $request)
    {
        $validate = $request->validate([
            'pengeluaran' => 'required|integer'
        ]);

        Pengeluaran::create([$validate]);

        $name = auth()->user()->name;
        Aktivitas::create([
            'user_id' => auth()->user(),
            'action' => "{$name} Menambahkan pengeluaran sebesar {$validate['pengeluaran']}",
            'aktivitas' => null,
        ]);

        return response()->json([
            'message' => 'Pengeluaran telah ditambahkan'
        ]);
    }
}

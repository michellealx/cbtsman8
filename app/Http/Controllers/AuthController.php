<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        if ($request->session()->has('user')) {
            return redirect('/dashboard');
        }

        return view('auth.login');
    }

    public function authenticate(Request $request)
    {
        $credentials = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $user = User::where('username', $credentials['username'])->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            return back()->withErrors(['username' => 'NIS/Username atau password salah'])->withInput();
        }

        // Generate token baru — session lama di device lain otomatis tidak valid
        $sessionToken = \Illuminate\Support\Str::random(60);
        $user->update(['session_token' => $sessionToken]);

        $request->session()->put('user', [
            'id'            => $user->id,
            'name'          => $user->name,
            'username'      => $user->username,
            'role'          => $user->role,
            'is_coordinator'=> (bool) ($user->is_coordinator ?? false),
            'kelas'         => $user->kelas,
            'session_token' => $sessionToken,
        ]);

        // Role-based redirect after login
        if ($user->role === 'murid') {
            return redirect()->route('murid.exams')->with('success', 'Selamat datang, ' . $user->name . '!');
        }

        return redirect('/dashboard')->with('success', 'Berhasil masuk sebagai ' . $user->role);
    }

    public function register(Request $request)
    {
        if ($request->session()->has('user')) {
            return redirect('/dashboard');
        }

        return view('auth.register');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'          => ['required', 'string', 'max:255'],
            'username'      => ['required', 'string', 'max:255', 'unique:users,username'],
            'password'      => ['required', 'string', 'min:6', 'confirmed'],
            'role'          => ['required', 'in:guru,murid'],
            'kelas'         => ['nullable', 'string'],
            'jenis_kelamin' => ['nullable', 'in:L,P'],
        ]);

        $data['password']      = Hash::make($data['password']);
        $data['session_token'] = \Illuminate\Support\Str::random(60);

        $user = User::create($data);

        $request->session()->put('user', [
            'id'            => $user->id,
            'name'          => $user->name,
            'username'      => $user->username,
            'role'          => $user->role,
            'is_coordinator'=> (bool) ($user->is_coordinator ?? false),
            'kelas'         => $user->kelas,
            'session_token' => $data['session_token'],
        ]);

        return redirect('/dashboard')->with('success', 'Akun berhasil dibuat dan Anda berhasil login.');
    }

    public function logout(Request $request)
    {
        // Hapus token dari DB agar tidak ada session aktif sama sekali
        $userData = $request->session()->get('user');
        if ($userData) {
            User::where('id', $userData['id'])->update(['session_token' => null]);
        }

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'Anda berhasil logout.');
    }
}

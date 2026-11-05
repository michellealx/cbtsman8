<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

abstract class Controller
{
    protected function currentUser(Request $request): ?array
    {
        return $request->session()->get('user');
    }

    protected function requireAuth(Request $request)
    {
        if (! $request->session()->has('user')) {
            return redirect('/login');
        }

        // Validasi session token — jika akun login di device lain, token di DB sudah berubah
        $userData = $request->session()->get('user');
        $dbToken  = \App\Models\User::where('id', $userData['id'])->value('session_token');

        if ($dbToken === null || $dbToken !== ($userData['session_token'] ?? '')) {
            $request->session()->flush();
            return redirect('/')->with('error', 'Sesi Anda telah berakhir karena akun ini login di perangkat lain.');
        }

        return null;
    }

    protected function requireRole(Request $request, array $roles)
    {
        $user = $this->currentUser($request);

        if (! $user || ! in_array($user['role'], $roles, true)) {
            return redirect('/dashboard');
        }

        return null;
    }

    protected function requireCoordinator(Request $request)
    {
        $user = $this->currentUser($request);

        if (! $user || empty($user['is_coordinator'])) {
            return redirect('/dashboard')->with('error', 'Akses dibatasi hanya untuk Koordinator CBT.');
        }

        return null;
    }

    protected function view(Request $request, string $view, array $data = [])
    {
        return view($view, array_merge($data, ['currentUser' => $this->currentUser($request)]));
    }
}

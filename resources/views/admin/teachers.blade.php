@extends('layouts.dashboard')

@section('title', 'Daftar Guru')
@section('page-title', 'Guru')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h4 mb-1 fw-bold">Daftar Guru</h1>
        <p class="text-muted mb-0">Kelola akun guru / tenaga pendidik.</p>
    </div>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
        + Tambah Guru
    </button>
</div>

<div class="card border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th style="width:40px;">
                            <input type="checkbox" class="form-check-input">
                        </th>
                        <th>Nama Guru</th>
                        <th>NIP / Username</th>
                        <th>Jenis Kelamin</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                    <tr>
                        <td>
                            <input type="checkbox" class="form-check-input" value="{{ $user->id }}">
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold" style="width:32px; height:32px; background:#10b981; font-size:0.8rem;">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                                <div>
                                    <div class="fw-semibold">{{ $user->name }}</div>
                                    @if($user->is_coordinator)
                                        <span class="badge bg-warning text-dark fw-bold" style="font-size:0.7rem;"><i class="bi bi-shield-check"></i> Koordinator CBT</span>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td>{{ $user->username }}</td>
                        <td>{{ $user->jenis_kelamin ?? '-' }}</td>
                        <td>
                            <form action="{{ route('admin.teachers.reset_password', $user->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Reset password untuk guru {{ $user->name }} menjadi default: password123 ?');">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-outline-warning text-dark fw-semibold" title="Reset password ke default (password123)">
                                    <i class="bi bi-key-fill"></i> Reset Password
                                </button>
                            </form>
                            <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editTeacherModal{{ $user->id }}"><i class="bi bi-pencil"></i> Edit</button>
                            @if(!$user->is_coordinator)
                            <form action="{{ route('admin.users.delete', $user->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus guru ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i> Hapus</button>
                            </form>
                            @endif
                        </td>
                    </tr>

                    <!-- Modal Edit Guru -->
                    <div class="modal fade" id="editTeacherModal{{ $user->id }}" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title fw-bold">Edit Guru</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="mb-3">
                                            <label class="form-label">Nama Lengkap</label>
                                            <input type="text" name="name" class="form-control" value="{{ $user->name }}" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">NIP / Username (Guru)</label>
                                            <input type="text" name="username" class="form-control" value="{{ $user->username }}" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Jenis Kelamin</label>
                                            <select name="jenis_kelamin" class="form-select">
                                                <option value="">-- Pilih --</option>
                                                <option value="L" {{ $user->jenis_kelamin == 'L' ? 'selected' : '' }}>Laki-laki (L)</option>
                                                <option value="P" {{ $user->jenis_kelamin == 'P' ? 'selected' : '' }}>Perempuan (P)</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Password Baru (Kosongkan jika tidak ingin mengubah)</label>
                                            <input type="password" name="password" class="form-control" minlength="6">
                                        </div>
                                        <hr>
                                        <div class="d-flex justify-content-end gap-2">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-5">
                            Belum ada data guru. Klik "Tambah Guru" untuk memulai.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tambah Pengguna -->
<div class="modal fade" id="addUserModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Tambah Guru Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('admin.users.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="role" value="guru">
                    
                    <div class="mb-3">
                        <label class="form-label">Nama Lengkap</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">NIP / Username (Guru)</label>
                        <input type="text" name="username" class="form-control" required placeholder="Masukkan NIP atau Username">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Jenis Kelamin</label>
                        <select name="jenis_kelamin" class="form-select">
                            <option value="">-- Pilih --</option>
                            <option value="L">Laki-laki (L)</option>
                            <option value="P">Perempuan (P)</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" required minlength="6">
                        <small class="text-muted">Minimal 6 karakter.</small>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan Guru</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@extends('layouts.dashboard')

@section('title', 'Daftar Siswa')
@section('page-title', 'Siswa')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h4 mb-1 fw-bold">Daftar Siswa</h1>
        <p class="text-muted mb-0">Kelola akun murid / siswa.</p>
    </div>
    <div class="d-flex gap-2">
        <button class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#importExcelModal">
            <i class="bi bi-file-earmark-spreadsheet me-1"></i> Import Excel
        </button>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
            + Tambah Siswa
        </button>
    </div>
</div>

@if(!empty($currentUser['is_coordinator']) && isset($teachers) && $teachers->count() > 0)
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-3">
        <form action="{{ route('admin.students') }}" method="GET" class="d-flex align-items-center gap-2">
            <label class="fw-semibold text-muted mb-0"><i class="bi bi-filter me-1"></i>Filter Guru:</label>
            <select name="teacher_id" class="form-select form-select-sm" style="max-width: 250px;" onchange="this.form.submit()">
                <option value="">Semua Guru</option>
                @foreach($teachers as $t)
                    <option value="{{ $t->id }}" {{ (isset($selectedTeacher) && $selectedTeacher == $t->id) ? 'selected' : '' }}>{{ $t->name }}</option>
                @endforeach
            </select>
            @if(isset($selectedTeacher) && $selectedTeacher)
                <a href="{{ route('admin.students') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
            @endif
        </form>
    </div>
</div>
@endif

@php
    $groupedUsers = $users->groupBy(function($u) { return $u->kelas ?: 'Belum Ada Kelas'; });
@endphp

<div class="accordion" id="accordionStudents">
    @forelse($groupedUsers as $kelas => $classUsers)
        <div class="accordion-item mb-3 border-0 shadow-sm rounded-3">
            <h2 class="accordion-header" id="heading-{{ \Illuminate\Support\Str::slug($kelas) }}">
                <button class="accordion-button fw-bold collapsed rounded-3" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-{{ \Illuminate\Support\Str::slug($kelas) }}" aria-expanded="false">
                    Kelas: {{ $kelas }} 
                    <span class="badge bg-primary ms-2">{{ $classUsers->count() }} Siswa</span>
                </button>
            </h2>
            <div id="collapse-{{ \Illuminate\Support\Str::slug($kelas) }}" class="accordion-collapse collapse" data-bs-parent="#accordionStudents">
                <div class="accordion-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th style="width:40px;">
                                        <input type="checkbox" class="form-check-input">
                                    </th>
                                    <th>Nama Siswa</th>
                                    <th>NIS</th>
                                    <th>Jenis Kelamin</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($classUsers as $user)
                                <tr>
                                    <td>
                                        <input type="checkbox" class="form-check-input" value="{{ $user->id }}">
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold" style="width:32px; height:32px; background:#1152b4; font-size:0.8rem;">
                                                {{ strtoupper(substr($user->name, 0, 1)) }}
                                            </div>
                                            <div class="fw-semibold">{{ $user->name }}</div>
                                        </div>
                                    </td>
                                    <td>{{ $user->username }}</td>
                                    <td>{{ $user->jenis_kelamin ?? '-' }}</td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editUserModal{{ $user->id }}"><i class="bi bi-pencil"></i> Edit</button>
                                        <form action="{{ route('admin.users.delete', $user->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus siswa ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i> Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                                
                                <!-- Modal Edit Pengguna -->
                                <div class="modal fade" id="editUserModal{{ $user->id }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title fw-bold">Edit Siswa</h5>
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
                                                        <label class="form-label">NIS (Siswa)</label>
                                                        <input type="text" name="username" class="form-control" value="{{ $user->username }}" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label fw-semibold">Kelas</label>
                                                        <select name="kelas" class="form-select">
                                                            <option value="">Pilih Kelas...</option>
                                                            @if(isset($classrooms))
                                                                @foreach($classrooms as $classroom)
                                                                    <option value="{{ $classroom->name }}" {{ $user->kelas == $classroom->name ? 'selected' : '' }}>{{ $classroom->name }}</option>
                                                                @endforeach
                                                            @endif
                                                        </select>
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
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="card border-0 text-center py-5">
            <div class="card-body">
                <h5 class="text-muted fw-semibold">Belum ada data siswa. Klik "Tambah Siswa" untuk memulai.</h5>
            </div>
        </div>
    @endforelse
</div>

<!-- Modal Tambah Pengguna -->
<div class="modal fade" id="addUserModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Tambah Siswa Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('admin.users.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="role" value="murid">
                    
                    <div class="mb-3">
                        <label class="form-label">Nama Lengkap</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">NIS (Siswa)</label>
                        <input type="text" name="username" class="form-control" required placeholder="Masukkan NIS">
                    </div>
                    
                    <div class="mb-3" id="kelasField">
                        <label class="form-label fw-semibold">Kelas</label>
                        <select name="kelas" class="form-select">
                            <option value="">Pilih Kelas...</option>
                            @if(isset($classrooms))
                                @foreach($classrooms as $classroom)
                                    <option value="{{ $classroom->name }}">{{ $classroom->name }}</option>
                                @endforeach
                            @endif
                        </select>
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
                        <button type="submit" class="btn btn-primary">Simpan Siswa</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Import Excel -->
<div class="modal fade" id="importExcelModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Import Data Siswa dari Excel</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.students.bulk') }}" method="POST" id="formBulkImport" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="students_data" id="studentsDataInput">
                <div class="modal-body">
                    <div class="mb-3 bg-light p-3 rounded border">
                        <label class="form-label fw-bold">Pilih Kelas untuk Siswa yang Diimport</label>
                        <select name="kelas" class="form-select">
                            <option value="">-- Gunakan Kelas dari File / Tetapkan Nanti --</option>
                            @if(isset($classrooms))
                                @foreach($classrooms as $classroom)
                                    <option value="{{ $classroom->name }}">{{ $classroom->name }}</option>
                                @endforeach
                            @endif
                        </select>
                        <div class="form-text">Jika dipilih, semua siswa yang diimport akan dimasukkan ke kelas ini. Jika dikosongkan, sistem akan membaca kolom "Kelas" dari file Excel/CSV.</div>
                    </div>
                    
                    <ul class="nav nav-tabs mb-3" id="importTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="upload-tab" data-bs-toggle="tab" data-bs-target="#upload" type="button" role="tab">Upload File (.xlsx, .csv)</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="paste-tab" data-bs-toggle="tab" data-bs-target="#paste" type="button" role="tab">Copy/Paste dari Excel</button>
                    </li>
                </ul>
                <div class="tab-content" id="importTabsContent">
                    <!-- Upload Tab -->
                    <div class="tab-pane fade show active" id="upload" role="tabpanel">
                        <div class="mb-3">
                            <label class="form-label">Pilih file Excel (.xlsx, .xls, .csv)</label>
                            <input class="form-control" type="file" id="excelFileInput" name="excel_file" accept=".xlsx, .xls, .csv, .txt">
                            <div class="form-text">Pastikan file memiliki baris header: <b>NIS, Nama, Kelas</b> (opsional), <b>Jenis Kelamin</b> (opsional: L/P), <b>Password</b> (opsional).</div>
                        </div>
                    </div>
                    <!-- Paste Tab -->
                    <div class="tab-pane fade" id="paste" role="tabpanel">
                        <div class="mb-3">
                            <label class="form-label">Paste data tabel di sini</label>
                            <textarea class="form-control" id="excelPasteArea" rows="6" placeholder="NIS&#9;Nama&#9;Jenis Kelamin&#9;Password..."></textarea>
                            <div class="form-text">Blok data di Excel (termasuk baris nama kolom di paling atas), tekan Ctrl+C, lalu klik di kotak ini dan tekan Ctrl+V.</div>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="parsePastedData()">Baca Data Paste</button>
                    </div>
                </div>

                <hr>
                
                <h6 class="fw-bold mb-2">Preview Data (<span id="previewCount">0</span> Siswa)</h6>
                <div class="table-responsive" style="max-height: 300px;">
                    <table class="table table-sm table-bordered table-striped" id="previewTable">
                        <thead class="table-light">
                            <tr>
                                <th>NIS</th>
                                <th>Nama</th>
                                <th>Kelas</th>
                                <th>Jenis Kelamin</th>
                                <th>Password</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="5" class="text-center text-muted">Data belum dimuat.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer d-flex justify-content-between">
                <div>
                    <span class="text-danger small d-none" id="importError">Terdapat error saat membaca file.</span>
                </div>
                <div>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success" id="btnSubmitImport" disabled>Simpan <span id="btnSubmitCount"></span> Siswa</button>
                </div>
            </div>
            </form>
        </div>
    </div>
</div>

<script src="{{ asset('xlsx.full.min.js') }}"></script>
<script>
    let parsedStudents = [];

    document.getElementById('excelFileInput').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (!file) return;

        const ext = file.name.split('.').pop().toLowerCase();

        // Native CSV / TXT parser (works offline without any library)
        if (ext === 'csv' || ext === 'txt') {
            const reader = new FileReader();
            reader.onload = function(evt) {
                const text = evt.target.result;
                const lines = text.split(/\r\n|\n|\r/).filter(l => l.trim() !== '');
                if (lines.length === 0) return;

                const firstLine = lines[0];
                const delimiter = firstLine.includes(';') ? ';' : (firstLine.includes('\t') ? '\t' : ',');
                const json = lines.map(line => {
                    // Simple CSV line splitter with quote handling
                    const pattern = new RegExp(`(?:^|${delimiter})(?:"([^"]*(?:""[^"]*)*)"|([^"${delimiter}]*))`, 'g');
                    let match, cols = [];
                    while ((match = pattern.exec(line)) !== null) {
                        cols.push((match[1] !== undefined ? match[1].replace(/""/g, '"') : match[2] || '').trim());
                    }
                    return cols;
                });
                processArrayData(json);
            };
            reader.readAsText(file);
            return;
        }

        // XLSX / XLS parser via SheetJS
        if (typeof XLSX !== 'undefined') {
            const reader = new FileReader();
            reader.onload = function(evt) {
                try {
                    const data = new Uint8Array(evt.target.result);
                    const workbook = XLSX.read(data, {type: 'array'});
                    const firstSheetName = workbook.SheetNames[0];
                    const worksheet = workbook.Sheets[firstSheetName];
                    const json = XLSX.utils.sheet_to_json(worksheet, {header: 1});
                    processArrayData(json);
                } catch(err) {
                    const errorSpan = document.getElementById('importError');
                    errorSpan.innerText = 'Gagal membaca file Excel. Pastikan format file valid atau simpan sebagai .csv.';
                    errorSpan.classList.remove('d-none');
                }
            };
            reader.readAsArrayBuffer(file);
        } else {
            // Enable submit button so backend can process the file directly
            document.getElementById('btnSubmitImport').disabled = false;
            document.getElementById('btnSubmitCount').innerText = '(File Terpilih)';
        }
    });

    function parsePastedData() {
        const text = document.getElementById('excelPasteArea').value;
        if (!text.trim()) return;
        
        const lines = text.split(/\r\n|\n|\r/).filter(l => l.trim() !== '');
        const delimiter = lines[0].includes('\t') ? '\t' : (lines[0].includes(';') ? ';' : ',');
        const json = lines.map(row => row.split(delimiter).map(c => c.trim()));
        processArrayData(json);
    }

    function processArrayData(data) {
        parsedStudents = [];
        const errorSpan = document.getElementById('importError');
        errorSpan.classList.add('d-none');
        
        if (data.length < 2) {
            errorSpan.innerText = 'Data kosong atau tidak memiliki baris nilai.';
            errorSpan.classList.remove('d-none');
            return;
        }

        const headers = data[0].map(h => String(h).toLowerCase().trim());
        const idxNis = headers.findIndex(h => h.includes('nis'));
        const idxNama = headers.findIndex(h => h.includes('nama'));
        const idxKelas = headers.findIndex(h => h.includes('kelas') || h.includes('rombel') || h.includes('class'));
        const idxJk = headers.findIndex(h => h === 'l/p' || h.includes('kelamin') || h.includes('jk') || h === 'lp');
        const idxPass = headers.findIndex(h => h.includes('pass'));

        if (idxNis === -1 || idxNama === -1) {
            errorSpan.innerText = 'Kolom NIS atau Nama tidak ditemukan pada baris pertama header.';
            errorSpan.classList.remove('d-none');
            return;
        }

        for (let i = 1; i < data.length; i++) {
            const row = data[i];
            if (!row || row.length === 0 || (!row[idxNis] && !row[idxNama])) continue;

            parsedStudents.push({
                nis: String(row[idxNis] || '').trim(),
                nama: String(row[idxNama] || '').trim(),
                kelas: idxKelas !== -1 ? String(row[idxKelas] || '').trim() : null,
                jenis_kelamin: idxJk !== -1 ? String(row[idxJk] || '').trim().substring(0, 1).toUpperCase() : null,
                password: idxPass !== -1 ? String(row[idxPass] || '').trim() : null
            });
        }

        updatePreviewTable();
    }

    function updatePreviewTable() {
        const tbody = document.querySelector('#previewTable tbody');
        tbody.innerHTML = '';
        
        document.getElementById('previewCount').innerText = parsedStudents.length;
        
        if (parsedStudents.length === 0) {
            tbody.innerHTML = '<tr><td colspan="5" class="text-center text-muted">Data kosong.</td></tr>';
            document.getElementById('btnSubmitImport').disabled = true;
            document.getElementById('btnSubmitCount').innerText = '';
            return;
        }

        parsedStudents.slice(0, 100).forEach(st => {
            tbody.innerHTML += `
                <tr>
                    <td>${st.nis}</td>
                    <td>${st.nama}</td>
                    <td>${st.kelas || '-'}</td>
                    <td>${st.jenis_kelamin || '-'}</td>
                    <td>${st.password ? '***' : '(Sesuai NIS)'}</td>
                </tr>
            `;
        });
        
        if (parsedStudents.length > 100) {
            tbody.innerHTML += `<tr><td colspan="5" class="text-center text-muted">... dan ${parsedStudents.length - 100} data lainnya</td></tr>`;
        }

        document.getElementById('studentsDataInput').value = JSON.stringify(parsedStudents);
        document.getElementById('btnSubmitImport').disabled = false;
        document.getElementById('btnSubmitCount').innerText = parsedStudents.length;
    }
</script>
@endsection

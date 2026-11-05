@extends('layouts.dashboard')

@section('title', 'Detail Kelas - ' . $classroom->name)
@section('page-title', 'Detail Kelas')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h4 mb-1 fw-bold">Kelas: {{ $classroom->name }}</h1>
        <p class="text-muted mb-0">Daftar siswa yang saat ini berada di kelas ini.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.classes') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
        <button class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#importExcelModal">
            <i class="bi bi-file-earmark-spreadsheet me-1"></i> Import Excel
        </button>
        <a href="{{ route('admin.classes.students.add', $classroom->id) }}" class="btn btn-primary">
            <i class="bi bi-person-plus me-1"></i> Tambah Siswa ke Kelas
        </a>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">No</th>
                        <th>Nama Siswa</th>
                        <th>NIS / Username</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($students as $index => $student)
                        <tr>
                            <td class="ps-4 text-muted">{{ $index + 1 }}</td>
                            <td class="fw-bold text-dark">{{ $student->name }}</td>
                            <td class="text-muted">{{ $student->username }}</td>
                            <td>
                                <!-- Form to remove student from class -->
                                <form action="{{ route('admin.classes.students.remove', [$classroom->id, $student->id]) }}" method="POST" class="d-inline" onsubmit="return confirm('Keluarkan siswa ini dari kelas?');">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        <i class="bi bi-person-x"></i> Keluarkan
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted py-5">
                                Belum ada siswa di kelas ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
</div>

<!-- Modal Import Excel -->
<div class="modal fade" id="importExcelModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Import Data Siswa ke Kelas {{ $classroom->name }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.students.bulk') }}" method="POST" id="formBulkImport" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="students_data" id="studentsDataInput">
                <input type="hidden" name="kelas" value="{{ $classroom->name }}">
                <div class="modal-body">
                    <ul class="nav nav-tabs mb-3" id="importTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="upload-tab" data-bs-toggle="tab" data-bs-target="#upload" type="button" role="tab">Upload File (.xlsx)</button>
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
                            <div class="form-text">Pastikan file memiliki kolom: <b>NIS, Nama, Jenis Kelamin, Password</b>.</div>
                        </div>
                    </div>
                    <!-- Paste Tab -->
                    <div class="tab-pane fade" id="paste" role="tabpanel">
                        <div class="mb-3">
                            <label class="form-label">Paste data tabel di sini</label>
                            <textarea class="form-control" id="excelPasteArea" rows="6" placeholder="NIS&#9;Nama&#9;Jenis Kelamin&#9;Password..."></textarea>
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
                                <th>Jenis Kelamin</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="3" class="text-center text-muted">Data belum dimuat.</td>
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
@endsection

@section('scripts')
<script src="{{ asset('xlsx.full.min.js') }}"></script>
<script>
    let parsedStudents = [];

    document.getElementById('excelFileInput').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (!file) return;

        const ext = file.name.split('.').pop().toLowerCase();
        if (ext === 'csv' || ext === 'txt') {
            const reader = new FileReader();
            reader.onload = function(evt) {
                const text = evt.target.result;
                const lines = text.split(/\r\n|\n|\r/).filter(l => l.trim() !== '');
                if (lines.length === 0) return;
                const firstLine = lines[0];
                const delimiter = firstLine.includes(';') ? ';' : (firstLine.includes('\t') ? '\t' : ',');
                const json = lines.map(line => {
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
                    errorSpan.innerText = 'Gagal membaca file Excel.';
                    errorSpan.classList.remove('d-none');
                }
            };
            reader.readAsArrayBuffer(file);
        } else {
            document.getElementById('btnSubmitImport').disabled = false;
            document.getElementById('btnSubmitCount').innerText = '(File Terpilih)';
        }
    });

    function parsePastedData() {
        const text = document.getElementById('excelPasteArea').value;
        if (!text.trim()) return;
        
        const rows = text.split('\n');
        const json = rows.map(row => row.split('\t'));
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
        const idxJk = headers.findIndex(h => h === 'l/p' || h.includes('kelamin') || h.includes('jk') || h === 'lp');
        const idxPass = headers.findIndex(h => h.includes('pass'));

        if (idxNis === -1 || idxNama === -1) {
            errorSpan.innerText = 'Kolom NIS atau Nama tidak ditemukan pada baris pertama.';
            errorSpan.classList.remove('d-none');
            return;
        }

        for (let i = 1; i < data.length; i++) {
            const row = data[i];
            if (!row || row.length === 0 || (!row[idxNis] && !row[idxNama])) continue;

            parsedStudents.push({
                nis: String(row[idxNis] || '').trim(),
                nama: String(row[idxNama] || '').trim(),
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
            tbody.innerHTML = '<tr><td colspan="3" class="text-center text-muted">Data kosong.</td></tr>';
            document.getElementById('btnSubmitImport').disabled = true;
            document.getElementById('btnSubmitCount').innerText = '';
            return;
        }

        parsedStudents.slice(0, 100).forEach(st => {
            tbody.innerHTML += `
                <tr>
                    <td>${st.nis}</td>
                    <td>${st.nama}</td>
                    <td>${st.jenis_kelamin || '-'}</td>
                </tr>
            `;
        });
        
        if (parsedStudents.length > 100) {
            tbody.innerHTML += `<tr><td colspan="3" class="text-center text-muted">... dan ${parsedStudents.length - 100} data lainnya</td></tr>`;
        }
        
        document.getElementById('studentsDataInput').value = JSON.stringify(parsedStudents);
        document.getElementById('btnSubmitImport').disabled = false;
        document.getElementById('btnSubmitCount').innerText = parsedStudents.length;
    }
</script>
@endsection

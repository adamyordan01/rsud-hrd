@extends('layouts.app')

@section('title', 'Backup Data Karyawan')

@push('styles')
<style>
    .backup-card {
        transition: transform 0.2s;
        cursor: pointer;
    }
    
    .backup-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 4px 25px 0 rgba(0,0,0,.1);
    }

    .progress-custom {
        height: 20px;
        margin: 10px 0;
        background-color: #f0f0f0;
        border-radius: 10px;
        overflow: hidden;
    }

    .progress-bar-custom {
        height: 100%;
        background: linear-gradient(45deg, #1bc5bd, #0bb7af);
        transition: width 0.3s ease;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: bold;
    }

    .backup-status {
        border-radius: 10px;
        padding: 15px;
        margin-bottom: 20px;
    }

    .backup-required {
        background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
        border: 1px solid #f1aeb5;
        color: #721c24;
    }

    .backup-complete {
        background: linear-gradient(135deg, #d1ecf1 0%, #bee5eb 100%);
        border: 1px solid #b6d7dd;
        color: #0c5460;
    }

    .backup-history-table {
        background: white;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 2px 15px rgba(0,0,0,0.1);
    }

    .backup-history-table th {
        background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
        color: white;
        border: none;
        padding: 15px;
    }

    .backup-history-table td {
        padding: 12px 15px;
        border-bottom: 1px solid #f0f0f0;
    }

    .backup-history-table tr:last-child td {
        border-bottom: none;
    }

    .btn-backup {
        background: linear-gradient(135deg, #1bc5bd 0%, #0bb7af 100%);
        border: none;
        color: white;
        padding: 12px 30px;
        border-radius: 25px;
        font-weight: bold;
        transition: all 0.3s ease;
    }

    .btn-backup:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        color: white;
    }

    .btn-backup:disabled {
        background: #6c757d;
        cursor: not-allowed;
        transform: none;
        box-shadow: none;
    }

    .loading-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.8);
        z-index: 9999;
        display: none;
        justify-content: center;
        align-items: center;
    }

    .loading-content {
        background: white;
        padding: 40px;
        border-radius: 15px;
        text-align: center;
        max-width: 400px;
        width: 90%;
    }

    .loading-spinner {
        width: 50px;
        height: 50px;
        border: 5px solid #f3f3f3;
        border-top: 5px solid #1bc5bd;
        border-radius: 50%;
        animation: spin 1s linear infinite;
        margin: 0 auto 20px;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
</style>
@endpush

@section('toolbar')
    <div id="kt_app_toolbar" class="app-toolbar pt-6 pb-2">
        <div id="kt_app_toolbar_container" class="app-container container-fluid d-flex align-items-stretch">
            <div class="app-toolbar-wrapper d-flex flex-stack flex-wrap gap-4 w-100">
                <div class="page-title d-flex flex-column justify-content-center gap-1 me-3">
                    <h1 class="page-heading d-flex flex-column justify-content-center text-gray-900 fw-bold fs-3 m-0">
                        Backup Data Karyawan
                    </h1>
                    <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0">
                        <li class="breadcrumb-item text-muted">
                            <a href="{{ route('admin.dashboard.index') }}" class="text-muted text-hover-primary">Admin</a>
                        </li>
                        <li class="breadcrumb-item">
                            <span class="bullet bg-gray-500 w-5px h-2px"></span>
                        </li>
                        <li class="breadcrumb-item text-muted">Backup Data</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('content')
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div id="kt_app_content_container" class="app-container container-fluid">
            
            <!-- Backup Status Card -->
            <div class="row mb-8">
                <div class="col-12">
                    @if($requirement['is_required'])
                    <div class="backup-status backup-required">
                        <div class="d-flex align-items-center">
                            <i class="ki-duotone ki-warning fs-2x me-4">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            <div class="flex-grow-1">
                                <h4 class="mb-1"><strong>⚠️ Backup Diperlukan!</strong></h4>
                                <p class="mb-0">
                                    Data karyawan untuk periode <strong>{{ $requirement['required_month_name'] }} {{ $requirement['required_year'] }}</strong> 
                                    belum dibackup. Silakan lakukan backup sekarang.
                                </p>
                            </div>
                            @if($canBackup)
                            <button class="btn btn-backup" onclick="performBackup()">
                                <i class="ki-duotone ki-save fs-2 me-2">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                                Backup Sekarang
                            </button>
                            @endif
                        </div>
                    </div>
                    @else
                    <div class="backup-status backup-complete">
                        <div class="d-flex align-items-center">
                            <i class="ki-duotone ki-check-circle fs-2x me-4">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            <div class="flex-grow-1">
                                <h4 class="mb-1"><strong>✅ Backup Terkini</strong></h4>
                                <p class="mb-0">
                                    Backup untuk periode <strong>{{ $requirement['required_month_name'] }} {{ $requirement['required_year'] }}</strong> 
                                    sudah tersedia ({{ number_format($requirement['backup_count']) }} records).
                                </p>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="row gy-5 g-xl-10 mb-8">
                <div class="col-xl-4">
                    <div class="card backup-card h-100">
                        <div class="card-body text-center py-8">
                            <i class="ki-duotone ki-save fs-3x text-primary mb-4">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            <h4 class="card-title mb-3">Backup Manual</h4>
                            <p class="text-gray-600 mb-4">Lakukan backup data karyawan secara manual untuk periode tertentu</p>
                            @if($canBackup)
                            <button class="btn btn-backup" onclick="showManualBackupModal()">
                                Backup Manual
                            </button>
                            @else
                            <button class="btn btn-secondary" disabled>
                                Tidak Ada Izin
                            </button>
                            @endif
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-4">
                    <div class="card backup-card h-100">
                        <div class="card-body text-center py-8">
                            <i class="ki-duotone ki-chart-line fs-3x text-success mb-4">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            <h4 class="card-title mb-3">Status Backup</h4>
                            <p class="text-gray-600 mb-4">Lihat status dan informasi backup terkini</p>
                            <button class="btn btn-light-success" onclick="refreshBackupStatus()">
                                <i class="ki-duotone ki-refresh fs-2 me-2">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                                Refresh Status
                            </button>
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-4">
                    <div class="card backup-card h-100">
                        <div class="card-body text-center py-8">
                            <i class="ki-duotone ki-folder fs-3x text-warning mb-4">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            <h4 class="card-title mb-3">Riwayat Backup</h4>
                            <p class="text-gray-600 mb-4">Lihat riwayat backup yang pernah dilakukan</p>
                            <button class="btn btn-light-warning" onclick="refreshBackupHistory()">
                                <i class="ki-duotone ki-time fs-2 me-2">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                                Lihat Riwayat
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Backup History -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header pt-7">
                            <h3 class="card-title align-items-start flex-column">
                                <span class="card-label fw-bold text-gray-900">Riwayat Backup</span>
                                <span class="text-gray-500 mt-1 fw-semibold fs-6">10 backup terakhir yang dilakukan</span>
                            </h3>
                            <div class="card-toolbar">
                                <button class="btn btn-sm btn-light-primary" onclick="refreshBackupHistory()">
                                    <i class="ki-duotone ki-refresh fs-4 me-1">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    Refresh
                                </button>
                            </div>
                        </div>
                        <div class="card-body pt-5">
                            <div class="table-responsive">
                                <table class="table table-row-dashed align-middle gs-0 gy-4 backup-history-table" id="backupHistoryTable">
                                    <thead>
                                        <tr>
                                            <th>Periode</th>
                                            <th>Total Records</th>
                                            <th>Tanggal Backup</th>
                                            <th>Status</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if($history['success'] && count($history['data']) > 0)
                                            @foreach($history['data'] as $item)
                                            <tr>
                                                <td>
                                                    <span class="fw-bold">
                                                        @php
                                                            $monthNames = [
                                                                '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April',
                                                                '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus',
                                                                '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
                                                            ];
                                                        @endphp
                                                        {{ $monthNames[$item->bulan] }} {{ $item->tahun }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="badge badge-light-primary">{{ number_format($item->total_records) }}</span>
                                                </td>
                                                <td>{{ \Carbon\Carbon::parse($item->backup_date)->format('d/m/Y H:i') }}</td>
                                                <td>
                                                    <span class="badge badge-light-success">Berhasil</span>
                                                </td>
                                                <td>
                                                    <button class="btn btn-sm btn-light-info" onclick="viewBackupData('{{ $item->bulan }}', '{{ $item->tahun }}')">
                                                        <i class="ki-duotone ki-eye fs-5">
                                                            <span class="path1"></span>
                                                            <span class="path2"></span>
                                                            <span class="path3"></span>
                                                        </i>
                                                        Lihat Data
                                                    </button>
                                                </td>
                                            </tr>
                                            @endforeach
                                        @else
                                        <tr>
                                            <td colspan="5" class="text-center text-gray-500 py-8">
                                                Belum ada riwayat backup
                                            </td>
                                        </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Loading Overlay -->
    <div class="loading-overlay" id="loadingOverlay">
        <div class="loading-content">
            <div class="loading-spinner"></div>
            <h4 id="loadingTitle">Memproses Backup...</h4>
            <p id="loadingMessage">Sedang melakukan backup data karyawan. Mohon tunggu dan jangan menutup halaman ini.</p>
            <div class="progress-custom">
                <div class="progress-bar-custom" id="loadingProgress" style="width: 0%">0%</div>
            </div>
        </div>
    </div>

    <!-- Manual Backup Modal -->
    <div class="modal fade" id="manualBackupModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Backup Manual</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="manualBackupForm">
                        <div class="mb-4">
                            <label class="form-label">Bulan</label>
                            <select class="form-select" name="month" required>
                                <option value="">Pilih Bulan</option>
                                <option value="1">Januari</option>
                                <option value="2">Februari</option>
                                <option value="3">Maret</option>
                                <option value="4">April</option>
                                <option value="5">Mei</option>
                                <option value="6">Juni</option>
                                <option value="7">Juli</option>
                                <option value="8">Agustus</option>
                                <option value="9">September</option>
                                <option value="10">Oktober</option>
                                <option value="11">November</option>
                                <option value="12">Desember</option>
                            </select>
                        </div>
                        <div class="mb-4">
                            <label class="form-label">Tahun</label>
                            <select class="form-select" name="year" required>
                                <option value="">Pilih Tahun</option>
                                @for($year = date('Y'); $year >= 2020; $year--)
                                <option value="{{ $year }}">{{ $year }}</option>
                                @endfor
                            </select>
                        </div>
                        <div class="form-check mb-4">
                            <input class="form-check-input" type="checkbox" name="force" id="forceBackup">
                            <label class="form-check-label" for="forceBackup">
                                Force backup (timpa jika sudah ada)
                            </label>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-backup" onclick="performManualBackup()">
                        <i class="ki-duotone ki-save fs-2 me-2">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                        Mulai Backup
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    function performBackup() {
        showLoading('Memproses Backup Otomatis...', 'Sedang melakukan backup data karyawan periode yang diperlukan.');
        
        fetch('{{ route("admin.backup.perform") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            hideLoading();
            
            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Backup Berhasil!',
                    html: `
                        <p>${data.message}</p>
                        <div class="mt-3">
                            <strong>Total Records:</strong> ${data.data.backup_count}<br>
                            <strong>Waktu Backup:</strong> ${data.data.backup_time}
                        </div>
                    `,
                    confirmButtonText: 'OK'
                }).then(() => {
                    location.reload();
                });
            } else {
                if (data.exists) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Backup Sudah Ada',
                        html: `
                            <p>${data.message}</p>
                            <p>Apakah Anda ingin melakukan backup ulang?</p>
                        `,
                        showCancelButton: true,
                        confirmButtonText: 'Ya, Backup Ulang',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            performBackupForce();
                        }
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Backup Gagal',
                        text: data.message
                    });
                }
            }
        })
        .catch(error => {
            hideLoading();
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Terjadi kesalahan saat melakukan backup: ' + error.message
            });
        });
    }

    function performBackupForce() {
        showLoading('Memproses Backup Ulang...', 'Sedang melakukan backup ulang dengan menimpa data sebelumnya.');
        
        fetch('{{ route("admin.backup.perform") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ force: true })
        })
        .then(response => response.json())
        .then(data => {
            hideLoading();
            
            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Backup Berhasil!',
                    text: data.message
                }).then(() => {
                    location.reload();
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Backup Gagal',
                    text: data.message
                });
            }
        })
        .catch(error => {
            hideLoading();
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Terjadi kesalahan: ' + error.message
            });
        });
    }

    function showManualBackupModal() {
        $('#manualBackupModal').modal('show');
    }

    function performManualBackup() {
        const form = document.getElementById('manualBackupForm');
        const formData = new FormData(form);
        
        const month = formData.get('month');
        const year = formData.get('year');
        const force = formData.get('force') === 'on';
        
        if (!month || !year) {
            Swal.fire({
                icon: 'warning',
                title: 'Data Tidak Lengkap',
                text: 'Silakan pilih bulan dan tahun terlebih dahulu.'
            });
            return;
        }
        
        $('#manualBackupModal').modal('hide');
        showLoading('Memproses Backup Manual...', `Sedang melakukan backup untuk periode ${getMonthName(month)} ${year}.`);
        
        fetch('{{ route("admin.backup.perform") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ 
                month: parseInt(month), 
                year: parseInt(year), 
                force: force 
            })
        })
        .then(response => response.json())
        .then(data => {
            hideLoading();
            
            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Backup Berhasil!',
                    text: data.message
                }).then(() => {
                    refreshBackupHistory();
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Backup Gagal',
                    text: data.message
                });
            }
        })
        .catch(error => {
            hideLoading();
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Terjadi kesalahan: ' + error.message
            });
        });
    }

    function refreshBackupStatus() {
        fetch('{{ route("admin.backup.status") }}')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            }
        })
        .catch(error => {
            console.error('Error refreshing backup status:', error);
        });
    }

    function refreshBackupHistory() {
        fetch('{{ route("admin.backup.history") }}')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateBackupHistoryTable(data.data);
            }
        })
        .catch(error => {
            console.error('Error refreshing backup history:', error);
        });
    }

    function updateBackupHistoryTable(history) {
        const tbody = document.querySelector('#backupHistoryTable tbody');
        tbody.innerHTML = '';
        
        if (history.length === 0) {
            tbody.innerHTML = '<tr><td colspan="5" class="text-center text-gray-500 py-8">Belum ada riwayat backup</td></tr>';
            return;
        }
        
        history.forEach(item => {
            const row = `
                <tr>
                    <td><span class="fw-bold">${getMonthName(item.bulan)} ${item.tahun}</span></td>
                    <td><span class="badge badge-light-primary">${item.total_records.toLocaleString()}</span></td>
                    <td>${formatDate(item.backup_date)}</td>
                    <td><span class="badge badge-light-success">Berhasil</span></td>
                    <td>
                        <button class="btn btn-sm btn-light-info" onclick="viewBackupData('${item.bulan}', '${item.tahun}')">
                            <i class="ki-duotone ki-eye fs-5"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
                            Lihat Data
                        </button>
                    </td>
                </tr>
            `;
            tbody.innerHTML += row;
        });
    }

    function viewBackupData(month, year) {
        window.open(`{{ route("admin.backup.data") }}?month=${month}&year=${year}`, '_blank');
    }

    function showLoading(title, message) {
        document.getElementById('loadingTitle').textContent = title;
        document.getElementById('loadingMessage').textContent = message;
        document.getElementById('loadingOverlay').style.display = 'flex';
        
        // Simulate progress
        let progress = 0;
        const progressBar = document.getElementById('loadingProgress');
        const interval = setInterval(() => {
            progress += Math.random() * 10;
            if (progress > 90) progress = 90;
            
            progressBar.style.width = progress + '%';
            progressBar.textContent = Math.round(progress) + '%';
        }, 200);
        
        // Store interval to clear it later
        window.loadingInterval = interval;
    }

    function hideLoading() {
        document.getElementById('loadingOverlay').style.display = 'none';
        if (window.loadingInterval) {
            clearInterval(window.loadingInterval);
        }
        
        // Reset progress
        const progressBar = document.getElementById('loadingProgress');
        progressBar.style.width = '0%';
        progressBar.textContent = '0%';
    }

    function getMonthName(month) {
        const months = {
            '01': 'Januari', '1': 'Januari',
            '02': 'Februari', '2': 'Februari',
            '03': 'Maret', '3': 'Maret',
            '04': 'April', '4': 'April',
            '05': 'Mei', '5': 'Mei',
            '06': 'Juni', '6': 'Juni',
            '07': 'Juli', '7': 'Juli',
            '08': 'Agustus', '8': 'Agustus',
            '09': 'September', '9': 'September',
            '10': 'Oktober',
            '11': 'November',
            '12': 'Desember'
        };
        return months[month] || month;
    }

    function formatDate(dateString) {
        const date = new Date(dateString);
        return date.toLocaleString('id-ID', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
    }
</script>
@endpush
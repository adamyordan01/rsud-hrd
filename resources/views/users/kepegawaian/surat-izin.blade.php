@extends('layouts.user', ['title' => 'Surat Izin'])

@push('styles')
<style>
    .izin-card {
        transition: all 0.3s;
        border-left: 4px solid transparent;
    }
    
    .izin-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
    }
    
    .izin-cuti { border-left-color: #009ef7; }
    .izin-sakit { border-left-color: #f1416c; }
    .izin-lainnya { border-left-color: #ffc700; }
    
    .status-badge {
        font-size: 0.75rem;
        padding: 6px 12px;
        border-radius: 20px;
    }
    
    .filter-tabs {
        border-bottom: 1px solid #e1e3ea;
        margin-bottom: 2rem;
    }
    
    .filter-tab {
        padding: 12px 24px;
        border: none;
        background: transparent;
        color: #5e6278;
        font-weight: 600;
        border-bottom: 3px solid transparent;
        transition: all 0.3s;
    }
    
    .filter-tab.active {
        color: var(--bs-user-primary);
        border-bottom-color: var(--bs-user-primary);
    }
    
    .filter-tab:hover {
        color: var(--bs-user-primary);
    }
</style>
@endpush

@section('toolbar')
<div id="kt_app_toolbar" class="app-toolbar pt-6 pb-2">
    <div id="kt_app_toolbar_container" class="app-container container-fluid d-flex align-items-stretch">
        <div class="app-toolbar-wrapper d-flex flex-stack flex-wrap gap-4 w-100">
            <div class="page-title d-flex flex-column justify-content-center gap-1 me-3">
                <h1 class="page-heading d-flex flex-column justify-content-center text-gray-900 fw-bold fs-3 m-0">
                    Surat Izin
                </h1>
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 user-breadcrumb">
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('user.dashboard') }}" class="text-muted text-hover-primary">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('user.kepegawaian.index') }}" class="text-muted text-hover-primary">Data Kepegawaian</a>
                    </li>
                    <li class="breadcrumb-item text-muted">Surat Izin</li>
                </ul>
            </div>
            <div class="d-flex align-items-center gap-2 gap-lg-3">
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modal_add_surat">
                    <i class="ki-duotone ki-plus fs-2">
                        <span class="path1"></span>
                        <span class="path2"></span>
                        <span class="path3"></span>
                    </i>
                    Tambah Surat Izin
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('content')
<div id="kt_app_content" class="app-content flex-column-fluid">
    <div id="kt_app_content_container" class="app-container container-fluid">
        
        {{-- Summary Stats --}}
        <div class="row g-6 g-xl-9 mb-8">
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <i class="ki-duotone ki-calendar fs-3x text-primary mb-3">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                        <h3 class="text-gray-900 fw-bold">{{ $suratIzin->where('jenis_izin', 'like', '%cuti%')->count() }}</h3>
                        <div class="text-muted">Total Cuti</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <i class="ki-duotone ki-cross-circle fs-3x text-danger mb-3">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                        <h3 class="text-gray-900 fw-bold">{{ $suratIzin->where('jenis_izin', 'like', '%sakit%')->count() }}</h3>
                        <div class="text-muted">Izin Sakit</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <i class="ki-duotone ki-time fs-3x text-warning mb-3">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                        <h3 class="text-gray-900 fw-bold">{{ $suratIzin->where('tgl_mulai', '>=', date('Y-01-01'))->count() }}</h3>
                        <div class="text-muted">{{ date('Y') }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <i class="ki-duotone ki-chart-pie fs-3x text-success mb-3">
                            <span class="path1"></span>
                            <span class="path2"></span>
                            <span class="path3"></span>
                        </i>
                        <h3 class="text-gray-900 fw-bold">{{ $suratIzin->count() }}</h3>
                        <div class="text-muted">Total Surat</div>
                    </div>
                </div>
            </div>
        </div>
        
        {{-- Filter Tabs --}}
        <div class="filter-tabs">
            <button class="filter-tab active" data-filter="all">Semua</button>
            <button class="filter-tab" data-filter="cuti">Cuti</button>
            <button class="filter-tab" data-filter="sakit">Izin Sakit</button>
            <button class="filter-tab" data-filter="lainnya">Lainnya</button>
        </div>
        
        {{-- Filter Form --}}
        <div class="card mb-8">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Tahun</label>
                        <select class="form-select" id="filter-tahun">
                            <option value="">Semua Tahun</option>
                            @php
                                $tahunList = $suratIzin->pluck('tgl_mulai')->map(function($tgl) {
                                    return date('Y', strtotime($tgl));
                                })->unique()->sort()->values();
                            @endphp
                            @foreach($tahunList as $tahun)
                                <option value="{{ $tahun }}" {{ request('tahun') == $tahun ? 'selected' : '' }}>
                                    {{ $tahun }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Bulan</label>
                        <select class="form-select" id="filter-bulan">
                            <option value="">Semua Bulan</option>
                            @for($i = 1; $i <= 12; $i++)
                                <option value="{{ $i }}" {{ request('bulan') == $i ? 'selected' : '' }}>
                                    {{ DateTime::createFromFormat('!m', $i)->format('F') }}
                                </option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Pencarian</label>
                        <input type="text" class="form-control" id="filter-search" 
                               placeholder="Cari keterangan atau alasan..." 
                               value="{{ request('search') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-bold d-block">&nbsp;</label>
                        <button type="button" class="btn btn-primary w-100" id="btn-filter">
                            <i class="ki-duotone ki-funnel fs-3">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            Filter
                        </button>
                    </div>
                </div>
            </div>
        </div>
        
        {{-- Izin List --}}
        <div class="row g-6 g-xl-9" id="izin-container">
            @forelse($suratIzin as $izin)
                <div class="col-xl-6 izin-item" 
                     data-jenis="{{ strtolower($izin->jenis_izin ?? 'lainnya') }}" 
                     data-tahun="{{ date('Y', strtotime($izin->tgl_mulai)) }}"
                     data-bulan="{{ date('n', strtotime($izin->tgl_mulai)) }}">
                    <div class="card izin-card izin-{{ strtolower(str_replace(' ', '', $izin->jenis_izin ?? 'lainnya')) }} h-100">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center w-100">
                                <h5 class="card-title text-gray-900 fw-bold mb-0">{{ $izin->jenis_izin ?? 'Izin' }}</h5>
                                <span class="badge badge-light-success">
                                    <i class="ki-duotone ki-check fs-7 me-1">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    Aktif
                                </span>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-6">
                                    <strong class="text-muted fs-7">Tanggal Mulai:</strong>
                                    <p class="text-gray-800 fw-semibold mb-0">
                                        {{ date('d/m/Y', strtotime($izin->tgl_mulai)) }}
                                    </p>
                                </div>
                                <div class="col-6">
                                    <strong class="text-muted fs-7">Tanggal Selesai:</strong>
                                    <p class="text-gray-800 fw-semibold mb-0">
                                        {{ date('d/m/Y', strtotime($izin->tgl_akhir)) }}
                                    </p>
                                </div>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-6">
                                    <strong class="text-muted fs-7">Lama Izin:</strong>
                                    <p class="text-gray-800 fw-semibold mb-0">
                                        {{ \Carbon\Carbon::parse($izin->tgl_mulai)->diffInDays(\Carbon\Carbon::parse($izin->tgl_akhir)) + 1 }} hari
                                    </p>
                                </div>
                                <div class="col-6">
                                    <strong class="text-muted fs-7">No. Surat:</strong>
                                    <p class="text-gray-800 fw-semibold mb-0">
                                        {{ str_pad($izin->kd_surat, 3, '0', STR_PAD_LEFT) }}/IZIN/RSUD-IM/{{ \Carbon\Carbon::parse($izin->tgl_mulai)->format('Y') }}
                                    </p>
                                </div>
                            </div>
                            
                            @if($izin->alasan)
                                <div class="mb-3">
                                    <strong class="text-muted fs-7">Alasan:</strong>
                                    <p class="text-gray-800 mb-0">{{ $izin->alasan }}</p>
                                </div>
                            @endif
                            
                            @if(isset($izin->keterangan) && $izin->keterangan)
                                <div class="mb-3">
                                    <strong class="text-muted fs-7">Keterangan:</strong>
                                    <p class="text-gray-800 mb-0">{{ $izin->keterangan }}</p>
                                </div>
                            @endif
                            
                            @if(isset($izin->pengganti) && $izin->pengganti)
                                <div class="mb-3">
                                    <strong class="text-muted fs-7">Pengganti:</strong>
                                    <p class="text-gray-800 mb-0">{{ $izin->pengganti }}</p>
                                </div>
                            @endif
                        </div>
                        <div class="card-footer bg-light">
                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-muted">
                                    <i class="ki-duotone ki-time fs-7 me-1">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    {{ date('d/m/Y', strtotime($izin->tgl_mulai)) }}
                                </small>
                                <div class="d-flex gap-1">
                                    @if(isset($izin->file_surat) && $izin->file_surat)
                                        <button type="button" class="btn btn-light-primary btn-sm" 
                                                onclick="downloadIzin('{{ $izin->kd_surat ?? $izin->id }}')">
                                            <i class="ki-duotone ki-down fs-5">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                        </button>
                                    @endif
                                    
                                    <button type="button" class="btn btn-light-info btn-sm" 
                                            onclick="printSurat('{{ $izin->kd_surat ?? $izin->id }}')"
                                            title="Cetak">
                                        <i class="ki-duotone ki-printer fs-5">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                            <span class="path3"></span>
                                        </i>
                                    </button>
                                    
                                    <button type="button" class="btn btn-light-warning btn-sm" 
                                            onclick="editSurat('{{ $izin->kd_surat ?? $izin->id }}')"
                                            title="Edit">
                                        <i class="ki-duotone ki-pencil fs-5">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                    </button>
                                    
                                    <button type="button" class="btn btn-light-danger btn-sm" 
                                            onclick="deleteSurat('{{ $izin->kd_surat ?? $izin->id }}')"
                                            title="Hapus">
                                        <i class="ki-duotone ki-trash fs-5">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                            <span class="path3"></span>
                                            <span class="path4"></span>
                                            <span class="path5"></span>
                                        </i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="card">
                        <div class="card-body text-center py-10">
                            <i class="ki-duotone ki-calendar fs-3x text-muted mb-5">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            <h5 class="text-muted mb-3">Belum Ada Surat Izin</h5>
                            <p class="text-muted">Riwayat izin akan muncul di sini setelah pengajuan diproses.</p>
                        </div>
                    </div>
                </div>
            @endforelse
        </div>
        
        {{-- Pagination --}}
        @if($suratIzin->hasPages())
            <div class="d-flex justify-content-center mt-8">
                {{ $suratIzin->links() }}
            </div>
        @endif
        
    </div>
</div>
@endsection

{{-- Modal Add/Edit Surat Izin --}}
<div class="modal fade" id="modal_add_surat" tabindex="-1" aria-labelledby="modal_add_surat_label" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal_add_surat_label">Tambah Surat Izin</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="form_surat_izin">
                <div class="modal-body">
                    <input type="hidden" id="edit_id" name="edit_id">
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="jenis_surat" class="form-label required">Jenis Surat</label>
                            <select class="form-select" id="jenis_surat" name="jenis_surat" required>
                                <option value="">Pilih Jenis Surat</option>
                                @foreach($jenisSurat as $jenis)
                                    <option value="{{ $jenis->kd_jenis_surat }}">{{ $jenis->jenis_surat }}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="kategori_izin" class="form-label required">Kategori Izin</label>
                            <select class="form-select" id="kategori_izin" name="kategori_izin" required>
                                <option value="">Pilih Kategori Izin</option>
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="tgl_mulai" class="form-label required">Tanggal Mulai</label>
                            <input type="date" class="form-control" id="tgl_mulai" name="tgl_mulai" required>
                            <div class="invalid-feedback"></div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="tgl_selesai" class="form-label required">Tanggal Selesai</label>
                            <input type="date" class="form-control" id="tgl_selesai" name="tgl_selesai" required>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="alasan" class="form-label required">Alasan</label>
                        <textarea class="form-control" id="alasan" name="alasan" rows="3" required maxlength="255"></textarea>
                        <div class="form-text">Maksimal 255 karakter</div>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="btn_submit">
                        <span class="indicator-label">Simpan</span>
                        <span class="indicator-progress">
                            Menyimpan... <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Tab filtering
    $('.filter-tab').click(function() {
        $('.filter-tab').removeClass('active');
        $(this).addClass('active');
        
        const filter = $(this).data('filter');
        filterByJenis(filter);
    });
    
    // Form filtering
    $('#btn-filter').click(function() {
        applyFilters();
    });
    
    // Jenis surat change handler
    $('#jenis_surat').change(function() {
        const kdJenisSurat = $(this).val();
        if (kdJenisSurat) {
            loadKategoriIzin(kdJenisSurat);
        } else {
            $('#kategori_izin').html('<option value="">Pilih Kategori Izin</option>');
        }
    });
    
    // Form submission
    $('#form_surat_izin').on('submit', function(e) {
        e.preventDefault();
        
        const form = $(this);
        const submitBtn = $('#btn_submit');
        const editId = $('#edit_id').val();
        
        // Remove existing validation states
        form.find('.is-invalid').removeClass('is-invalid');
        form.find('.invalid-feedback').text('');
        
        // Show loading state
        submitBtn.attr('data-kt-indicator', 'on');
        
        const url = editId ? 
            `{{ route('user.kepegawaian.surat-izin.update', '') }}/${editId}` : 
            `{{ route('user.kepegawaian.surat-izin.store') }}`;
        
        const method = editId ? 'PUT' : 'POST';
        
        // Prepare form data
        let formData = {
            jenis_surat: $('#jenis_surat').val(),
            tgl_mulai: $('#tgl_mulai').val(),
            tgl_selesai: $('#tgl_selesai').val(),
            kategori_izin: $('#kategori_izin').val(),
            alasan: $('#alasan').val()
        };
        
        if (method === 'PUT') {
            formData._method = 'PUT';
        }
        
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        
        $.ajax({
            url: url,
            method: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message);
                    $('#modal_add_surat').modal('hide');
                    location.reload(); // Reload page to show new data
                } else {
                    toastr.error(response.message || 'Terjadi kesalahan');
                }
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    const errors = xhr.responseJSON.errors;
                    $.each(errors, function(field, messages) {
                        const fieldElement = $(`#${field}`);
                        fieldElement.addClass('is-invalid');
                        fieldElement.siblings('.invalid-feedback').text(messages[0]);
                    });
                } else {
                    toastr.error(xhr.responseJSON?.message || 'Terjadi kesalahan sistem');
                }
            },
            complete: function() {
                submitBtn.removeAttr('data-kt-indicator');
            }
        });
    });
    
    // Reset modal when closed
    $('#modal_add_surat').on('hidden.bs.modal', function() {
        resetForm();
    });
    
    function loadKategoriIzin(kdJenisSurat) {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        
        $.ajax({
            url: `{{ route('user.kepegawaian.surat-izin.get-kategori-by-jenis') }}`,
            method: 'POST',
            data: { kd_jenis_surat: kdJenisSurat },
            success: function(response) {
                $('#kategori_izin').html(response);
            },
            error: function() {
                toastr.error('Gagal memuat kategori izin');
            }
        });
    }
    
    function resetForm() {
        const form = $('#form_surat_izin')[0];
        form.reset();
        $('#edit_id').val('');
        $('#modal_add_surat_label').text('Tambah Surat Izin');
        $('#btn_submit .indicator-label').text('Simpan');
        
        // Reset validation states
        $('#form_surat_izin').find('.is-invalid').removeClass('is-invalid');
        $('#form_surat_izin').find('.invalid-feedback').text('');
    }
    
    function filterByJenis(jenis) {
        $('.izin-item').each(function() {
            const item = $(this);
            const itemJenis = item.data('jenis');
            
            if(jenis === 'all' || itemJenis === jenis) {
                item.show();
            } else {
                item.hide();
            }
        });
        
        checkNoResults();
    }
    
    function applyFilters() {
        const tahun = $('#filter-tahun').val();
        const bulan = $('#filter-bulan').val();
        const search = $('#filter-search').val().toLowerCase();
        const activeTab = $('.filter-tab.active').data('filter');
        
        $('.izin-item').each(function() {
            const item = $(this);
            const itemJenis = item.data('jenis');
            const itemTahun = item.data('tahun').toString();
            const itemBulan = item.data('bulan').toString();
            const itemText = item.text().toLowerCase();
            
            let show = true;
            
            // Filter by active tab
            if(activeTab !== 'all' && itemJenis !== activeTab) show = false;
            
            // Filter by form inputs
            if(tahun && itemTahun !== tahun) show = false;
            if(bulan && itemBulan !== bulan) show = false;
            if(search && !itemText.includes(search)) show = false;
            
            if(show) {
                item.show();
            } else {
                item.hide();
            }
        });
        
        checkNoResults();
    }
    
    function checkNoResults() {
        const visibleItems = $('.izin-item:visible').length;
        if(visibleItems === 0) {
            if(!$('#no-results').length) {
                $('#izin-container').append(`
                    <div class="col-12" id="no-results">
                        <div class="card">
                            <div class="card-body text-center py-10">
                                <i class="ki-duotone ki-magnifier fs-3x text-muted mb-5">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                                <h5 class="text-muted mb-3">Tidak Ada Hasil</h5>
                                <p class="text-muted">Tidak ditemukan data sesuai dengan filter yang dipilih.</p>
                            </div>
                        </div>
                    </div>
                `);
            }
        } else {
            $('#no-results').remove();
        }
    }
});

function downloadIzin(izinId) {
    const downloadUrl = `{{ route('user.kepegawaian.download-izin', ':id') }}`.replace(':id', izinId);
    window.open(downloadUrl, '_blank');
}

// Functions accessible globally
function editSurat(id) {
    $.ajax({
        url: `{{ route('user.kepegawaian.surat-izin.edit', '') }}/${id}`,
        method: 'GET',
        success: function(response) {
            if (response.success) {
                const data = response.data;
                
                $('#edit_id').val(data.kd_surat);
                $('#jenis_surat').val(data.jenis_surat).trigger('change');
                $('#tgl_mulai').val(data.tgl_mulai);
                $('#tgl_selesai').val(data.tgl_selesai);
                $('#alasan').val(data.alasan);
                
                // Wait for kategori to load then set value
                setTimeout(function() {
                    $('#kategori_izin').val(data.kategori_izin);
                }, 500);
                
                $('#modal_add_surat_label').text('Edit Surat Izin');
                $('#btn_submit .indicator-label').text('Update');
                $('#modal_add_surat').modal('show');
            }
        },
        error: function() {
            toastr.error('Gagal memuat data surat izin');
        }
    });
}

function deleteSurat(id) {
    Swal.fire({
        title: 'Hapus Surat Izin?',
        text: 'Data yang dihapus tidak dapat dikembalikan!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal',
        customClass: {
            confirmButton: 'btn btn-danger',
            cancelButton: 'btn btn-secondary'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            
            $.ajax({
                url: `{{ route('user.kepegawaian.surat-izin.destroy', '') }}/${id}`,
                method: 'DELETE',
                success: function(response) {
                    if (response.success) {
                        toastr.success(response.message);
                        location.reload();
                    } else {
                        toastr.error(response.message || 'Terjadi kesalahan');
                    }
                },
                error: function(xhr) {
                    toastr.error(xhr.responseJSON?.message || 'Terjadi kesalahan sistem');
                }
            });
        }
    });
}

function printSurat(id) {
    const printUrl = `{{ route('user.kepegawaian.surat-izin.print', ':id') }}`.replace(':id', id);
    window.open(printUrl, '_blank');
}
</script>
@endpush
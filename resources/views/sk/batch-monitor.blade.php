@extends('layouts.backend')

@section('title', 'Monitor Queue TTE SK')

@section('content')
    <!--begin::Toolbar-->
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        <!--begin::Toolbar container-->
        <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
            <!--begin::Page title-->
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <!--begin::Title-->
                <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                    Monitor Queue TTE SK
                </h1>
                <!--end::Title-->
                <!--begin::Breadcrumb-->
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('admin.dashboard.index') }}" class="text-muted text-hover-primary">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-400 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('admin.sk-kontrak.index') }}" class="text-muted text-hover-primary">SK Kontrak</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-400 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-muted">Monitor Queue</li>
                </ul>
                <!--end::Breadcrumb-->
            </div>
            <!--end::Page title-->
            <!--begin::Actions-->
            <div class="d-flex align-items-center gap-2 gap-lg-3">
                <button type="button" class="btn btn-sm btn-info" id="btn-refresh-data">
                    <i class="ki-duotone ki-arrows-circle fs-2">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                    Refresh
                </button>
                <button type="button" class="btn btn-sm btn-warning" id="btn-queue-worker-status">
                    <i class="ki-duotone ki-pulse fs-2">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                    Status Worker
                </button>
            </div>
            <!--end::Actions-->
        </div>
        <!--end::Toolbar container-->
    </div>
    <!--end::Toolbar-->

    <!--begin::Content-->
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <!--begin::Content container-->
        <div id="kt_app_content_container" class="app-container container-xxl">
            
            <!-- Stats Cards -->
            <div class="row g-5 g-xl-10 mb-5 mb-xl-10">
                <div class="col-md-6 col-lg-6 col-xl-3">
                    <div class="card card-flush bgi-no-repeat bgi-size-contain bgi-position-x-end h-md-50px mb-5 mb-xl-10" style="background-color: #f8f9fa;">
                        <div class="card-header pt-5">
                            <div class="card-title d-flex flex-column">
                                <span class="fs-2hx fw-bold text-dark me-2 lh-1 ls-n2" id="total-batches">0</span>
                                <span class="text-gray-400 pt-1 fw-semibold fs-6">Total Batches</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-6 col-xl-3">
                    <div class="card card-flush bgi-no-repeat bgi-size-contain bgi-position-x-end h-md-50px mb-5 mb-xl-10" style="background-color: #e3f2fd;">
                        <div class="card-header pt-5">
                            <div class="card-title d-flex flex-column">
                                <span class="fs-2hx fw-bold text-primary me-2 lh-1 ls-n2" id="processing-batches">0</span>
                                <span class="text-gray-400 pt-1 fw-semibold fs-6">Sedang Proses</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-6 col-xl-3">
                    <div class="card card-flush bgi-no-repeat bgi-size-contain bgi-position-x-end h-md-50px mb-5 mb-xl-10" style="background-color: #e8f5e8;">
                        <div class="card-header pt-5">
                            <div class="card-title d-flex flex-column">
                                <span class="fs-2hx fw-bold text-success me-2 lh-1 ls-n2" id="completed-batches">0</span>
                                <span class="text-gray-400 pt-1 fw-semibold fs-6">Selesai</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-6 col-xl-3">
                    <div class="card card-flush bgi-no-repeat bgi-size-contain bgi-position-x-end h-md-50px mb-5 mb-xl-10" style="background-color: #ffebee;">
                        <div class="card-header pt-5">
                            <div class="card-title d-flex flex-column">
                                <span class="fs-2hx fw-bold text-danger me-2 lh-1 ls-n2" id="failed-batches">0</span>
                                <span class="text-gray-400 pt-1 fw-semibold fs-6">Gagal</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Queue Jobs Stats -->
            <div class="row g-5 g-xl-10 mb-5 mb-xl-10">
                <div class="col-xl-6">
                    <div class="card card-flush h-md-100">
                        <div class="card-header pt-5">
                            <h3 class="card-title align-items-start flex-column">
                                <span class="card-label fw-bold text-dark">Queue Jobs</span>
                                <span class="text-muted mt-1 fw-semibold fs-7">Jobs dalam antrian</span>
                            </h3>
                        </div>
                        <div class="card-body pt-5">
                            <div class="d-flex flex-wrap">
                                <div class="position-relative d-flex flex-center h-175px w-175px me-15 mb-7">
                                    <div class="position-absolute translate-middle start-50 top-50 d-flex flex-column flex-center">
                                        <span class="fs-2qx fw-bold" id="pending-jobs-count">0</span>
                                        <span class="fs-6 fw-semibold text-gray-400">Pending Jobs</span>
                                    </div>
                                    <canvas id="jobs-chart" width="175" height="175"></canvas>
                                </div>
                                <div class="d-flex flex-column justify-content-center flex-row-fluid pe-11 mb-5">
                                    <div class="d-flex fs-6 fw-semibold align-items-center mb-3">
                                        <div class="bullet bg-primary me-3"></div>
                                        <div class="text-gray-400">SK TTE Queue</div>
                                        <div class="ms-auto fw-bold text-gray-700" id="sk-tte-queue-count">0</div>
                                    </div>
                                    <div class="d-flex fs-6 fw-semibold align-items-center mb-3">
                                        <div class="bullet bg-success me-3"></div>
                                        <div class="text-gray-400">Default Queue</div>
                                        <div class="ms-auto fw-bold text-gray-700" id="default-queue-count">0</div>
                                    </div>
                                    <div class="d-flex fs-6 fw-semibold align-items-center">
                                        <div class="bullet bg-danger me-3"></div>
                                        <div class="text-gray-400">Failed Jobs</div>
                                        <div class="ms-auto fw-bold text-gray-700" id="failed-jobs-count">0</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-6">
                    <div class="card card-flush h-md-100">
                        <div class="card-header pt-5">
                            <h3 class="card-title align-items-start flex-column">
                                <span class="card-label fw-bold text-dark">Worker Status</span>
                                <span class="text-muted mt-1 fw-semibold fs-7">Status queue worker</span>
                            </h3>
                        </div>
                        <div class="card-body pt-5">
                            <div id="worker-status-container">
                                <div class="d-flex align-items-center mb-5">
                                    <div class="symbol symbol-40px me-4">
                                        <span class="symbol-label bg-light-primary">
                                            <i class="ki-duotone ki-pulse fs-2 text-primary">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                        </span>
                                    </div>
                                    <div class="d-flex flex-column flex-grow-1">
                                        <span class="text-gray-800 text-hover-primary fs-6 fw-bold">Queue Worker</span>
                                        <span class="text-muted fw-semibold">Database MySQL Connection</span>
                                    </div>
                                    <span class="badge badge-light-secondary" id="worker-status">Unknown</span>
                                </div>
                                <div class="separator separator-dashed mb-5"></div>
                                <div class="d-flex align-items-center mb-5">
                                    <div class="text-muted fw-semibold fs-7 me-4">Last Processed:</div>
                                    <div class="fw-bold fs-7" id="last-processed">-</div>
                                </div>
                                <div class="d-flex align-items-center">
                                    <div class="text-muted fw-semibold fs-7 me-4">Queue Size:</div>
                                    <div class="fw-bold fs-7" id="queue-size">0 jobs</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Batch Processing Table -->
            <div class="card">
                <div class="card-header border-0 pt-6">
                    <div class="card-title">
                        <div class="d-flex align-items-center position-relative my-1">
                            <i class="ki-duotone ki-magnifier fs-3 position-absolute ms-5">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            <input type="text" data-kt-user-table-filter="search" class="form-control form-control-solid w-250px ps-13" placeholder="Cari batch..." />
                        </div>
                    </div>
                    <div class="card-toolbar">
                        <div class="d-flex justify-content-end" data-kt-user-table-toolbar="base">
                            <button type="button" class="btn btn-light-primary me-3" id="btn-auto-refresh">
                                <i class="ki-duotone ki-arrows-circle fs-2">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                                Auto Refresh: OFF
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body py-4">
                    <div class="table-responsive">
                        <table class="table align-middle table-row-dashed fs-6 gy-5" id="batch-monitor-table">
                            <thead>
                                <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                                    <th class="min-w-50px">ID</th>
                                    <th class="min-w-100px">Urut/Tahun</th>
                                    <th class="min-w-100px">Total Karyawan</th>
                                    <th class="min-w-100px">Progress</th>
                                    <th class="min-w-100px">Status</th>
                                    <th class="min-w-150px">Dibuat</th>
                                    <th class="min-w-150px">Estimasi Selesai</th>
                                    <th class="text-end min-w-100px">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="text-gray-600 fw-semibold" id="batch-monitor-tbody">
                                <!-- Dynamic content -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
        <!--end::Content container-->
    </div>
    <!--end::Content-->

    @include('sk.batch-progress-modal')

@endsection

@push('scripts')
<script>
    let autoRefreshInterval = null;
    let isAutoRefresh = false;

    $(document).ready(function() {
        loadBatchData();
        loadStats();
        loadWorkerStatus();
        
        // Auto refresh toggle
        $('#btn-auto-refresh').on('click', function() {
            toggleAutoRefresh();
        });
        
        // Manual refresh
        $('#btn-refresh-data').on('click', function() {
            loadBatchData();
            loadStats();
            loadWorkerStatus();
        });
        
        // Worker status check
        $('#btn-queue-worker-status').on('click', function() {
            checkWorkerStatus();
        });
    });

    function toggleAutoRefresh() {
        if (isAutoRefresh) {
            // Turn off auto refresh
            clearInterval(autoRefreshInterval);
            isAutoRefresh = false;
            $('#btn-auto-refresh').html('<i class="ki-duotone ki-arrows-circle fs-2"><span class="path1"></span><span class="path2"></span></i>Auto Refresh: OFF');
            $('#btn-auto-refresh').removeClass('btn-light-success').addClass('btn-light-primary');
        } else {
            // Turn on auto refresh
            autoRefreshInterval = setInterval(function() {
                loadBatchData();
                loadStats();
                loadWorkerStatus();
            }, 30000); // Refresh every 30 seconds
            
            isAutoRefresh = true;
            $('#btn-auto-refresh').html('<i class="ki-duotone ki-arrows-circle fs-2"><span class="path1"></span><span class="path2"></span></i>Auto Refresh: ON');
            $('#btn-auto-refresh').removeClass('btn-light-primary').addClass('btn-light-success');
        }
    }

    function loadStats() {
        $.ajax({
            url: '{{ route("admin.sk-kontrak.queue-stats") }}',
            method: 'GET',
            success: function(response) {
                if (response.code === 200) {
                    $('#total-batches').text(response.stats.total_batches);
                    $('#processing-batches').text(response.stats.processing_batches);
                    $('#completed-batches').text(response.stats.completed_batches);
                    $('#failed-batches').text(response.stats.failed_batches);
                    
                    $('#pending-jobs-count').text(response.stats.pending_jobs);
                    $('#sk-tte-queue-count').text(response.stats.sk_tte_queue);
                    $('#default-queue-count').text(response.stats.default_queue);
                    $('#failed-jobs-count').text(response.stats.failed_jobs);
                }
            },
            error: function(xhr) {
                console.error('Error loading stats:', xhr);
            }
        });
    }

    function loadWorkerStatus() {
        $.ajax({
            url: '{{ route("admin.sk-kontrak.worker-status") }}',
            method: 'GET',
            success: function(response) {
                if (response.code === 200) {
                    const status = response.worker_running ? 'Running' : 'Not Running';
                    const badgeClass = response.worker_running ? 'badge-light-success' : 'badge-light-danger';
                    
                    $('#worker-status').removeClass().addClass('badge ' + badgeClass).text(status);
                    $('#last-processed').text(response.last_processed || '-');
                    $('#queue-size').text(response.queue_size + ' jobs');
                }
            },
            error: function(xhr) {
                $('#worker-status').removeClass().addClass('badge badge-light-danger').text('Error');
                console.error('Error checking worker status:', xhr);
            }
        });
    }

    function loadBatchData() {
        $.ajax({
            url: '{{ route("admin.sk-kontrak.batch-list") }}',
            method: 'GET',
            beforeSend: function() {
                $('#batch-monitor-tbody').html('<tr><td colspan="8" class="text-center">Loading...</td></tr>');
            },
            success: function(response) {
                if (response.code === 200) {
                    let tbody = '';
                    
                    if (response.batches.length === 0) {
                        tbody = '<tr><td colspan="8" class="text-center text-muted">Belum ada batch processing</td></tr>';
                    } else {
                        response.batches.forEach(function(batch) {
                            const progress = Math.round((batch.processed_count / batch.total_karyawan) * 100);
                            const statusConfig = {
                                'pending': { class: 'badge-secondary', text: 'Pending' },
                                'processing': { class: 'badge-primary', text: 'Processing' },
                                'completed': { class: 'badge-success', text: 'Completed' },
                                'failed': { class: 'badge-danger', text: 'Failed' }
                            };
                            
                            const config = statusConfig[batch.status] || statusConfig['pending'];
                            const createdAt = new Date(batch.created_at).toLocaleString('id-ID');
                            const estimatedCompletion = batch.estimated_completion ? new Date(batch.estimated_completion).toLocaleString('id-ID') : '-';
                            
                            tbody += `
                                <tr>
                                    <td>${batch.id}</td>
                                    <td>${batch.urut}/${batch.tahun_sk}</td>
                                    <td>${batch.total_karyawan}</td>
                                    <td>
                                        <div class="d-flex flex-column">
                                            <div class="d-flex justify-content-between">
                                                <span class="text-muted fs-7">${batch.processed_count}/${batch.total_karyawan}</span>
                                                <span class="text-muted fs-7">${progress}%</span>
                                            </div>
                                            <div class="progress h-6px">
                                                <div class="progress-bar bg-primary" style="width: ${progress}%"></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td><span class="badge ${config.class}">${config.text}</span></td>
                                    <td>${createdAt}</td>
                                    <td>${estimatedCompletion}</td>
                                    <td class="text-end">
                                        <button class="btn btn-light btn-sm" onclick="showBatchDetail(${batch.id})">
                                            <i class="ki-duotone ki-eye fs-6"></i>
                                            Detail
                                        </button>
                                        ${batch.failed_count > 0 ? `
                                            <button class="btn btn-warning btn-sm ms-2" onclick="retryFailed(${batch.id})">
                                                <i class="ki-duotone ki-arrows-circle fs-6"></i>
                                                Retry
                                            </button>
                                        ` : ''}
                                    </td>
                                </tr>
                            `;
                        });
                    }
                    
                    $('#batch-monitor-tbody').html(tbody);
                }
            },
            error: function(xhr) {
                $('#batch-monitor-tbody').html('<tr><td colspan="8" class="text-center text-danger">Error loading data</td></tr>');
                console.error('Error loading batch data:', xhr);
            }
        });
    }

    function showBatchDetail(batchId) {
        // Use existing modal function
        showBatchProgressModal(batchId, 0, '');
    }

    function retryFailed(batchId) {
        // Use existing retry function from modal
        retryFailedBatch(batchId);
    }

    function checkWorkerStatus() {
        Swal.fire({
            title: 'Queue Worker Commands',
            html: `
                <div class="text-start">
                    <p><strong>Untuk menjalankan worker:</strong></p>
                    <code class="d-block bg-light p-3 mb-3">php artisan queue:work database_mysql --queue=sk_tte --timeout=300 --tries=3</code>
                    
                    <p><strong>Untuk background (production):</strong></p>
                    <code class="d-block bg-light p-3 mb-3">nohup php artisan queue:work database_mysql --queue=sk_tte --timeout=300 --tries=3 > /dev/null 2>&1 &</code>
                    
                    <p><strong>Status worker saat ini:</strong></p>
                    <div id="current-worker-status" class="alert alert-info">Checking...</div>
                </div>
            `,
            width: 800,
            showCloseButton: true,
            showConfirmButton: false,
            willOpen: () => {
                // Check current status
                $.ajax({
                    url: '{{ route("admin.sk-kontrak.worker-status") }}',
                    method: 'GET',
                    success: function(response) {
                        let statusHtml = '';
                        if (response.code === 200) {
                            if (response.worker_running) {
                                statusHtml = '<div class="alert alert-success">✅ Worker sedang berjalan</div>';
                            } else {
                                statusHtml = '<div class="alert alert-warning">⚠️ Worker tidak berjalan</div>';
                            }
                            statusHtml += `<p class="text-muted">Queue size: ${response.queue_size} jobs</p>`;
                        } else {
                            statusHtml = '<div class="alert alert-danger">❌ Error checking worker status</div>';
                        }
                        $('#current-worker-status').html(statusHtml);
                    },
                    error: function() {
                        $('#current-worker-status').html('<div class="alert alert-danger">❌ Error checking worker status</div>');
                    }
                });
            }
        });
    }

    // Cleanup on page unload
    $(window).on('beforeunload', function() {
        if (autoRefreshInterval) {
            clearInterval(autoRefreshInterval);
        }
    });
</script>
@endpush
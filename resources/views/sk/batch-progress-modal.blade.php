<!--begin::Modal - Batch Progress-->
<div class="modal fade" id="kt_modal_batch_progress" data-bs-backdrop="static" data-keyboard="false" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable mw-900px">
        <div class="modal-content">
            <div class="modal-header" id="kt_modal_batch_progress_header">
                <h2 class="fw-bold">Progress TTE SK Batch</h2>
                <div class="btn btn-icon btn-sm btn-active-icon-primary" data-kt-menu-modal-batch-progress="close" data-bs-dismiss="modal">
                    <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                </div>
            </div>
            <div class="modal-body px-5 py-5">
                <!-- Batch Info -->
                <div class="batch-info mb-5">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="d-flex align-items-center mb-3">
                                <span class="fw-bold me-3">Batch ID:</span>
                                <span id="batch-id-display">-</span>
                            </div>
                            <div class="d-flex align-items-center mb-3">
                                <span class="fw-bold me-3">Total Karyawan:</span>
                                <span id="total-karyawan-display">-</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-center mb-3">
                                <span class="fw-bold me-3">Status:</span>
                                <span class="badge" id="batch-status-badge">-</span>
                            </div>
                            <div class="d-flex align-items-center mb-3">
                                <span class="fw-bold me-3">Estimasi Selesai:</span>
                                <span id="eta-display">-</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Progress Bar -->
                <div class="progress-section mb-5">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="fw-bold">Progress:</span>
                        <span id="progress-percentage">0%</span>
                    </div>
                    <div class="progress" style="height: 20px;">
                        <div class="progress-bar progress-bar-striped progress-bar-animated" 
                             role="progressbar" 
                             style="width: 0%" 
                             id="progress-bar">
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-4 text-center">
                            <div class="text-success fw-bold fs-3" id="success-count">0</div>
                            <div class="text-muted">Berhasil</div>
                        </div>
                        <div class="col-md-4 text-center">
                            <div class="text-warning fw-bold fs-3" id="processing-count">0</div>
                            <div class="text-muted">Sedang Proses</div>
                        </div>
                        <div class="col-md-4 text-center">
                            <div class="text-danger fw-bold fs-3" id="failed-count">0</div>
                            <div class="text-muted">Gagal</div>
                        </div>
                    </div>
                </div>

                <!-- Current Processing -->
                <div class="current-processing mb-5" id="current-processing-section" style="display: none;">
                    <div class="alert alert-primary d-flex align-items-center">
                        <span class="spinner-border spinner-border-sm me-3" role="status" aria-hidden="true"></span>
                        <span>
                            Sedang memproses: <strong id="current-karyawan-name">-</strong>
                        </span>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="action-buttons text-center">
                    <button type="button" class="btn btn-secondary me-3" data-bs-dismiss="modal">
                        Tutup (Proses tetap berjalan)
                    </button>
                    <button type="button" class="btn btn-info me-3" id="btn-view-detail">
                        <i class="ki-duotone ki-eye fs-2">
                            <span class="path1"></span>
                            <span class="path2"></span>
                            <span class="path3"></span>
                        </i>
                        Lihat Detail
                    </button>
                    <button type="button" class="btn btn-warning" id="btn-retry-failed" style="display: none;">
                        <i class="ki-duotone ki-arrows-circle fs-2">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                        Retry Gagal
                    </button>
                </div>

                <!-- Completion Message -->
                <div class="completion-message mt-5" id="completion-message" style="display: none;">
                    <div class="alert alert-success">
                        <div class="d-flex align-items-center">
                            <i class="ki-duotone ki-check-circle fs-1 text-success me-3">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            <div>
                                <div class="fw-bold">Proses TTE Batch Selesai!</div>
                                <div class="text-muted" id="completion-summary">-</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!--end::Modal - Batch Progress-->

<!--begin::Modal - Batch Detail-->
<div class="modal fade" id="kt_modal_batch_detail" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable mw-1000px">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="fw-bold">Detail Progress Batch</h2>
                <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                    <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                </div>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4" id="batch-detail-table">
                        <thead>
                            <tr class="fw-bold text-muted">
                                <th class="min-w-50px">No</th>
                                <th class="min-w-100px">Kd. Karyawan</th>
                                <th class="min-w-200px">Nama Karyawan</th>
                                <th class="min-w-100px text-center">Status</th>
                                <th class="min-w-150px">Waktu Proses</th>
                                <th class="min-w-200px">Keterangan</th>
                            </tr>
                        </thead>
                        <tbody id="batch-detail-tbody">
                            <!-- Dynamic content will be loaded here -->
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
<!--end::Modal - Batch Detail-->

@push('scripts')
<script>
    let currentBatchId = null;
    let pollingInterval = null;

    function showBatchProgressModal(batchId, totalKaryawan, estimatedCompletion) {
        currentBatchId = batchId;
        
        // Set initial values
        $('#batch-id-display').text(batchId);
        $('#total-karyawan-display').text(totalKaryawan);
        $('#eta-display').text(estimatedCompletion);
        
        // Reset progress
        resetProgressDisplay();
        
        // Show modal
        $('#kt_modal_batch_progress').modal('show');
        
        // Start polling
        startPolling();
    }

    function resetProgressDisplay() {
        $('#progress-bar').css('width', '0%');
        $('#progress-percentage').text('0%');
        $('#success-count').text('0');
        $('#processing-count').text('0');
        $('#failed-count').text('0');
        $('#batch-status-badge').removeClass().addClass('badge badge-secondary').text('Pending');
        $('#current-processing-section').hide();
        $('#completion-message').hide();
        $('#btn-retry-failed').hide();
    }

    function startPolling() {
        if (pollingInterval) {
            clearInterval(pollingInterval);
        }
        
        // Poll every 10 seconds
        pollingInterval = setInterval(function() {
            if (currentBatchId) {
                checkBatchProgress(currentBatchId);
            }
        }, 10000);
        
        // Initial check
        if (currentBatchId) {
            checkBatchProgress(currentBatchId);
        }
    }

    function stopPolling() {
        if (pollingInterval) {
            clearInterval(pollingInterval);
            pollingInterval = null;
        }
    }

    function checkBatchProgress(batchId) {
        $.ajax({
            url: '{{ route("admin.sk-kontrak.batch-status", ":batchId") }}'.replace(':batchId', batchId),
            method: 'GET',
            success: function(response) {
                if (response.code === 200) {
                    updateProgressDisplay(response);
                    
                    // Stop polling if completed or failed
                    if (response.status === 'completed' || response.status === 'failed') {
                        stopPolling();
                        showCompletionMessage(response);
                    }
                }
            },
            error: function(xhr) {
                console.error('Error checking batch progress:', xhr);
                if (xhr.status === 404) {
                    stopPolling();
                    toastr.error('Batch tidak ditemukan', 'Error');
                }
            }
        });
    }

    function updateProgressDisplay(data) {
        // Update progress bar
        $('#progress-bar').css('width', data.percentage + '%');
        $('#progress-percentage').text(data.percentage + '%');
        
        // Update counts
        $('#success-count').text(data.success || 0);
        $('#processing-count').text((data.processed - data.success - data.failed) || 0);
        $('#failed-count').text(data.failed || 0);
        
        // Update status badge
        const statusConfig = {
            'pending': { class: 'badge-secondary', text: 'Pending' },
            'processing': { class: 'badge-primary', text: 'Processing' },
            'completed': { class: 'badge-success', text: 'Completed' },
            'failed': { class: 'badge-danger', text: 'Failed' }
        };
        
        const config = statusConfig[data.status] || statusConfig['pending'];
        $('#batch-status-badge').removeClass().addClass('badge ' + config.class).text(config.text);
        
        // Update current processing
        if (data.current_processing && data.status === 'processing') {
            $('#current-karyawan-name').text(data.current_processing);
            $('#current-processing-section').show();
        } else {
            $('#current-processing-section').hide();
        }
        
        // Update ETA
        if (data.estimated_completion) {
            $('#eta-display').text(data.estimated_completion);
        }
        
        // Show retry button if there are failed items
        if (data.failed > 0 && (data.status === 'completed' || data.status === 'failed')) {
            $('#btn-retry-failed').show();
        }
    }

    function showCompletionMessage(data) {
        let message = `Selesai memproses ${data.total} karyawan. `;
        message += `Berhasil: ${data.success}, Gagal: ${data.failed}`;
        
        $('#completion-summary').text(message);
        $('#completion-message').show();
        
        if (data.failed > 0) {
            $('#btn-retry-failed').show();
        }
    }

    // Event handlers
    $('#btn-view-detail').on('click', function() {
        if (currentBatchId) {
            showBatchDetail(currentBatchId);
        }
    });

    $('#btn-retry-failed').on('click', function() {
        if (currentBatchId) {
            retryFailedBatch(currentBatchId);
        }
    });

    function showBatchDetail(batchId) {
        $.ajax({
            url: '{{ route("admin.sk-kontrak.batch-detail", ":batchId") }}'.replace(':batchId', batchId),
            method: 'GET',
            beforeSend: function() {
                $('#batch-detail-tbody').html('<tr><td colspan="6" class="text-center">Loading...</td></tr>');
            },
            success: function(response) {
                if (response.code === 200) {
                    let tbody = '';
                    response.progress_details.forEach(function(item, index) {
                        const statusConfig = {
                            'pending': { class: 'badge-secondary', text: 'Pending' },
                            'processing': { class: 'badge-primary', text: 'Processing' },
                            'success': { class: 'badge-success', text: 'Success' },
                            'failed': { class: 'badge-danger', text: 'Failed' }
                        };
                        
                        const config = statusConfig[item.status] || statusConfig['pending'];
                        const processedAt = item.processed_at ? new Date(item.processed_at).toLocaleString('id-ID') : '-';
                        const errorMsg = item.error_message || '-';
                        
                        tbody += `
                            <tr>
                                <td>${index + 1}</td>
                                <td>${item.kd_karyawan}</td>
                                <td>${item.karyawan_name}</td>
                                <td class="text-center">
                                    <span class="badge ${config.class}">${config.text}</span>
                                </td>
                                <td>${processedAt}</td>
                                <td>${errorMsg}</td>
                            </tr>
                        `;
                    });
                    
                    $('#batch-detail-tbody').html(tbody);
                    $('#kt_modal_batch_detail').modal('show');
                }
            },
            error: function(xhr) {
                toastr.error('Error loading batch detail', 'Error');
                $('#batch-detail-tbody').html('<tr><td colspan="6" class="text-center text-danger">Error loading data</td></tr>');
            }
        });
    }

    function retryFailedBatch(batchId) {
        Swal.fire({
            title: 'Retry Failed Items?',
            text: 'Apakah Anda yakin ingin mengulang proses TTE untuk item yang gagal?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, Retry!',
            cancelButtonText: 'Batal',
            customClass: {
                confirmButton: 'btn btn-primary',
                cancelButton: 'btn btn-secondary'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '{{ route("admin.sk-kontrak.retry-failed-batch", ":batchId") }}'.replace(':batchId', batchId),
                    method: 'POST',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.code === 200) {
                            toastr.success(response.message, 'Success');
                            // Restart polling
                            startPolling();
                            // Hide retry button and reset display
                            $('#btn-retry-failed').hide();
                            $('#completion-message').hide();
                        }
                    },
                    error: function(xhr) {
                        toastr.error('Error saat retry batch', 'Error');
                    }
                });
            }
        });
    }

    // Stop polling when modal is closed
    $('#kt_modal_batch_progress').on('hidden.bs.modal', function() {
        stopPolling();
        currentBatchId = null;
    });

    // Cleanup on page unload
    $(window).on('beforeunload', function() {
        stopPolling();
    });
</script>
@endpush
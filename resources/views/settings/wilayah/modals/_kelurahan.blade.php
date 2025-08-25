<!-- Modal Kelurahan -->
<div class="modal fade" id="kelurahanModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog mw-550px">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="fw-bold modal-title" id="modalKelurahanTitle">Tambah Kelurahan</h2>
                <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal" aria-label="Close">
                    <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                </div>
            </div>
            <form method="POST" class="form" id="kelurahanForm">
                @csrf
                <input type="hidden" name="_method" id="formKelurahanMethod" value="POST">
                <input type="hidden" name="kd_kecamatan" id="kelurahan_kecamatan_id">
                <input type="hidden" name="kd_kelurahan" id="kd_kelurahan_hidden">
                <div class="modal-body">
                    <!-- Nama Kelurahan -->
                    <div class="d-flex flex-column mb-3">
                        <label class="fw-semibold fs-6 mb-2 required">Nama Kelurahan</label>
                        <input type="text" class="form-control form-control-solid" name="kelurahan" id="kelurahan" placeholder="Nama Kelurahan" autofocus maxlength="80" />
                        <div class="fv-plugins-message-container invalid-feedback error-text kelurahan_error"></div>
                    </div>
                    
                    <!-- Kode Pos -->
                    <div class="d-flex flex-column mb-3">
                        <label class="fw-semibold fs-6 mb-2">Kode Pos</label>
                        <input type="text" class="form-control form-control-solid" name="kode_pos" id="kode_pos" placeholder="Kode Pos" maxlength="5" />
                        <div class="fv-plugins-message-container invalid-feedback error-text kode_pos_error"></div>
                    </div>
                    
                    <!-- Status Aktif -->
                    <div class="d-flex flex-column mb-3">
                        <label class="fw-semibold fs-6 mb-2 required">Status</label>
                        <select class="form-select form-select-solid" name="aktif" id="aktif">
                            <option value="1">Aktif</option>
                            <option value="0">Tidak Aktif</option>
                        </select>
                        <div class="fv-plugins-message-container invalid-feedback error-text aktif_error"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary submit-btn" data-kt-menu-modal-action="submit">
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

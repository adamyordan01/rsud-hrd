<!-- Modal: Tambah/Edit Provinsi -->
<div class="modal fade" id="provinsiModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog mw-550px">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="fw-bold modal-title" id="modalProvinsiTitle">Tambah Provinsi</h2>
                <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal" aria-label="Close">
                    <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                </div>
            </div>
            <form method="POST" class="form" id="provinsiForm">
                @csrf
                <input type="hidden" name="_method" id="formProvinsiMethod" value="POST">
                <input type="hidden" name="kd_propinsi" id="kd_provinsi_hidden">
                <div class="modal-body">
                    <!-- Nama Provinsi -->
                    <div class="d-flex flex-column mb-3">
                        <label class="fw-semibold fs-6 mb-2 required">Nama Provinsi</label>
                        <input type="text" class="form-control form-control-solid" name="propinsi" id="propinsi" placeholder="Nama Provinsi" autofocus maxlength="30" />
                        <div class="fv-plugins-message-container invalid-feedback error-text propinsi_error"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary submit-btn" data-kt-menu-modal-action="submit">
                        <span class="indicator-label">Simpan</span>
                        <span class="indicator-progress" style="display: none;">
                            Menyimpan... <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal: Tambah/Edit Kecamatan -->
<div class="modal fade" id="kecamatanModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog mw-550px">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="fw-bold modal-title" id="modalKecamatanTitle">Tambah Kecamatan</h2>
                <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal" aria-label="Close">
                    <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                </div>
            </div>
            <form method="POST" class="form" id="kecamatanForm">
                @csrf
                <input type="hidden" name="_method" id="formKecamatanMethod" value="POST">
                <input type="hidden" name="kd_kabupaten" id="kecamatan_kabupaten_id">
                <input type="hidden" name="kd_kecamatan" id="kd_kecamatan_hidden">
                <div class="modal-body">
                    <!-- Nama Kecamatan -->
                    <div class="d-flex flex-column mb-3">
                        <label class="fw-semibold fs-6 mb-2 required">Nama Kecamatan</label>
                        <input type="text" class="form-control form-control-solid" name="kecamatan" id="kecamatan" placeholder="Nama Kecamatan" autofocus maxlength="30" />
                        <div class="fv-plugins-message-container invalid-feedback error-text kecamatan_error"></div>
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

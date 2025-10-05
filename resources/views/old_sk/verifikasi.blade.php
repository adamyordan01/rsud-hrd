<form action="#">
    @csrf
    <input type="hidden" name="urut_rincian_verif_4" value="" id="urut_rincian_verif_4">
    <input type="hidden" name="tahun_rincian_verif_4" value="" id="tahun_rincian_verif_4">
    <div class="d-flex flex-column fv-row mb-5">
        <label
            class="required fw-semibold fs-6 mb-2 d-flex align-items-center"
        >
            Tanggal Tanda Tangan SK
        </label>
        <input
            class="form-control form-control-solid"
            name="tgl_ttd_sk"
            id="tgl_ttd_sk"
            placeholder="Pilih Tanggal Tanda Tangan SK"
        />
        <div class="fv-plugins-message-container invalid-feedback error-text tujuan_error"></div>
    </div>
</form>

@push('scripts')
    <script>
        $(document).ready(function() {
            $('#tgl_ttd_sk').daterangepicker({
                format: 'dd-mm-yyyy',
                todayHighlight: true,
                autoclose: true
            });
        });
    </script>
@endpush
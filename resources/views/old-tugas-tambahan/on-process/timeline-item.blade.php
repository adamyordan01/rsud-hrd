<div class="timeline timeline-border-dashed">
    @forelse ($verifData as $verif)
        @if ($verif['kd_karyawan_verif'])
            <!--begin::Timeline item-->
            <div class="timeline-item pb-5">
                <!--begin::Timeline line-->
                <div class="timeline-line"></div>
                <!--end::Timeline line-->

                <!--begin::Timeline icon-->
                <div class="timeline-icon">
                    <i class="ki-duotone ki-cd fs-2 text-success"><span class="path1"></span><span class="path2"></span></i>                                   
                </div>
                <!--end::Timeline icon-->

                <!--begin::Timeline content-->
                <div class="timeline-content m-0">
                    <!--begin::Label-->
                    <span class="fs-8 fw-bolder text-success text-uppercase">Verifikasi {{ $verif['verif'] }}</span>
                    <!--begin::Label-->                                        

                    <!--begin::Title-->
                    <a href="#" class="fs-6 text-gray-800 fw-bold d-block text-hover-primary">{{ $verif['nama_verif'] }}</a>
                    <!--end::Title-->   
                    
                    <!--begin::Title-->
                    <span class="fw-semibold text-gray-500">Waktu: {{ $verif['waktu_verif'] }}</span>
                    <!--end::Title-->    
                </div>
                <!--end::Timeline content-->                                  
            </div>
        @endif
    @empty
        <div class="text-center">
            <span class="badge badge-light-primary">Belum ada verifikasi</span>
        </div>
    @endforelse
</div>

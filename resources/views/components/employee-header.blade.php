<div class="card mb-5 mb-xl-10">
    <div class="card-body pt-9 pb-0">
        <!-- Alert untuk data yang belum terisi -->
        @if (!empty($missingFields))
            <div class="notice d-flex bg-light-warning rounded border-warning border border-dashed p-6 mb-5">
                <i class="ki-outline ki-information fs-2tx text-warning me-4 self-start"></i>
                <div class="d-flex flex-column flex-grow-1">
                    <div class="fw-semibold">
                        <h4 class="text-gray-900 fw-bold">Perhatian!</h4>
                        <div class="fs-6 text-gray-700">
                            Harap lengkapi data berikut: {{ implode(', ', $missingFields) }}.
                            Silakan <a class="fw-bold" href="#">perbarui profil Anda</a>.
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <div class="d-flex flex-wrap flex-sm-nowrap">
            <div class="me-7 mb-4">
                <div class="symbol symbol-100px symbol-lg-160px symbol-fixed position-relative">
                    {{-- <img src="https://e-rsud.langsakota.go.id/hrd/user/images/profil/{{ $karyawan->foto }}" alt="{{ $karyawan->kd_karyawan }}" /> --}}
                    <img src="{{ $photoUrl }}" alt="{{ $karyawan->kd_karyawan }}" />
                    <div
                        class="position-absolute translate-middle bottom-0 start-100 mb-6 bg-success rounded-circle border border-4 border-body h-20px w-20px">
                    </div>
                </div>
            </div>

            <div class="flex-grow-1">
                <div class="d-flex justify-content-between align-items-start flex-wrap mb-2">
                    <div class="d-flex flex-column">
                        <div class="d-flex align-items-center mb-2">
                            <a href="#" class="text-gray-900 text-hover-primary fs-2 fw-bold me-1">
                                {{ $namaLengkap }}
                            </a>
                        </div>

                        <div class="d-flex align-items-center fw-semibold mb-2">
                            <a href="#" class="d-flex align-items-center text-gray-500 text-hover-primary">
                                <i class="ki-duotone ki-dots-circle-vertical fs-4 me-1"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span></i>
                                    {{ $karyawan->kd_karyawan }}
                            </a>
                        </div>

                        <div class="d-flex align-items-center fw-semibold mb-2">
                            <a href="#"
                                class="d-flex align-items-center text-gray-500 text-hover-primary">
                                <i class="ki-duotone ki-profile-circle fs-4 me-1"><span class="path1"></span><span
                                        class="path2"></span><span class="path3"></span></i> {{ $karyawan->status_kerja }}
                            </a>
                        </div>

                        <div class="d-flex align-items-center fw-semibold mb-2">
                            <div
                                class="d-flex align-items-center text-gray-500">
                                <i class="ki-duotone ki-geolocation fs-4 me-1"><span class="path1"></span><span
                                        class="path2"></span></i>
                                {{ $alamat }}
                            </div>
                        </div>

                        <div class="d-flex align-items-center fw-semibold mb-2">
                            <a href="#" class="d-flex align-items-center text-gray-500 text-hover-primary">
                                <i class="ki-duotone ki-sms fs-4 me-1"><span class="path1"></span><span
                                        class="path2"></span></i> 
                                    {{ $karyawan->email ?? 'Belum diisi' }}
                            </a>
                        </div>
                    </div>
                </div>

                <div class="d-flex flex-wrap flex-stack">
                    <div class="d-flex align-items-center w-200px w-sm-300px flex-column mt-3">
                        <div class="d-flex justify-content-between w-100 mt-auto mb-2">
                            <span class="fw-semibold fs-6 text-gray-500">Kelengkapan Data</span>
                            <span class="fw-bold fs-6">{{ $persentaseKelengkapan }}%</span>
                        </div>

                        <div class="h-5px mx-3 w-100 bg-light mb-3">
                            <div class="bg-success rounded h-5px" role="progressbar" style="width: {{ $persentaseKelengkapan }}%;"
                                aria-valuenow="{{ $persentaseKelengkapan }}" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                </div>

                <!-- button cetak id card, upload foto, dan cetak kartu identitas rata kanan -->
                <div class="d-flex flex-wrap justify-content-end mt-5">
                    <div class="d-flex align-items-center flex-column mt-3">
                        <a href="#" class="btn btn-sm btn-primary" id="printIdCardButton" data-id="{{ $karyawan->kd_karyawan }}">
                            <i class="ki-duotone ki-printer fs-2">
                                <span class="path1"></span>
                                <span class="path2"></span>
                                <span class="path3"></span>
                                <span class="path4"></span>
                                <span class="path5"></span>
                            </i>
                            Cetak ID Card
                        </a>
                    </div>

                    <div class="d-flex align-items-center flex-column ms-2 mt-3">
                        <a href="#" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#uploadPhotoModal" data-id="{{ $karyawan->kd_karyawan }}">Upload Foto</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="hover-scroll-x">
            <ul class="nav flex-nowrap nav-stretch nav-line-tabs  border-transparent fs-5 fw-bold">
                <li class="nav-item mt-2">
                    <a class="nav-link text-active-primary ms-0 me-10 py-5 @if (request()->routeIs('admin.karyawan.show')) active @endif"
                        href="{{ route('admin.karyawan.show', $karyawan->kd_karyawan) }}"
                    >
                        Data Pribadi
                    </a>
                </li>
                <li class="nav-item mt-2">
                    <a 
                        class="nav-link text-active-primary ms-0 me-10 py-5 @if (request()->routeIs('admin.karyawan.bpjs-ketenagakerjaan.index')) active @endif" 
                        href="{{ route('admin.karyawan.bpjs-ketenagakerjaan.index', $karyawan->kd_karyawan) }}"
                    >
                        BPJS Ketenagakerjaan
                    </a>
                </li>
                <li class="nav-item mt-2">
                    <a 
                        class="nav-link text-active-primary ms-0 me-10 py-5 @if (request()->routeIs('admin.karyawan.keluarga.index')) active @endif" 
                        href="{{ route('admin.karyawan.keluarga.index', $karyawan->kd_karyawan) }}"
                    >
                        Keluarga
                    </a>
                    {{-- <a class="nav-link text-active-primary ms-0 me-10 py-5"
                        href="/metronic8/demo1/account/settings.html">
                        Keluarga
                    </a> --}}
                </li>
                <li class="nav-item mt-2">
                    <a 
                        class="nav-link text-active-primary ms-0 me-10 py-5 @if (request()->routeIs('admin.karyawan.kemampuan-bahasa.index')) active @endif" 
                        href="{{ route('admin.karyawan.kemampuan-bahasa.index', $karyawan->kd_karyawan) }}"
                    >
                        Bahasa
                    </a>
                </li>
                <li class="nav-item mt-2">
                    <a 
                        class="nav-link text-active-primary ms-0 me-10 py-5 @if (request()->routeIs('admin.karyawan.pendidikan.index')) active @endif" 
                        href="{{ route('admin.karyawan.pendidikan.index', $karyawan->kd_karyawan) }}"
                    >
                        Pendidikan
                    </a>
                </li>
                <li class="nav-item mt-2">
                    <a 
                        class="nav-link text-active-primary ms-0 me-10 py-5 @if (request()->routeIs('admin.karyawan.riwayat-kerja.index')) active @endif" 
                        href="{{ route('admin.karyawan.riwayat-kerja.index', $karyawan->kd_karyawan) }}"
                    >
                        Riwayat Pekerjaan
                    </a>
                </li>
                <li class="nav-item mt-2">
                    <a 
                        class="nav-link text-active-primary ms-0 me-10 py-5 @if (request()->routeIs('admin.karyawan.riwayat-organisasi.index')) active @endif" 
                        href="{{ route('admin.karyawan.riwayat-organisasi.index', $karyawan->kd_karyawan) }}"
                    >
                        Riwayat Organisasi 
                    </a>
                </li>
                <li class="nav-item mt-2">
                    <a 
                        class="nav-link text-active-primary ms-0 me-10 py-5 @if (request()->routeIs('admin.karyawan.penghargaan.index')) active @endif" 
                        href="{{ route('admin.karyawan.penghargaan.index', $karyawan->kd_karyawan) }}"
                    >
                        Penghargaan 
                    </a>
                </li>
                <li class="nav-item mt-2">
                    <a 
                        class="nav-link text-active-primary ms-0 me-10 py-5 @if (request()->routeIs('admin.karyawan.seminar.index')) active @endif" 
                        href="{{ route('admin.karyawan.seminar.index', $karyawan->kd_karyawan) }}"
                    >
                        Seminar 
                    </a>
                </li>
                <li class="nav-item mt-2">
                    <a 
                        class="nav-link text-active-primary ms-0 me-10 py-5 @if (request()->routeIs('admin.karyawan.tugas.index')) active @endif" 
                        href="{{ route('admin.karyawan.tugas.index', $karyawan->kd_karyawan) }}"
                    >
                        Tugas 
                    </a>
                </li>

                <li class="nav-item mt-2">
                    <a 
                        class="nav-link text-active-primary ms-0 me-10 py-5 @if (request()->routeIs('admin.karyawan.str.index')) active @endif" 
                        href="{{ route('admin.karyawan.str.index', $karyawan->kd_karyawan) }}"
                    >
                        STR 
                    </a>
                </li>

                <li class="nav-item mt-2">
                    <a 
                        class="nav-link text-active-primary ms-0 me-10 py-5 @if (request()->routeIs('admin.karyawan.sip.index')) active @endif" 
                        href="{{ route('admin.karyawan.sip.index', $karyawan->kd_karyawan) }}"
                    >
                        SIP 
                    </a>
                </li>

                <li class="nav-item mt-2">
                    <a 
                        class="nav-link text-active-primary ms-0 me-10 py-5 @if (request()->routeIs('admin.karyawan.cuti.index')) active @endif" 
                        href="{{ route('admin.karyawan.cuti.index', $karyawan->kd_karyawan) }}"
                    >
                        Cuti 
                    </a>
                </li>

                {{ $slot }}
            </ul>
        </div>
    </div>
</div>
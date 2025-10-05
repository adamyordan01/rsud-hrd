{{-- Role Switcher Component --}}
@if(Auth::check() && Auth::user()->roles->count() > 1)
<div class="menu-item">
    <div class="menu-content">
        <div class="separator mx-1 my-4"></div>
    </div>
</div>

<div class="menu-item">
    <div class="menu-link px-3" data-bs-toggle="modal" data-bs-target="#roleSwitcherModal">
        <span class="menu-icon">
            <i class="ki-duotone ki-switch fs-2">
                <span class="path1"></span>
                <span class="path2"></span>
            </i>
        </span>
        <span class="menu-title">Ganti Role</span>
        <span class="menu-badge">
            <span class="badge badge-light-primary badge-sm">{{ session('active_role_display', 'Default') }}</span>
        </span>
    </div>
</div>

{{-- Role Switcher Modal --}}
<div class="modal fade" id="roleSwitcherModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header pb-0 border-0 justify-content-end">
                <div class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal">
                    <i class="ki-duotone ki-cross fs-1">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                </div>
            </div>
            <div class="modal-body scroll-y mx-5 mx-xl-18 pt-0 pb-15">
                <div class="text-center mb-13">
                    <h1 class="mb-3">Ganti Role</h1>
                    <div class="text-muted fw-semibold fs-5">
                        Pilih role untuk mengubah tampilan dashboard
                    </div>
                </div>

                <div class="mb-8">
                    <label class="fs-6 fw-semibold form-label mb-2">Role Saat Ini</label>
                    <div class="position-relative">
                        <div class="d-flex align-items-center border border-dashed border-gray-300 rounded px-4 py-3">
                            <div class="symbol symbol-35px me-3">
                                <span class="symbol-label bg-light-primary">
                                    <i class="fas fa-user-check text-primary fs-6"></i>
                                </span>
                            </div>
                            <div class="flex-grow-1">
                                <div class="fw-bold text-gray-800">{{ session('active_role_display', 'Default') }}</div>
                                <div class="text-muted fs-7">Role aktif saat ini</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mb-0">
                    <label class="fs-6 fw-semibold form-label mb-2">Pilih Role Baru</label>
                    <div class="role-options">
                        @foreach(Auth::user()->getHrdRoles() as $role)
                            @php
                                $roleDisplayName = match($role) {
                                    'pegawai_biasa' => 'Pegawai',
                                    'struktural' => 'Pejabat Struktural',
                                    'it_member' => 'Staff IT',
                                    'it_head' => 'Kepala IT',
                                    'kepegawaian' => 'Staff Kepegawaian',
                                    'superadmin' => 'Super Administrator',
                                    'pegawai_viewer' => 'Viewer Pegawai',
                                    default => ucwords(str_replace('_', ' ', $role))
                                };

                                $roleIcon = match($role) {
                                    'pegawai_biasa' => 'fas fa-user',
                                    'struktural' => 'fas fa-users-cog',
                                    'it_member' => 'fas fa-laptop-code',
                                    'it_head' => 'fas fa-crown',
                                    'kepegawaian' => 'fas fa-clipboard-list',
                                    'superadmin' => 'fas fa-shield-alt',
                                    'pegawai_viewer' => 'fas fa-eye',
                                    default => 'fas fa-user-tag'
                                };

                                $isCurrentRole = session('active_role') === 'hrd_' . $role;
                            @endphp

                            <div class="d-flex align-items-center border border-gray-300 rounded px-4 py-3 mb-3 role-option {{ $isCurrentRole ? 'bg-light-primary border-primary' : 'cursor-pointer' }}" 
                                 data-role="{{ $role }}" 
                                 data-display="{{ $roleDisplayName }}"
                                 style="{{ $isCurrentRole ? '' : 'cursor: pointer;' }}">
                                <div class="symbol symbol-35px me-3">
                                    <span class="symbol-label {{ $isCurrentRole ? 'bg-primary' : 'bg-light-gray-800' }}">
                                        <i class="{{ $roleIcon }} {{ $isCurrentRole ? 'text-white' : 'text-gray-800' }} fs-6"></i>
                                    </span>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="fw-bold {{ $isCurrentRole ? 'text-primary' : 'text-gray-800' }}">{{ $roleDisplayName }}</div>
                                    <div class="text-muted fs-7">
                                        @if($role === 'pegawai_biasa')
                                            Dashboard Pegawai
                                        @else
                                            Dashboard Admin
                                        @endif
                                    </div>
                                </div>
                                @if($isCurrentRole)
                                    <div class="badge badge-light-primary">Aktif</div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Handle role switching
    $('.role-option').not('.bg-light-primary').on('click', function() {
        const selectedRole = $(this).data('role');
        const displayName = $(this).data('display');
        
        // Add loading state
        $(this).find('.flex-grow-1').append('<div class="loading-text"><span class="spinner-border spinner-border-sm me-2"></span>Switching...</div>');
        
        $.ajax({
            url: '{{ route('role-selector.switch') }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                role: selectedRole
            },
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message);
                    $('#roleSwitcherModal').modal('hide');
                    
                    // Redirect after short delay
                    setTimeout(function() {
                        window.location.href = response.redirect;
                    }, 1000);
                } else {
                    toastr.error(response.message || 'Terjadi kesalahan');
                    $('.loading-text').remove();
                }
            },
            error: function(xhr) {
                let message = 'Terjadi kesalahan saat mengubah role';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                }
                toastr.error(message);
                $('.loading-text').remove();
            }
        });
    });

    // Reset modal when closed
    $('#roleSwitcherModal').on('hidden.bs.modal', function() {
        $('.loading-text').remove();
    });
});
</script>
@endif
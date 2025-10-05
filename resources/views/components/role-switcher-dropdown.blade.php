@if(Auth::check() && Auth::user()->roles->count() > 1)
<!--begin::Role switcher menu item-->
<div class="menu-item px-5" data-kt-menu-trigger="{default: 'click', lg: 'hover'}"
    data-kt-menu-placement="left-start" data-kt-menu-offset="-15px, 0">
    <a href="#" class="menu-link px-5">
        <span class="menu-title position-relative">
            {{ Session::get('active_role_display', 'Role') }}

            <span class="ms-5 position-absolute translate-middle-y top-50 end-0">
                <i class="ki-outline ki-user-tick fs-2"></i>
            </span>
        </span>
    </a>

    <!--begin::Role menu-->
    <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-title-gray-700 menu-icon-gray-500 menu-active-bg menu-state-color fw-semibold py-4 fs-base w-200px"
        data-kt-menu="true">
        @foreach(Auth::user()->getHrdRoles() as $role)
            @php
                $roleDisplayNames = [
                    'pegawai_biasa' => 'Pegawai',
                    'struktural' => 'Pejabat Struktural', 
                    'it_member' => 'Staff IT',
                    'it_head' => 'Kepala IT',
                    'kepegawaian' => 'Staff Kepegawaian',
                    'superadmin' => 'Super Administrator',
                    'pegawai_viewer' => 'Viewer Pegawai'
                ];
                
                $roleIcons = [
                    'pegawai_biasa' => 'ki-outline ki-user',
                    'struktural' => 'ki-outline ki-people',
                    'it_member' => 'ki-outline ki-code',
                    'it_head' => 'ki-outline ki-crown',
                    'kepegawaian' => 'ki-outline ki-document',
                    'superadmin' => 'ki-outline ki-shield-tick',
                    'pegawai_viewer' => 'ki-outline ki-eye'
                ];
                
                $displayName = $roleDisplayNames[$role] ?? ucwords(str_replace('_', ' ', $role));
                $icon = $roleIcons[$role] ?? 'ki-outline ki-user-tick';
                $isActive = Session::get('active_role') === 'hrd_'.$role;
            @endphp
            
            <!--begin::Role item-->
            <div class="menu-item px-3 my-0">
                <a href="#" class="menu-link px-3 py-2 role-switch-item" 
                   data-role="{{ $role }}"
                   @if($isActive) style="background-color: var(--bs-primary-bg-subtle);" @endif>
                    <span class="menu-icon">
                        <i class="{{ $icon }} fs-2"></i>
                    </span>
                    <span class="menu-title">
                        {{ $displayName }}
                    </span>
                    @if($isActive)
                        <span class="ms-auto">
                            <i class="ki-outline ki-check fs-2 text-success"></i>
                        </span>
                    @endif
                </a>
            </div>
            <!--end::Role item-->
        @endforeach
    </div>
    <!--end::Role menu-->
</div>
<!--end::Role switcher menu item-->

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle role switching
    document.querySelectorAll('.role-switch-item').forEach(function(item) {
        item.addEventListener('click', function(e) {
            e.preventDefault();
            
            const selectedRole = this.getAttribute('data-role');
            
            // Show loading state
            const originalText = this.querySelector('.menu-title').textContent;
            this.querySelector('.menu-title').innerHTML = '<i class="fas fa-spinner fa-spin"></i> Switching...';
            
            // Make AJAX request to switch role
            fetch('{{ route("role-selector.switch") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    role: selectedRole
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Show success message
                    if (typeof toastr !== 'undefined') {
                        toastr.success(data.message || 'Role berhasil diubah');
                    }
                    
                    // Redirect to appropriate dashboard
                    window.location.href = data.redirect;
                } else {
                    // Show error message
                    if (typeof toastr !== 'undefined') {
                        toastr.error(data.message || 'Gagal mengubah role');
                    }
                    
                    // Restore original text
                    this.querySelector('.menu-title').textContent = originalText;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                if (typeof toastr !== 'undefined') {
                    toastr.error('Terjadi kesalahan saat mengubah role');
                }
                
                // Restore original text
                this.querySelector('.menu-title').textContent = originalText;
            });
        });
    });
});
</script>
@endpush
@endif
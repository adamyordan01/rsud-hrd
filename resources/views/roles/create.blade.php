@extends('layouts.backend', ['title' => 'Add New Role'])

@php
    use Illuminate\Support\Str;
@endphp

@push('styles')
    <style>
        .permission-card {
            transition: all 0.3s ease;
        }
        .permission-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }
        .group-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        .permission-item {
            transition: background-color 0.2s ease;
        }
        .permission-item:hover {
            background-color: #f8f9fa;
        }
        .search-container {
            position: relative;
        }
        .search-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #adb5bd;
        }
        .search-input {
            padding-left: 45px;
        }
    </style>
@endpush

@section('toolbar')
    <div id="kt_app_toolbar" class="app-toolbar pt-6 pb-2">
        <div id="kt_app_toolbar_container" class="app-container container-fluid d-flex align-items-stretch">
            <div class="app-toolbar-wrapper d-flex flex-stack flex-wrap gap-4 w-100">
                <div class="page-title d-flex flex-column justify-content-center gap-1 me-3">
                    <h1 class="page-heading d-flex flex-column justify-content-center text-gray-900 fw-bold fs-3 m-0">
                        Tambah Role Baru
                    </h1>
                    <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0">
                        <li class="breadcrumb-item text-muted">
                            <a href="{{ route('admin.dashboard.index') }}" class="text-muted text-hover-primary">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item">
                            <span class="bullet bg-gray-500 w-5px h-2px"></span>
                        </li>
                        <li class="breadcrumb-item text-muted">
                            <a href="{{ route('admin.user-management.roles.index') }}" class="text-muted text-hover-primary">Role Management</a>
                        </li>
                        <li class="breadcrumb-item">
                            <span class="bullet bg-gray-500 w-5px h-2px"></span>
                        </li>
                        <li class="breadcrumb-item text-dark">Tambah Role</li>
                    </ul>
                </div>
                <div class="d-flex align-items-center gap-2 gap-lg-3">
                    <a href="{{ route('admin.user-management.roles.index') }}" class="btn btn-sm btn-light">
                        <i class="ki-duotone ki-arrow-left fs-5 me-1">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                        Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('content')
<div id="kt_app_content" class="app-content flex-column-fluid">
    <div id="kt_app_content_container" class="app-container container-fluid">
        <div class="row g-7">
            <div class="col-12">
                <form id="role-form" method="POST" action="{{ route('admin.user-management.roles.store') }}">
                    @csrf
                
                {{-- Basic Info Card --}}
                <div class="card mb-6">
                    <div class="card-header border-0 pt-6">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label fw-bolder fs-3 mb-1">Informasi Role</span>
                            <span class="text-muted mt-1 fw-bold fs-7">Isi informasi dasar untuk role baru</span>
                        </h3>
                    </div>
                    
                    <div class="card-body py-4">
                        <div class="row g-6">
                            <div class="col-lg-6">
                                <label class="form-label required">Nama Role</label>
                                <input type="text" 
                                       class="form-control @error('name') is-invalid @enderror" 
                                       name="name" 
                                       value="{{ old('name') }}" 
                                       placeholder="Masukkan nama role (contoh: admin, staff)"
                                       required>
                                <div class="form-text">Nama role akan otomatis ditambahi prefix "hrd_"</div>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-lg-6">
                                <label class="form-label">Level</label>
                                <input type="number" 
                                       class="form-control @error('level') is-invalid @enderror" 
                                       name="level" 
                                       value="{{ old('level') }}" 
                                       placeholder="Masukkan level role (opsional)">
                                <div class="form-text">Level untuk menentukan hierarki role</div>
                                @error('level')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-12">
                                <label class="form-label">Deskripsi</label>
                                <textarea class="form-control @error('description') is-invalid @enderror" 
                                          name="description" 
                                          rows="3" 
                                          placeholder="Masukkan deskripsi role (opsional)">{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Permissions Card --}}
                <div class="card">
                    <div class="card-header border-0 pt-6">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label fw-bolder fs-3 mb-1">Permissions</span>
                            <span class="text-muted mt-1 fw-bold fs-7">Pilih permissions yang akan diberikan kepada role ini</span>
                        </h3>
                        <div class="card-toolbar">
                            {{-- Progress Info --}}
                            <div class="d-flex align-items-center">
                                <span class="text-muted fs-7 me-3">Selected: <span id="selected-count" class="fw-bold text-primary">0</span></span>
                                <span class="text-muted fs-7">Total: <span id="total-count" class="fw-bold">{{ $permissions->count() }}</span></span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card-body py-4">
                        {{-- Search & Actions --}}
                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-7">
                            <div class="search-container d-flex align-items-center gap-3">
                                <div class="position-relative">
                                    <i class="ki-duotone ki-magnifier search-icon fs-5">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    <input type="text" 
                                           id="permission-search" 
                                           class="form-control form-control-sm search-input w-250px" 
                                           placeholder="Cari permissions...">
                                </div>
                            </div>
                            
                            <div class="d-flex align-items-center gap-2">
                                <button type="button" id="select-all" class="btn btn-sm btn-light-primary">
                                    <i class="ki-duotone ki-check-square fs-5 me-1">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    Pilih Semua
                                </button>
                                <button type="button" id="unselect-all" class="btn btn-sm btn-light-danger">
                                    <i class="ki-duotone ki-cross-square fs-5 me-1">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    Hapus Semua
                                </button>
                            </div>
                        </div>

                        {{-- Permission Groups --}}
                        <div class="row g-7">
                            @foreach($groupedPermissions as $category => $categoryPermissions)
                            <div class="col-lg-6 permission-group" data-category="{{ $category }}">
                                <div class="card permission-card border-dashed border-primary">
                                    <div class="card-header group-header border-0 py-4">
                                        <div class="d-flex align-items-center w-100">
                                            <div class="form-check form-check-custom form-check-solid me-3">
                                                <input class="form-check-input group-checkbox" 
                                                       type="checkbox" 
                                                       id="group-{{ $category }}"
                                                       data-category="{{ $category }}">
                                                <label class="form-check-label" for="group-{{ $category }}"></label>
                                            </div>
                                            <h4 class="card-title text-white fw-bold mb-0 flex-grow-1">
                                                {{ ucfirst(str_replace('_', ' ', $category)) }}
                                            </h4>
                                            <span class="badge badge-light badge-circle category-count" data-category="{{ $category }}">
                                                0/{{ count($categoryPermissions) }}
                                            </span>
                                        </div>
                                    </div>
                                    
                                    <div class="card-body py-5 bg-light">
                                        <div class="row g-4">
                                            @foreach($categoryPermissions as $permission)
                                            <div class="col-12 permission-item" data-permission-name="{{ $permission->name }}">
                                                <div class="form-check form-check-custom form-check-solid d-flex align-items-center permission-item rounded p-3">
                                                    <input class="form-check-input permission-checkbox" 
                                                           type="checkbox" 
                                                           name="permissions[]" 
                                                           value="{{ $permission->id }}"
                                                           id="permission-{{ $permission->id }}"
                                                           data-category="{{ $category }}"
                                                           @if(is_array(old('permissions')) && in_array($permission->id, old('permissions'))) checked @endif>
                                                    <label class="form-check-label flex-grow-1 ms-3 cursor-pointer" 
                                                           for="permission-{{ $permission->id }}">
                                                        <div class="fw-semibold text-gray-800">
                                                            {{ str_replace('hrd_', '', $permission->name) }}
                                                        </div>
                                                        @if($permission->description)
                                                        <div class="text-muted fs-7 mt-1">{{ $permission->description }}</div>
                                                        @endif
                                                    </label>
                                                </div>
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>

                        {{-- No Results Message --}}
                        <div id="no-results" class="text-center py-10" style="display: none;">
                            <div class="text-muted fs-4">
                                <i class="ki-duotone ki-file-deleted fs-3x mb-3">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                                <div>Tidak ada permissions yang ditemukan</div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="d-flex justify-content-end gap-3 mt-7">
                    <button type="submit" class="btn btn-primary">
                        <i class="ki-duotone ki-check fs-5 me-1">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                        Simpan Role
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    const permissionCheckboxes = $('.permission-checkbox');
    const groupCheckboxes = $('.group-checkbox');
    const searchInput = $('#permission-search');
    const selectAllBtn = $('#select-all');
    const unselectAllBtn = $('#unselect-all');
    
    // Update counts
    function updateCounts() {
        const totalSelected = permissionCheckboxes.filter(':checked').length;
        $('#selected-count').text(totalSelected);
        
        // Update category counts
        $('.category-count').each(function() {
            const category = $(this).data('category');
            const categoryCheckboxes = $(`.permission-checkbox[data-category="${category}"]`);
            const selectedInCategory = categoryCheckboxes.filter(':checked').length;
            const totalInCategory = categoryCheckboxes.length;
            
            $(this).text(`${selectedInCategory}/${totalInCategory}`);
            
            // Update group checkbox state
            const groupCheckbox = $(`#group-${category}`);
            if (selectedInCategory === 0) {
                groupCheckbox.prop('checked', false).prop('indeterminate', false);
            } else if (selectedInCategory === totalInCategory) {
                groupCheckbox.prop('checked', true).prop('indeterminate', false);
            } else {
                groupCheckbox.prop('checked', false).prop('indeterminate', true);
            }
        });
    }
    
    // Group checkbox change
    groupCheckboxes.on('change', function() {
        const category = $(this).data('category');
        const isChecked = $(this).prop('checked');
        
        $(`.permission-checkbox[data-category="${category}"]`).prop('checked', isChecked);
        updateCounts();
    });
    
    // Permission checkbox change
    permissionCheckboxes.on('change', function() {
        updateCounts();
    });
    
    // Search functionality
    searchInput.on('input', function() {
        const searchTerm = $(this).val().toLowerCase();
        let hasVisibleItems = false;
        
        $('.permission-item').each(function() {
            const permissionName = $(this).data('permission-name').toLowerCase();
            const isVisible = permissionName.includes(searchTerm);
            
            $(this).toggle(isVisible);
            if (isVisible) hasVisibleItems = true;
        });
        
        // Hide/show groups based on visible items
        $('.permission-group').each(function() {
            const hasVisiblePermissions = $(this).find('.permission-item:visible').length > 0;
            $(this).toggle(hasVisiblePermissions);
        });
        
        // Show/hide no results message
        $('#no-results').toggle(!hasVisibleItems);
    });
    
    // Select all visible permissions
    selectAllBtn.on('click', function() {
        $('.permission-item:visible .permission-checkbox').prop('checked', true);
        updateCounts();
    });
    
    // Unselect all visible permissions
    unselectAllBtn.on('click', function() {
        $('.permission-item:visible .permission-checkbox').prop('checked', false);
        updateCounts();
    });
    
    // Form submission
    $('#role-form').on('submit', function(e) {
        // Show loading state
        const submitBtn = $(this).find('button[type="submit"]');
        const originalText = submitBtn.html();
        
        submitBtn.prop('disabled', true).html(`
            <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
            Menyimpan...
        `);
        
        // Reset button state after a delay if form validation fails
        setTimeout(function() {
            submitBtn.prop('disabled', false).html(originalText);
        }, 3000);
    });
    
    // Initialize counts
    updateCounts();
});
</script>
@endpush
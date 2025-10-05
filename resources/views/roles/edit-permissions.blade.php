@extends('layouts.backend', ['title' => 'Edit Role Permissions'])

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
                        Edit Role Permissions
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
                        <li class="breadcrumb-item text-muted">Edit Permissions</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('content')
<div id="kt_app_content" class="app-content flex-column-fluid">
    <div id="kt_app_content_container" class="app-container container-fluid">
        
        <!-- Role Information Header -->
        <div class="card mb-6">
            <div class="card-header">
                <div class="d-flex align-items-center">
                    <div class="symbol symbol-50px me-5">
                        <div class="symbol-label bg-light-primary">
                            <i class="ki-outline ki-shield-tick fs-2x text-primary"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1">
                        <h3 class="text-gray-900 fw-bold mb-1">{{ str_replace('hrd_', '', $role->name) }}</h3>
                        <p class="text-muted mb-0">{{ $role->description ?? 'No description' }}</p>
                    </div>
                    <div class="text-end">
                        <span class="badge badge-light-primary fs-7 fw-bold">Level: {{ $role->level ?? 'N/A' }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Search and Actions -->
        <div class="card mb-6">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <div class="search-container">
                            <i class="ki-outline ki-magnifier fs-3 search-icon"></i>
                            <input type="text" id="permission-search" class="form-control search-input" 
                                   placeholder="Search permissions...">
                        </div>
                    </div>
                    <div class="col-md-6 text-end">
                        <button type="button" class="btn btn-sm btn-light-success me-2" id="select-all">
                            <i class="ki-outline ki-check-square fs-4 me-1"></i>
                            Select All
                        </button>
                        <button type="button" class="btn btn-sm btn-light-danger" id="deselect-all">
                            <i class="ki-outline ki-close-square fs-4 me-1"></i>
                            Deselect All
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Permissions Form -->
        <form action="{{ route('admin.user-management.roles.update-permissions', $role->id) }}" method="POST" id="permissions-form">
            @csrf
            @method('PUT')
            
            <!-- Permission Groups -->
            <div class="row g-6 mb-6">
                @foreach($permissionGroups as $groupName => $permissions)
                <div class="col-md-6 col-lg-4">
                    <div class="card permission-card h-100">
                        <div class="card-header group-header">
                            <div class="d-flex justify-content-between align-items-center w-100">
                                <h5 class="card-title text-white mb-0">
                                    <i class="ki-outline ki-security-user fs-2 me-2"></i>
                                    {{ $groupName }}
                                </h5>
                                <div class="form-check form-check-custom form-check-solid">
                                    <input type="checkbox" class="form-check-input group-select bg-white" 
                                           data-group="{{ Str::slug($groupName) }}" id="group-{{ Str::slug($groupName) }}">
                                    <label class="form-check-label text-white fw-bold" for="group-{{ Str::slug($groupName) }}">
                                        All
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="permission-list">
                                @foreach($permissions as $permission)
                                <div class="form-check form-check-custom form-check-solid permission-item p-2 rounded mb-2" 
                                     data-permission-name="{{ strtolower($permission->name) }}">
                                    <input type="checkbox" 
                                           name="permissions[]" 
                                           value="{{ $permission->id }}" 
                                           class="form-check-input permission-checkbox" 
                                           data-group="{{ Str::slug($groupName) }}"
                                           id="permission-{{ $permission->id }}"
                                           {{ in_array($permission->id, $rolePermissions) ? 'checked' : '' }}>
                                    <label class="form-check-label d-flex flex-column" for="permission-{{ $permission->id }}">
                                        <span class="fw-semibold text-gray-800">
                                            {{ str_replace(['hrd_', '_'], ['', ' '], $permission->name) }}
                                        </span>
                                        @if($permission->description)
                                        <small class="text-muted">{{ $permission->description }}</small>
                                        @endif
                                    </label>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="card-footer">
                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-muted">
                                    <span class="selected-count">{{ count(array_intersect($rolePermissions, collect($permissions)->pluck('id')->toArray())) }}</span> 
                                    of {{ count($permissions) }} selected
                                </small>
                                <div class="progress w-50px h-6px">
                                    <div class="progress-bar bg-success group-progress" 
                                         data-group="{{ Str::slug($groupName) }}"
                                         style="width: {{ count($permissions) > 0 ? (count(array_intersect($rolePermissions, collect($permissions)->pluck('id')->toArray())) / count($permissions)) * 100 : 0 }}%">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Summary Card -->
            <div class="card mb-6">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h5 class="mb-2">Permission Summary</h5>
                            <p class="text-muted mb-0">
                                <span id="total-selected">{{ count($rolePermissions) }}</span> 
                                of {{ collect($permissionGroups)->flatten()->count() }} permissions selected
                            </p>
                        </div>
                        <div class="col-md-4">
                            <div class="progress h-15px">
                                <div class="progress-bar bg-primary" id="total-progress" 
                                     style="width: {{ collect($permissionGroups)->flatten()->count() > 0 ? (count($rolePermissions) / collect($permissionGroups)->flatten()->count()) * 100 : 0 }}%">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="card">
                <div class="card-body text-center">
                    <button type="submit" class="btn btn-primary btn-lg me-3">
                        <i class="ki-outline ki-check fs-2 me-2"></i>
                        Save Permissions
                    </button>
                    <a href="{{ route('admin.user-management.roles.index') }}" class="btn btn-secondary btn-lg">
                        <i class="ki-outline ki-cross fs-2 me-2"></i>
                        Cancel
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Group selection functionality
    $('.group-select').on('change', function() {
        const group = $(this).data('group');
        const checked = $(this).is(':checked');
        $(`.permission-checkbox[data-group="${group}"]`).prop('checked', checked);
        updateGroupProgress(group);
        updateTotalSummary();
    });

    // Individual permission change
    $('.permission-checkbox').on('change', function() {
        const group = $(this).data('group');
        updateGroupSelect(group);
        updateGroupProgress(group);
        updateTotalSummary();
    });

    // Search functionality
    $('#permission-search').on('keyup', function() {
        const search = $(this).val().toLowerCase();
        $('.permission-item').each(function() {
            const permissionName = $(this).data('permission-name');
            const visible = permissionName.includes(search);
            $(this).toggle(visible);
        });
    });

    // Select all permissions
    $('#select-all').on('click', function() {
        $('.permission-checkbox').prop('checked', true);
        $('.group-select').prop('checked', true);
        updateAllGroupProgress();
        updateTotalSummary();
    });

    // Deselect all permissions
    $('#deselect-all').on('click', function() {
        $('.permission-checkbox').prop('checked', false);
        $('.group-select').prop('checked', false);
        updateAllGroupProgress();
        updateTotalSummary();
    });

    // Form submission with loading state
    $('#permissions-form').on('submit', function() {
        const submitBtn = $(this).find('button[type="submit"]');
        submitBtn.prop('disabled', true);
        submitBtn.html('<span class="spinner-border spinner-border-sm me-2"></span>Saving...');
    });

    // Functions
    function updateGroupSelect(group) {
        const groupCheckboxes = $(`.permission-checkbox[data-group="${group}"]`);
        const checkedCount = groupCheckboxes.filter(':checked').length;
        const totalCount = groupCheckboxes.length;
        
        const groupSelect = $(`.group-select[data-group="${group}"]`);
        groupSelect.prop('indeterminate', checkedCount > 0 && checkedCount < totalCount);
        groupSelect.prop('checked', checkedCount === totalCount);
    }

    function updateGroupProgress(group) {
        const groupCheckboxes = $(`.permission-checkbox[data-group="${group}"]`);
        const checkedCount = groupCheckboxes.filter(':checked').length;
        const totalCount = groupCheckboxes.length;
        const percentage = totalCount > 0 ? (checkedCount / totalCount) * 100 : 0;
        
        $(`.group-progress[data-group="${group}"]`).css('width', percentage + '%');
        
        // Update selected count in card footer
        const card = $(`.group-select[data-group="${group}"]`).closest('.card');
        card.find('.selected-count').text(checkedCount);
    }

    function updateAllGroupProgress() {
        $('.group-select').each(function() {
            const group = $(this).data('group');
            updateGroupProgress(group);
        });
    }

    function updateTotalSummary() {
        const totalSelected = $('.permission-checkbox:checked').length;
        const totalPermissions = $('.permission-checkbox').length;
        const percentage = totalPermissions > 0 ? (totalSelected / totalPermissions) * 100 : 0;
        
        $('#total-selected').text(totalSelected);
        $('#total-progress').css('width', percentage + '%');
    }

    // Initialize group states
    $('.group-select').each(function() {
        const group = $(this).data('group');
        updateGroupSelect(group);
        updateGroupProgress(group);
    });
});
</script>
@endpush
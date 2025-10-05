@extends('layouts.auth')

@section('title', 'Pilih Role')

@push('styles')
<style>
    .role-card {
        border: 2px solid #e9ecef;
        border-radius: 12px;
        transition: all 0.3s ease;
        cursor: pointer;
        background: white;
        height: 100%;
    }

    .role-card:hover {
        border-color: #007bff;
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(0, 123, 255, 0.15);
    }

    .role-card.selected {
        border-color: #007bff;
        background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
        color: white;
    }

    .role-card.selected .card-text {
        color: rgba(255, 255, 255, 0.9);
    }

    .role-icon {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 15px;
        font-size: 24px;
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        color: #6c757d;
        transition: all 0.3s ease;
    }

    .role-card.selected .role-icon {
        background: rgba(255, 255, 255, 0.2);
        color: white;
    }

    .role-title {
        font-weight: 600;
        font-size: 18px;
        margin-bottom: 8px;
    }

    .role-description {
        font-size: 14px;
        line-height: 1.4;
        color: #6c757d;
    }

    .role-card.selected .role-description {
        color: rgba(255, 255, 255, 0.9);
    }

    .continue-btn {
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        border: none;
        padding: 12px 30px;
        border-radius: 25px;
        color: white;
        font-weight: 600;
        transition: all 0.3s ease;
        min-width: 150px;
    }

    .continue-btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
        color: white;
    }

    .continue-btn:disabled {
        background: #6c757d;
        cursor: not-allowed;
        transform: none;
        box-shadow: none;
    }

    .auth-container {
        min-height: 100vh;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px;
    }

    .auth-card {
        background: white;
        border-radius: 15px;
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
        overflow: hidden;
        width: 100%;
        max-width: 900px;
    }

    .header-section {
        background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
        color: white;
        padding: 30px;
        text-align: center;
    }

    .welcome-text {
        font-size: 24px;
        font-weight: 700;
        margin-bottom: 8px;
    }

    .subtitle-text {
        font-size: 16px;
        opacity: 0.9;
        margin-bottom: 0;
    }
</style>
@endpush

@section('content')
<div class="auth-container">
    <div class="auth-card">
        <div class="header-section">
            <div class="welcome-text">Selamat Datang, {{ Auth::user()->name ?? Auth::user()->kd_karyawan }}</div>
            <div class="subtitle-text">Pilih role untuk melanjutkan ke dashboard</div>
        </div>
        
        <div class="p-4 p-md-5">
            <form id="roleSelectorForm">
                @csrf
                <div class="row g-4 mb-4">
                    @foreach($roleOptions as $role)
                    <div class="col-md-6 col-lg-4">
                        <div class="role-card h-100 p-4 text-center" data-role="{{ $role['name'] }}">
                            <div class="role-icon">
                                <i class="{{ $role['icon'] }}"></i>
                            </div>
                            <div class="role-title">{{ $role['display_name'] }}</div>
                            <div class="role-description">{{ $role['description'] }}</div>
                        </div>
                    </div>
                    @endforeach
                </div>

                <div class="text-center">
                    <button type="submit" class="btn continue-btn" id="continueBtn" disabled>
                        <i class="fas fa-arrow-right me-2"></i>
                        Lanjutkan
                    </button>
                </div>
            </form>

            <div class="text-center mt-4">
                <a href="{{ route('logout') }}" 
                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();" 
                   class="text-muted">
                    <i class="fas fa-sign-out-alt me-1"></i>
                    Keluar
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    let selectedRole = null;

    // Handle role card selection
    $('.role-card').on('click', function() {
        // Remove selection from all cards
        $('.role-card').removeClass('selected');
        
        // Add selection to clicked card
        $(this).addClass('selected');
        
        // Store selected role
        selectedRole = $(this).data('role');
        
        // Enable continue button
        $('#continueBtn').prop('disabled', false);
    });

    // Handle form submission
    $('#roleSelectorForm').on('submit', function(e) {
        e.preventDefault();
        
        if (!selectedRole) {
            toastr.warning('Silakan pilih role terlebih dahulu');
            return;
        }

        $('#continueBtn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Memproses...');

        $.ajax({
            url: '{{ route('role-selector.select') }}',
            method: 'POST',
            data: {
                _token: $('input[name="_token"]').val(),
                role: selectedRole
            },
            success: function(response) {
                if (response.success) {
                    toastr.success('Role berhasil dipilih. Mengalihkan...');
                    setTimeout(function() {
                        window.location.href = response.redirect;
                    }, 1000);
                } else {
                    toastr.error(response.message || 'Terjadi kesalahan');
                    $('#continueBtn').prop('disabled', false).html('<i class="fas fa-arrow-right me-2"></i>Lanjutkan');
                }
            },
            error: function(xhr) {
                let message = 'Terjadi kesalahan saat memproses permintaan';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                }
                toastr.error(message);
                $('#continueBtn').prop('disabled', false).html('<i class="fas fa-arrow-right me-2"></i>Lanjutkan');
            }
        });
    });

    // Add hover effects
    $('.role-card').on('mouseenter', function() {
        if (!$(this).hasClass('selected')) {
            $(this).css('border-color', '#007bff');
        }
    }).on('mouseleave', function() {
        if (!$(this).hasClass('selected')) {
            $(this).css('border-color', '#e9ecef');
        }
    });
});
</script>
@endpush
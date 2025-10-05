<!DOCTYPE html>
<html lang="en">
<head>
    <title>@yield('title') - RSUD Langsa | HRD</title>
    <meta charset="utf-8" />
    <meta name="description" content="HRD - RSUD LANGSA" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta property="og:locale" content="en_US" />
    <meta property="og:type" content="article" />
    <meta property="og:title" content="@yield('title') - RSUD Langsa HRD" />
    <meta property="og:site_name" content="RSUD LANGSA" />
    
    <link rel="shortcut icon" href="{{ asset('assets/media/logos/favicon.ico') }}" />
    
    <!--begin::Fonts-->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700" />
    
    <!--begin::Global Stylesheets Bundle-->
    <link href="{{ asset('assets/plugins/global/plugins.bundle.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/style.bundle.css') }}" rel="stylesheet" type="text/css" />
    
    <style>
        .error-page {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .error-content {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            padding: 3rem;
            text-align: center;
            max-width: 600px;
            width: 90%;
            margin: 2rem;
        }
        
        .error-code {
            font-size: 8rem;
            font-weight: 700;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            line-height: 1;
            margin-bottom: 1rem;
        }
        
        .error-title {
            color: #333;
            font-size: 2.5rem;
            font-weight: 600;
            margin-bottom: 1rem;
        }
        
        .error-description {
            color: #666;
            font-size: 1.1rem;
            line-height: 1.6;
            margin-bottom: 2rem;
        }
        
        .error-actions {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
        }
        
        .btn-error-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: white;
            padding: 12px 30px;
            border-radius: 50px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        
        .btn-error-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.4);
            color: white;
            text-decoration: none;
        }
        
        .btn-error-secondary {
            background: transparent;
            border: 2px solid #667eea;
            color: #667eea;
            padding: 10px 28px;
            border-radius: 50px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        
        .btn-error-secondary:hover {
            background: #667eea;
            color: white;
            text-decoration: none;
            transform: translateY(-2px);
        }
        
        .error-animation {
            animation: float 3s ease-in-out infinite;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }
        
        .logo-container {
            margin-bottom: 2rem;
        }
        
        .logo-container img {
            max-height: 60px;
            width: auto;
        }
        
        @media (max-width: 768px) {
            .error-code {
                font-size: 5rem;
            }
            
            .error-title {
                font-size: 2rem;
            }
            
            .error-content {
                padding: 2rem;
                margin: 1rem;
            }
            
            .error-actions {
                flex-direction: column;
                align-items: center;
            }
        }
    </style>
</head>

<body>
    <div class="error-page">
        <div class="error-content error-animation">
            <!-- Logo -->
            <div class="logo-container">
                <img src="https://rsud_hrd.me/assets/media/logos/logo-hrd.png" alt="RSUD Langsa HRD" />
            </div>
            
            <!-- Error Code -->
            <div class="error-code">
                @yield('code')
            </div>
            
            <!-- Error Title -->
            <h1 class="error-title">
                @yield('title')
            </h1>
            
            <!-- Error Description -->
            <p class="error-description">
                @yield('message')
            </p>
            
            <!-- Actions -->
            <div class="error-actions">
                @yield('actions')
            </div>
            
            <!-- Additional Content -->
            @yield('additional-content')
        </div>
    </div>
    
    <!--begin::Javascript-->
    <script src="{{ asset('assets/plugins/global/plugins.bundle.js') }}"></script>
    <script src="{{ asset('assets/js/scripts.bundle.js') }}"></script>
    
    <script>
        // Auto redirect after countdown (optional)
        @if(isset($autoRedirect) && $autoRedirect)
        let countdown = {{ $countdown ?? 10 }};
        const countdownElement = document.getElementById('countdown');
        
        const timer = setInterval(() => {
            countdown--;
            if (countdownElement) {
                countdownElement.textContent = countdown;
            }
            
            if (countdown <= 0) {
                clearInterval(timer);
                window.location.href = '{{ $redirectUrl ?? route("admin.dashboard.index") }}';
            }
        }, 1000);
        @endif
        
        // Back button functionality
        function goBack() {
            if (window.history.length > 1) {
                window.history.back();
            } else {
                window.location.href = '{{ route("admin.dashboard.index") }}';
            }
        }
    </script>
    
    @stack('scripts')
</body>
</html>
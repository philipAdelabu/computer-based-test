<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
    <meta name="theme-color" content="#667eea">
    <title>Login - {{ \App\Helpers\SettingHelper::schoolName() }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; }
        html, body {
            overflow-x: hidden;
            max-width: 100vw;
            margin: 0;
            padding: 0;
        }
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Poppins', sans-serif;
            padding: 1rem;
            position: relative;
        }
        
        /* Decorative circles */
        body::before,
        body::after {
            content: '';
            position: fixed;
            border-radius: 50%;
            background: rgba(255,255,255,0.06);
            z-index: 0;
        }
        body::before {
            width: 300px;
            height: 300px;
            top: -100px;
            right: -100px;
        }
        body::after {
            width: 400px;
            height: 400px;
            bottom: -150px;
            left: -150px;
        }
        
        /* Login card */
        .login-card {
            background: white;
            border-radius: 20px;
            padding: 2.25rem 1.75rem;
            box-shadow: 0 20px 60px rgba(0,0,0,0.25);
            max-width: 420px;
            width: 100%;
            position: relative;
            z-index: 1;
        }
        
        /* Logo */
        .login-logo {
            text-align: center;
            margin-bottom: 1.5rem;
        }
        .login-logo-img {
            max-width: 80px;
            max-height: 80px;
            object-fit: contain;
            margin-bottom: 0.75rem;
            border-radius: 12px;
        }
        .login-logo-icon {
            font-size: 3rem;
            color: #667eea;
            display: block;
            margin-bottom: 0.5rem;
        }
        .login-school-name {
            font-weight: 700;
            color: #2d3748;
            font-size: 1.35rem;
            margin: 0;
            line-height: 1.3;
        }
        .login-motto {
            font-size: 0.8rem;
            color: #718096;
            font-style: italic;
            margin-top: 0.25rem;
        }
        
        /* Form */
        .form-label {
            font-size: 0.85rem;
            font-weight: 500;
            color: #2d3748;
            margin-bottom: 0.4rem;
        }
        .form-control {
            border-radius: 12px;
            padding: 0.75rem 1rem;
            border: 2px solid #e2e8f0;
            font-size: 0.95rem;
            transition: all 0.2s;
            min-height: 48px;
        }
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.15);
        }
        .input-group-text {
            background: white;
            border: 2px solid #e2e8f0;
            border-right: none;
            border-radius: 12px 0 0 12px;
            color: #718096;
        }
        .input-group .form-control {
            border-left: none;
            border-radius: 0 12px 12px 0;
        }
        .input-group:focus-within .input-group-text {
            border-color: #667eea;
        }
        
        /* Button */
        .btn-login {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 0.85rem 1rem;
            border-radius: 12px;
            font-weight: 600;
            color: white;
            width: 100%;
            font-size: 1rem;
            transition: all 0.3s;
            min-height: 52px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.4);
            color: white;
        }
        .btn-login:active { transform: translateY(0); }
        
        /* Demo credentials */
        .demo-credentials {
            background: #f7fafc;
            border-radius: 10px;
            padding: 0.85rem 1rem;
            font-size: 0.75rem;
            margin-top: 1.25rem;
            line-height: 1.8;
        }
        .demo-credentials strong {
            color: #2d3748;
            display: block;
            margin-bottom: 0.35rem;
            font-size: 0.8rem;
        }
        .demo-credentials .cred-row {
            color: #718096;
        }
        .demo-credentials .cred-row strong {
            display: inline;
            color: #4a5568;
        }
        
        /* Mobile */
        @media (max-width: 575.98px) {
            body { padding: 0.75rem; align-items: flex-start; padding-top: 2rem; }
            .login-card {
                padding: 1.75rem 1.25rem;
                border-radius: 16px;
            }
            .login-school-name { font-size: 1.2rem; }
            .login-logo-img { max-width: 70px; max-height: 70px; }
            .login-logo-icon { font-size: 2.5rem; }
        }
        
        @media (max-width: 360px) {
            .login-card { padding: 1.5rem 1rem; }
            .login-school-name { font-size: 1.1rem; }
        }
    </style>
</head>
<body>
    @php
        $logoUrl = \App\Helpers\SettingHelper::schoolLogo();
        $schoolName = \App\Helpers\SettingHelper::schoolName();
        $schoolMotto = \App\Helpers\SettingHelper::schoolMotto();
    @endphp
    
    <div class="login-card">
        <!-- Logo & School Name -->
        <div class="login-logo">
            @if($logoUrl)
                <img src="{{ $logoUrl }}" alt="{{ $schoolName }}" class="login-logo-img">
            @else
                <i class="bi bi-laptop login-logo-icon"></i>
            @endif
            <h1 class="login-school-name">{{ $schoolName }}</h1>
            @if($schoolMotto)
                <div class="login-motto">"{{ $schoolMotto }}"</div>
            @endif
        </div>
        
        <!-- Login Form -->
        <form method="POST" action="{{ route('login') }}">
            @csrf
            
            <div class="mb-3">
                <label class="form-label">Email Address</label>
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="bi bi-envelope"></i>
                    </span>
                    <input type="email" 
                           name="email" 
                           class="form-control @error('email') is-invalid @enderror" 
                           placeholder="Enter your email" 
                           value="{{ old('email') }}" 
                           autocomplete="email"
                           autofocus 
                           required>
                </div>
                @error('email')
                    <small class="text-danger d-block mt-1">
                        <i class="bi bi-exclamation-circle"></i> {{ $message }}
                    </small>
                @enderror
            </div>
            
            <div class="mb-4">
                <label class="form-label">Password</label>
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="bi bi-lock"></i>
                    </span>
                    <input type="password" 
                           name="password" 
                           id="passwordInput"
                           class="form-control @error('password') is-invalid @enderror" 
                           placeholder="Enter your password"
                           autocomplete="current-password"
                           required>
                    <button type="button" 
                            class="btn btn-outline-secondary" 
                            id="togglePassword"
                            style="border-radius: 0 12px 12px 0; border: 2px solid #e2e8f0; border-left: none; background: white; color: #718096;"
                            tabindex="-1">
                        <i class="bi bi-eye" id="toggleIcon"></i>
                    </button>
                </div>
                @error('password')
                    <small class="text-danger d-block mt-1">
                        <i class="bi bi-exclamation-circle"></i> {{ $message }}
                    </small>
                @enderror
            </div>
            
            <button type="submit" class="btn-login">
                <i class="bi bi-box-arrow-in-right"></i>
                Sign In
            </button>
        </form>
        
        <!-- Demo Credentials -->
        <div class="demo-credentials">
            <strong><i class="bi bi-info-circle"></i> Demo Credentials</strong>
            <div class="cred-row"><strong>Admin:</strong> admin@cbt.com / password</div>
            <div class="cred-row"><strong>Teacher:</strong> teacher@cbt.com / password</div>
            <div class="cred-row"><strong>Student:</strong> student@cbt.com / password</div>
        </div>
    </div>
    
    <script>
        // Password visibility toggle
        document.getElementById('togglePassword').addEventListener('click', function() {
            const passwordInput = document.getElementById('passwordInput');
            const toggleIcon = document.getElementById('toggleIcon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('bi-eye');
                toggleIcon.classList.add('bi-eye-slash');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('bi-eye-slash');
                toggleIcon.classList.add('bi-eye');
            }
        });
    </script>
</body>
</html>
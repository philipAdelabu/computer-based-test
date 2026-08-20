<!-- resources/views/auth/login.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - CBT System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .login-card {
            background: white;
            border-radius: 1.5rem;
            padding: 3rem;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            max-width: 450px;
            width: 100%;
        }
        .login-card .logo {
            text-align: center;
            margin-bottom: 2rem;
        }
        .login-card .logo i {
            font-size: 3rem;
            color: #667eea;
        }
        .login-card .logo h3 {
            font-weight: 700;
            color: #2d3748;
            margin-top: 0.5rem;
        }
        .form-control {
            border-radius: 0.75rem;
            padding: 0.75rem 1rem;
            border: 2px solid #e2e8f0;
            transition: all 0.3s;
        }
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        .btn-login {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 0.75rem;
            border-radius: 0.75rem;
            font-weight: 600;
            color: white;
            width: 100%;
            transition: all 0.3s;
        }
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.4);
        }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="logo">
            <i class="bi bi-laptop"></i>
            <h3>CBT System</h3>
            <p class="text-muted">Computer Based Test Platform</p>
        </div>
        
        <form method="POST" action="{{ route('login') }}">
            @csrf
            <div class="mb-3">
                <label class="form-label fw-semibold">Email Address</label>
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0">
                        <i class="bi bi-envelope text-muted"></i>
                    </span>
                    <input type="email" name="email" class="form-control border-start-0" 
                           placeholder="Enter your email" value="{{ old('email') }}" required autofocus>
                </div>
                @error('email')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>
            
            <div class="mb-4">
                <label class="form-label fw-semibold">Password</label>
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0">
                        <i class="bi bi-lock text-muted"></i>
                    </span>
                    <input type="password" name="password" class="form-control border-start-0" 
                           placeholder="Enter your password" required>
                </div>
                @error('password')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>
            
            <button type="submit" class="btn btn-login">
                <i class="bi bi-box-arrow-in-right me-2"></i> Sign In
            </button>
        </form>
        
        <div class="text-center mt-3">
            <small class="text-muted">Demo Credentials:</small><br>
            <small class="text-muted">Admin: admin@cbt.com / password</small><br>
            <small class="text-muted">Teacher: teacher@cbt.com / password</small><br>
            <small class="text-muted">Student: student@cbt.com / password</small>
        </div>
    </div>
</body>
</html>
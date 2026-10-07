<!-- resources/views/welcome.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ \App\Helpers\SettingHelper::schoolName() }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .hero-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 80px 20px;
            flex: 1;
            display: flex;
            align-items: center;
        }
        .hero-logo {
            max-width: 120px;
            max-height: 120px;
            object-fit: contain;
            margin-bottom: 1.5rem;
            background: white;
            border-radius: 20px;
            padding: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
        .hero-logo-icon {
            font-size: 5rem;
            color: white;
            margin-bottom: 1rem;
        }
        .hero-title {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }
        .hero-motto {
            font-size: 1.1rem;
            font-style: italic;
            opacity: 0.95;
            margin-bottom: 1.5rem;
        }
        .hero-description {
            font-size: 1.05rem;
            opacity: 0.95;
            max-width: 600px;
            margin: 0 auto 2rem;
        }
        .btn-hero {
            background: white;
            color: #667eea;
            border: none;
            padding: 0.9rem 2.5rem;
            border-radius: 50px;
            font-weight: 600;
            font-size: 1rem;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-block;
        }
        .btn-hero:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.3);
            color: #667eea;
        }
        .feature-card {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            text-align: center;
            height: 100%;
            transition: all 0.3s;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        }
        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.15);
        }
        .feature-icon {
            font-size: 3rem;
            color: #667eea;
            margin-bottom: 1rem;
        }
        .feature-title {
            font-size: 1.1rem;
            font-weight: 700;
            color: #2d3748;
            margin-bottom: 0.5rem;
        }
        .feature-description {
            color: #718096;
            font-size: 0.9rem;
        }
        .features-section {
            background: #f7fafc;
            padding: 60px 20px;
        }
        footer {
            background: #2d3748;
            color: white;
            text-align: center;
            padding: 20px;
            font-size: 0.9rem;
        }
        footer .school-info {
            opacity: 0.85;
            margin-bottom: 0.5rem;
        }
    </style>
</head>
<body>
    @php
        $logoUrl = \App\Helpers\SettingHelper::schoolLogo();
        $schoolName = \App\Helpers\SettingHelper::schoolName();
        $schoolMotto = \App\Helpers\SettingHelper::schoolMotto();
        $landingTitle = \App\Models\Setting::get('landing_page_title', 'Welcome to Our CBT System');
        $landingDescription = \App\Models\Setting::get('landing_page_description', 'A modern computer-based testing platform for schools and institutions.');
        $footerText = \App\Models\Setting::get('footer_text', '© ' . date('Y') . ' CBT System. All rights reserved.');
        $schoolAddress = \App\Helpers\SettingHelper::schoolAddress();
        $schoolPhone = \App\Helpers\SettingHelper::schoolPhone();
        $schoolEmail = \App\Helpers\SettingHelper::schoolEmail();
    @endphp
    
    <!-- HERO SECTION -->
    <section class="hero-section">
        <div class="container text-center">
            @if($logoUrl)
                <img src="{{ $logoUrl }}" alt="{{ $schoolName }}" class="hero-logo">
            @else
                <i class="bi bi-laptop hero-logo-icon"></i>
            @endif
            
            <h1 class="hero-title">{{ $schoolName }}</h1>
            
            @if($schoolMotto)
                <p class="hero-motto">"{{ $schoolMotto }}"</p>
            @endif
            
            <p class="hero-description">{{ $landingDescription }}</p>
            
            <a href="{{ route('login') }}" class="btn-hero">
                <i class="bi bi-box-arrow-in-right me-2"></i> Login to Continue
            </a>
        </div>
    </section>
    
    <!-- FEATURES SECTION -->
    <section class="features-section">
        <div class="container">
            <h2 class="text-center mb-5" style="font-weight: 700; color: #2d3748;">
                {{ $landingTitle }}
            </h2>
            
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="feature-card">
                        <i class="bi bi-file-text feature-icon"></i>
                        <div class="feature-title">Computer-Based Tests</div>
                        <p class="feature-description mb-0">
                            Take timed assessments online with an intuitive interface and instant results.
                        </p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card">
                        <i class="bi bi-bar-chart feature-icon"></i>
                        <div class="feature-title">Performance Tracking</div>
                        <p class="feature-description mb-0">
                            Monitor student progress with detailed analytics and comprehensive reports.
                        </p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card">
                        <i class="bi bi-file-earmark-bar-graph feature-icon"></i>
                        <div class="feature-title">Report Cards</div>
                        <p class="feature-description mb-0">
                            Automatically generated report cards combining test and exam scores.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <!-- FOOTER -->
    <footer>
        @if($schoolAddress || $schoolPhone || $schoolEmail)
            <div class="school-info">
                @if($schoolAddress)<i class="bi bi-geo-alt"></i> {{ $schoolAddress }}@endif
                @if($schoolPhone) &nbsp;|&nbsp; <i class="bi bi-telephone"></i> {{ $schoolPhone }}@endif
                @if($schoolEmail) &nbsp;|&nbsp; <i class="bi bi-envelope"></i> {{ $schoolEmail }}@endif
            </div>
        @endif
        <div>{{ $footerText }}</div>
    </footer>
</body>
</html>
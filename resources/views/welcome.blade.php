<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
    <meta name="theme-color" content="#667eea">
    <title>{{ \App\Helpers\SettingHelper::schoolName() }}</title>
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
            font-family: 'Poppins', sans-serif;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        
        /* ============ HERO SECTION ============ */
        .hero-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 60px 20px 80px;
            flex: 1;
            display: flex;
            align-items: center;
            position: relative;
            overflow: hidden;
            text-align: center;
        }
        .hero-section::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -20%;
            width: 500px;
            height: 500px;
            background: rgba(255,255,255,0.05);
            border-radius: 50%;
        }
        .hero-section::after {
            content: '';
            position: absolute;
            bottom: -30%;
            left: -10%;
            width: 400px;
            height: 400px;
            background: rgba(255,255,255,0.05);
            border-radius: 50%;
        }
        .hero-content {
            position: relative;
            z-index: 2;
            max-width: 700px;
            margin: 0 auto;
        }
        .hero-logo {
            max-width: 100px;
            max-height: 100px;
            object-fit: contain;
            margin-bottom: 1.25rem;
            background: white;
            border-radius: 20px;
            padding: 12px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
        .hero-logo-icon {
            font-size: 4rem;
            color: white;
            margin-bottom: 1rem;
            display: block;
        }
        .hero-title {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0.75rem;
            line-height: 1.2;
        }
        .hero-motto {
            font-size: 1rem;
            font-style: italic;
            opacity: 0.95;
            margin-bottom: 1.25rem;
        }
        .hero-description {
            font-size: 1rem;
            opacity: 0.95;
            margin-bottom: 2rem;
            line-height: 1.6;
        }
        .btn-hero {
            background: white;
            color: #667eea;
            border: none;
            padding: 0.9rem 2rem;
            border-radius: 50px;
            font-weight: 600;
            font-size: 1rem;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            min-height: 52px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.15);
        }
        .btn-hero:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.25);
            color: #667eea;
        }
        .btn-hero:active { transform: translateY(-1px); }
        
        /* ============ FEATURES SECTION ============ */
        .features-section {
            background: #f7fafc;
            padding: 50px 20px;
        }
        .section-title {
            text-align: center;
            font-weight: 700;
            font-size: 1.5rem;
            color: #2d3748;
            margin-bottom: 2rem;
        }
        .feature-card {
            background: white;
            border-radius: 16px;
            padding: 1.75rem 1.25rem;
            text-align: center;
            height: 100%;
            transition: all 0.3s;
            box-shadow: 0 4px 15px rgba(0,0,0,0.06);
            border: 1px solid #e9eef5;
        }
        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.12);
            border-color: #667eea;
        }
        .feature-icon {
            font-size: 2.5rem;
            color: #667eea;
            margin-bottom: 0.85rem;
            display: inline-block;
        }
        .feature-title {
            font-size: 1.05rem;
            font-weight: 700;
            color: #2d3748;
            margin-bottom: 0.5rem;
        }
        .feature-description {
            color: #718096;
            font-size: 0.9rem;
            line-height: 1.6;
            margin: 0;
        }
        
        /* ============ FOOTER ============ */
        footer {
            background: #2d3748;
            color: white;
            padding: 25px 20px;
            font-size: 0.85rem;
            text-align: center;
        }
        footer .school-info {
            opacity: 0.85;
            margin-bottom: 0.5rem;
            line-height: 1.8;
        }
        footer .school-info a {
            color: white;
            text-decoration: none;
        }
        footer .school-info i {
            margin-right: 0.25rem;
        }
        
        /* ============ MOBILE OPTIMIZATIONS ============ */
        @media (max-width: 767.98px) {
            .hero-section { padding: 40px 16px 60px; }
            .hero-logo { max-width: 80px; max-height: 80px; padding: 10px; }
            .hero-logo-icon { font-size: 3rem; }
            .hero-title { font-size: 1.5rem; }
            .hero-motto { font-size: 0.9rem; }
            .hero-description { font-size: 0.9rem; }
            .btn-hero {
                width: 100%;
                padding: 0.85rem 1.5rem;
                font-size: 0.95rem;
            }
            .features-section { padding: 35px 16px; }
            .section-title { font-size: 1.25rem; margin-bottom: 1.5rem; }
            .feature-card { padding: 1.5rem 1rem; }
            .feature-icon { font-size: 2rem; }
            .feature-title { font-size: 0.95rem; }
            .feature-description { font-size: 0.85rem; }
        }
        
        @media (max-width: 400px) {
            .hero-title { font-size: 1.35rem; }
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
    
    <!-- HERO -->
    <section class="hero-section">
        <div class="hero-content">
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
                <i class="bi bi-box-arrow-in-right"></i> 
                <span>Login to Continue</span>
            </a>
        </div>
    </section>
    
    <!-- FEATURES -->
    <section class="features-section">
        <div class="container">
            <h2 class="section-title">{{ $landingTitle }}</h2>
            
            <div class="row g-3 g-md-4">
                <div class="col-12 col-md-4">
                    <div class="feature-card">
                        <i class="bi bi-file-text feature-icon"></i>
                        <div class="feature-title">Computer-Based Tests</div>
                        <p class="feature-description">
                            Take timed assessments online with an intuitive interface and instant results.
                        </p>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="feature-card">
                        <i class="bi bi-bar-chart feature-icon"></i>
                        <div class="feature-title">Performance Tracking</div>
                        <p class="feature-description">
                            Monitor student progress with detailed analytics and comprehensive reports.
                        </p>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="feature-card">
                        <i class="bi bi-file-earmark-bar-graph feature-icon"></i>
                        <div class="feature-title">Report Cards</div>
                        <p class="feature-description">
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
                @if($schoolAddress)
                    <div><i class="bi bi-geo-alt"></i> {{ $schoolAddress }}</div>
                @endif
                <div>
                    @if($schoolPhone)
                        <a href="tel:{{ $schoolPhone }}"><i class="bi bi-telephone"></i> {{ $schoolPhone }}</a>
                    @endif
                    @if($schoolPhone && $schoolEmail) &nbsp;·&nbsp; @endif
                    @if($schoolEmail)
                        <a href="mailto:{{ $schoolEmail }}"><i class="bi bi-envelope"></i> {{ $schoolEmail }}</a>
                    @endif
                </div>
            </div>
        @endif
        <div>{{ $footerText }}</div>
    </footer>
</body>
</html>
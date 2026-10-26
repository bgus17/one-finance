<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password - Koperasi ONE FINANCE</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * {
            font-family: 'Inter', sans-serif;
        }

        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #0a0e1a;
            margin: 0;
            padding: 20px;
            position: relative;
            overflow: hidden;
        }

        body::before {
            content: '';
            position: fixed;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: 
                radial-gradient(ellipse at 20% 50%, rgba(77, 171, 247, 0.08) 0%, transparent 60%),
                radial-gradient(ellipse at 80% 50%, rgba(102, 16, 242, 0.08) 0%, transparent 60%),
                radial-gradient(ellipse at 50% 100%, rgba(77, 171, 247, 0.04) 0%, transparent 50%);
            animation: rotateBackground 30s linear infinite;
            z-index: 0;
        }

        @keyframes rotateBackground {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .particle {
            position: fixed;
            border-radius: 50%;
            pointer-events: none;
            z-index: 0;
            animation: floatParticle 20s ease-in-out infinite;
        }

        .particle:nth-child(1) {
            width: 300px;
            height: 300px;
            background: radial-gradient(circle, rgba(77, 171, 247, 0.03) 0%, transparent 70%);
            top: -100px;
            left: -100px;
            animation-delay: 0s;
        }

        .particle:nth-child(2) {
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, rgba(102, 16, 242, 0.03) 0%, transparent 70%);
            bottom: -150px;
            right: -150px;
            animation-delay: -5s;
        }

        .particle:nth-child(3) {
            width: 200px;
            height: 200px;
            background: radial-gradient(circle, rgba(77, 171, 247, 0.02) 0%, transparent 70%);
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            animation-delay: -10s;
        }

        @keyframes floatParticle {
            0%, 100% { transform: translate(0, 0) scale(1); }
            33% { transform: translate(30px, -20px) scale(1.1); }
            66% { transform: translate(-20px, 30px) scale(0.9); }
        }

        .login-wrapper {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 440px;
        }

        .login-card {
            background: rgba(255, 255, 255, 0.04);
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            border: 1px solid rgba(255, 255, 255, 0.06);
            border-radius: 24px;
            padding: 40px 36px;
            box-shadow: 0 24px 80px rgba(0, 0, 0, 0.4), inset 0 1px 0 rgba(255, 255, 255, 0.05);
            position: relative;
            overflow: hidden;
        }

        .login-card::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 100%;
            height: 100%;
            background: radial-gradient(ellipse at 70% 30%, rgba(77, 171, 247, 0.04) 0%, transparent 60%);
            pointer-events: none;
        }

        .login-logo {
            text-align: center;
            margin-bottom: 32px;
            position: relative;
        }

        .login-logo .logo-icon {
            width: 72px;
            height: 72px;
            margin: 0 auto 16px;
            background: linear-gradient(135deg, rgba(77, 171, 247, 0.15), rgba(102, 16, 242, 0.1));
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid rgba(255, 255, 255, 0.06);
            position: relative;
        }

        .login-logo .logo-icon img {
            height: 44px;
            width: auto;
            filter: brightness(1.1);
        }

        .login-logo .logo-icon .glow {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 120px;
            height: 120px;
            background: radial-gradient(circle, rgba(77, 171, 247, 0.1) 0%, transparent 70%);
            border-radius: 50%;
            animation: pulseGlow 3s ease-in-out infinite;
        }

        @keyframes pulseGlow {
            0%, 100% { transform: translate(-50%, -50%) scale(1); opacity: 0.6; }
            50% { transform: translate(-50%, -50%) scale(1.3); opacity: 1; }
        }

        .login-logo h2 {
            font-weight: 700;
            font-size: 22px;
            color: #ffffff;
            margin: 0;
            letter-spacing: -0.5px;
        }

        .login-logo p {
            color: rgba(255, 255, 255, 0.4);
            font-size: 13px;
            margin: 4px 0 0;
            letter-spacing: 0.5px;
        }

        .login-logo .sub-text {
            color: rgba(255, 255, 255, 0.3);
            font-size: 14px;
            margin-top: 8px;
        }

        .login-form .form-group {
            margin-bottom: 18px;
            position: relative;
        }

        .login-form .form-label {
            color: rgba(255, 255, 255, 0.6);
            font-size: 13px;
            font-weight: 500;
            margin-bottom: 6px;
            display: block;
            letter-spacing: 0.3px;
        }

        .login-form .form-label i {
            margin-right: 6px;
            color: rgba(77, 171, 247, 0.5);
        }

        .login-form .form-control {
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid rgba(255, 255, 255, 0.06);
            border-radius: 12px;
            padding: 12px 16px;
            color: #ffffff;
            font-size: 14px;
            transition: all 0.3s ease;
            height: 50px;
        }

        .login-form .form-control::placeholder {
            color: rgba(255, 255, 255, 0.2);
        }

        .login-form .form-control:focus {
            background: rgba(255, 255, 255, 0.06);
            border-color: rgba(77, 171, 247, 0.3);
            box-shadow: 0 0 0 4px rgba(77, 171, 247, 0.05);
            outline: none;
            color: #ffffff;
        }

        .login-form .form-control.is-invalid {
            border-color: rgba(255, 107, 107, 0.3);
            box-shadow: 0 0 0 4px rgba(255, 107, 107, 0.05);
        }

        .login-form .form-control.is-invalid:focus {
            border-color: rgba(255, 107, 107, 0.4);
            box-shadow: 0 0 0 4px rgba(255, 107, 107, 0.08);
        }

        .login-form .invalid-feedback {
            color: #ff6b6b;
            font-size: 12px;
            margin-top: 4px;
        }

        .btn-login {
            width: 100%;
            height: 50px;
            background: linear-gradient(135deg, #4dabf7, #6610f2);
            border: none;
            border-radius: 12px;
            color: #ffffff;
            font-weight: 600;
            font-size: 15px;
            transition: all 0.4s ease;
            cursor: pointer;
            position: relative;
            overflow: hidden;
            margin-top: 4px;
        }

        .btn-login::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 200%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.06), transparent);
            transition: all 0.6s ease;
        }

        .btn-login:hover::before {
            left: 100%;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 40px rgba(77, 171, 247, 0.3);
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .btn-login i {
            margin-right: 8px;
        }

        .btn-back {
            width: 100%;
            height: 50px;
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid rgba(255, 255, 255, 0.06);
            border-radius: 12px;
            color: rgba(255, 255, 255, 0.6);
            font-weight: 500;
            font-size: 14px;
            transition: all 0.3s ease;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            margin-top: 8px;
        }

        .btn-back:hover {
            background: rgba(255, 255, 255, 0.06);
            color: #ffffff;
            text-decoration: none;
        }

        .btn-back i {
            margin-right: 8px;
        }

        .alert-custom {
            background: rgba(255, 107, 107, 0.06);
            border: 1px solid rgba(255, 107, 107, 0.1);
            border-radius: 12px;
            color: #ff6b6b;
            padding: 12px 16px;
            font-size: 13px;
            margin-bottom: 18px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .alert-custom i {
            font-size: 16px;
        }

        .alert-custom-success {
            background: rgba(46, 204, 113, 0.06);
            border: 1px solid rgba(46, 204, 113, 0.1);
            color: #2ecc71;
        }

        .login-footer {
            text-align: center;
            margin-top: 20px;
        }

        .login-footer small {
            color: rgba(255, 255, 255, 0.15);
            font-size: 12px;
            letter-spacing: 0.3px;
        }

        .login-footer small i {
            color: rgba(255, 107, 107, 0.3);
        }

        @media (max-width: 480px) {
            .login-card {
                padding: 28px 20px;
                border-radius: 20px;
            }
            .login-logo .logo-icon {
                width: 60px;
                height: 60px;
            }
            .login-logo .logo-icon img {
                height: 36px;
            }
            .login-logo h2 {
                font-size: 20px;
            }
        }
    </style>
</head>
<body>

    {{-- Floating Particles --}}
    <div class="particle"></div>
    <div class="particle"></div>
    <div class="particle"></div>

    {{-- ============================================ --}}
    {{-- LOGIN WRAPPER --}}
    {{-- ============================================ --}}
    <div class="login-wrapper">

        <div class="login-card">

            {{-- LOGO --}}
            <div class="login-logo">
                <div class="logo-icon">
                    <img src="{{ asset('assets/img/one-finance.png') }}" alt="Logo">
                    <div class="glow"></div>
                </div>
                <h2>ONE FINANCE</h2>
                <p>Lembaga Keuangan Mikro</p>
                <div class="sub-text">
                    <i class="fas fa-key me-2"></i> Reset Password
                </div>
            </div>

            {{-- ALERT --}}
            @if(session('error'))
                <div class="alert-custom">
                    <i class="fas fa-exclamation-circle"></i>
                    {{ session('error') }}
                </div>
            @endif

            @if(session('success'))
                <div class="alert-custom alert-custom-success">
                    <i class="fas fa-check-circle"></i>
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="alert-custom">
                    <i class="fas fa-exclamation-circle"></i>
                    @foreach($errors->all() as $error)
                        {{ $error }}<br>
                    @endforeach
                </div>
            @endif

            {{-- FORM --}}
            <form method="POST" action="{{ route('forgot-password.post') }}" class="login-form">
                @csrf

                <div class="text-center mb-4">
                    <p style="color: rgba(255, 255, 255, 0.4); font-size: 13px;">
                        Masukkan email Anda, kami akan mengirimkan link untuk reset password.
                    </p>
                </div>

                {{-- EMAIL --}}
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-envelope"></i> Email
                    </label>
                    <input type="email" 
                           name="email" 
                           class="form-control @error('email') is-invalid @enderror" 
                           value="{{ old('email') }}" 
                           placeholder="Masukkan email" 
                           required autofocus>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- BUTTON --}}
                <button type="submit" class="btn-login">
                    <i class="fas fa-paper-plane"></i> Kirim Link Reset
                </button>

                {{-- BACK TO LOGIN --}}
                <a href="{{ route('login') }}" class="btn-back">
                    <i class="fas fa-arrow-left"></i> Kembali ke Login
                </a>

            </form>

            {{-- FOOTER --}}
            <div class="login-footer">
                <small>
                    <i class="fas fa-code"></i>
                    Sistem Koperasi CV. ONE FINANCE
                    <span class="version">v1.0</span>
                </small>
            </div>

        </div>

    </div>

    {{-- ============================================ --}}
    {{-- SCRIPTS --}}
    {{-- ============================================ --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js">
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const emailInput = document.querySelector('input[name="email"]');
            if (emailInput) {
                setTimeout(() => emailInput.focus(), 100);
            }
        });
    </script>

</body>
</html>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Koperasi ONE FINANCE</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        * {
            font-family: 'Inter', sans-serif;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f0f8ff;
            margin: 0;
            padding: 20px;
            position: relative;
            overflow: hidden;
        }

        /* Animated Gradient Background */
        body::before {
            content: '';
            position: fixed;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: 
                radial-gradient(ellipse at 20% 50%, rgba(77, 171, 247, 0.15) 0%, transparent 60%),
                radial-gradient(ellipse at 80% 50%, rgba(52, 152, 219, 0.10) 0%, transparent 60%),
                radial-gradient(ellipse at 50% 100%, rgba(77, 171, 247, 0.08) 0%, transparent 50%);
            animation: rotateBackground 30s linear infinite;
            z-index: 0;
        }

        @keyframes rotateBackground {
            0% { transform: rotate(0deg) scale(1); }
            50% { transform: rotate(180deg) scale(1.1); }
            100% { transform: rotate(360deg) scale(1); }
        }

        /* Floating Particles */
        .particle-container {
            position: fixed;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            z-index: 0;
            pointer-events: none;
            overflow: hidden;
        }

        .particle {
            position: absolute;
            border-radius: 50%;
            animation: floatParticle linear infinite;
            opacity: 0.3;
        }

        .particle:nth-child(1) {
            width: 8px;
            height: 8px;
            background: #4dabf7;
            left: 10%;
            animation-duration: 12s;
            animation-delay: 0s;
        }
        .particle:nth-child(2) {
            width: 12px;
            height: 12px;
            background: #3498db;
            left: 25%;
            animation-duration: 15s;
            animation-delay: 2s;
        }
        .particle:nth-child(3) {
            width: 6px;
            height: 6px;
            background: #74b9ff;
            left: 40%;
            animation-duration: 10s;
            animation-delay: 4s;
        }
        .particle:nth-child(4) {
            width: 10px;
            height: 10px;
            background: #4dabf7;
            left: 55%;
            animation-duration: 14s;
            animation-delay: 1s;
        }
        .particle:nth-child(5) {
            width: 7px;
            height: 7px;
            background: #3498db;
            left: 70%;
            animation-duration: 11s;
            animation-delay: 3s;
        }
        .particle:nth-child(6) {
            width: 14px;
            height: 14px;
            background: #74b9ff;
            left: 85%;
            animation-duration: 16s;
            animation-delay: 5s;
        }
        .particle:nth-child(7) {
            width: 9px;
            height: 9px;
            background: #4dabf7;
            left: 15%;
            animation-duration: 13s;
            animation-delay: 6s;
        }
        .particle:nth-child(8) {
            width: 11px;
            height: 11px;
            background: #3498db;
            left: 45%;
            animation-duration: 17s;
            animation-delay: 2s;
        }

        @keyframes floatParticle {
            0% {
                transform: translateY(100vh) rotate(0deg) scale(0);
                opacity: 0;
            }
            10% {
                opacity: 0.3;
            }
            90% {
                opacity: 0.3;
            }
            100% {
                transform: translateY(-10vh) rotate(720deg) scale(1);
                opacity: 0;
            }
        }

        /* Floating bubbles */
        .bubble {
            position: fixed;
            border-radius: 50%;
            pointer-events: none;
            z-index: 0;
            animation: floatBubble 20s ease-in-out infinite;
        }

        .bubble:nth-child(1) {
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, rgba(77, 171, 247, 0.06) 0%, transparent 70%);
            top: -150px;
            right: -150px;
            animation-delay: 0s;
        }
        .bubble:nth-child(2) {
            width: 300px;
            height: 300px;
            background: radial-gradient(circle, rgba(52, 152, 219, 0.05) 0%, transparent 70%);
            bottom: -100px;
            left: -100px;
            animation-delay: -7s;
        }
        .bubble:nth-child(3) {
            width: 200px;
            height: 200px;
            background: radial-gradient(circle, rgba(77, 171, 247, 0.04) 0%, transparent 70%);
            top: 50%;
            right: 10%;
            transform: translateY(-50%);
            animation-delay: -14s;
        }

        @keyframes floatBubble {
            0%, 100% { transform: translate(0, 0) scale(1) rotate(0deg); }
            33% { transform: translate(-30px, -20px) scale(1.1) rotate(5deg); }
            66% { transform: translate(20px, 30px) scale(0.9) rotate(-5deg); }
        }

        /* Container utama - 2 kolom dengan entrance animation */
        .login-container {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 1000px;
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-radius: 30px;
            box-shadow: 0 30px 80px rgba(77, 171, 247, 0.15), 0 10px 30px rgba(0, 0, 0, 0.05);
            overflow: hidden;
            display: flex;
            flex-wrap: wrap;
            border: 1px solid rgba(255, 255, 255, 0.3);
            animation: slideUpFade 0.8s ease-out;
        }

        @keyframes slideUpFade {
            0% {
                opacity: 0;
                transform: translateY(40px) scale(0.95);
            }
            100% {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        /* ============================================ */
        /* KOLOM KIRI - BRANDING (DIPERBAIKI) */
        /* ============================================ */
        .login-brand {
            flex: 1 1 45%;
            padding: 50px 45px;
            background: linear-gradient(160deg, #e3f2fd 0%, #bbdefb 100%);
            display: flex;
            flex-direction: column;
            justify-content: center;
            position: relative;
            overflow: hidden;
            min-height: 500px;
            animation: slideInLeft 0.8s ease-out 0.2s both;
        }

        @keyframes slideInLeft {
            0% {
                opacity: 0;
                transform: translateX(-40px);
            }
            100% {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .login-brand::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 100%;
            height: 100%;
            background: radial-gradient(ellipse, rgba(77, 171, 247, 0.1) 0%, transparent 70%);
            pointer-events: none;
            animation: pulseGlow 4s ease-in-out infinite;
        }

        @keyframes pulseGlow {
            0%, 100% { transform: scale(1); opacity: 0.5; }
            50% { transform: scale(1.2); opacity: 1; }
        }

        .login-brand .brand-content {
            position: relative;
            z-index: 1;
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        /* Logo */
        .login-brand .brand-logo-wrapper {
            display: flex;
            align-items: center;
            gap: 16px;
            margin-bottom: 8px;
        }

        .login-brand .brand-icon {
            width: 70px;
            height: 70px;
            background: linear-gradient(135deg, #4dabf7, #3498db);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 14px;
            box-shadow: 0 10px 35px rgba(77, 171, 247, 0.3);
            animation: logoPulse 3s ease-in-out infinite;
            position: relative;
            flex-shrink: 0;
        }

        @keyframes logoPulse {
            0%, 100% {
                transform: scale(1);
                box-shadow: 0 10px 35px rgba(77, 171, 247, 0.3);
            }
            50% {
                transform: scale(1.05);
                box-shadow: 0 14px 45px rgba(77, 171, 247, 0.5);
            }
        }

        .login-brand .brand-icon::after {
            content: '';
            position: absolute;
            top: -3px;
            left: -3px;
            right: -3px;
            bottom: -3px;
            border-radius: 23px;
            background: linear-gradient(135deg, #4dabf7, #3498db, #4dabf7);
            background-size: 300% 300%;
            z-index: -1;
            animation: gradientBorder 3s ease-in-out infinite;
            opacity: 0.3;
        }

        @keyframes gradientBorder {
            0%, 100% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
        }

        .login-brand .brand-icon img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            filter: brightness(0) invert(1);
            animation: logoFloat 4s ease-in-out infinite;
        }

        @keyframes logoFloat {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-3px); }
        }

        /* Title & Subtitle - Lebih rapi */
        .login-brand .brand-title-group {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .login-brand h1 {
            font-size: 32px;
            font-weight: 900;
            color: #1a237e;
            margin: 0;
            letter-spacing: -1px;
            line-height: 1.1;
            animation: fadeInRight 0.8s ease-out 0.4s both;
        }

        .login-brand h1 span {
            color: #4dabf7;
            position: relative;
            display: inline-block;
        }

        .login-brand h1 span::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 100%;
            height: 3px;
            background: linear-gradient(90deg, #4dabf7, transparent);
            animation: underlineGlow 2s ease-in-out infinite;
        }

        @keyframes underlineGlow {
            0%, 100% { width: 100%; opacity: 0.3; }
            50% { width: 100%; opacity: 1; }
        }

        .login-brand .brand-subtitle {
            font-size: 14px;
            color: #4a6fa5;
            font-weight: 500;
            letter-spacing: 0.5px;
            margin: 0;
            animation: fadeInRight 0.8s ease-out 0.6s both;
        }

        /* Divider */
        .login-brand .brand-divider {
            width: 60px;
            height: 3px;
            background: linear-gradient(90deg, #4dabf7, transparent);
            border-radius: 2px;
            margin: 4px 0 6px;
            animation: fadeInRight 0.8s ease-out 0.7s both;
        }

        /* Features - Lebih rapi dengan grid */
        .login-brand .brand-features {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            margin-top: 6px;
            animation: fadeInRight 0.8s ease-out 0.8s both;
        }

        .login-brand .feature-item {
            display: flex;
            align-items: center;
            gap: 12px;
            color: #1a237e;
            font-size: 13px;
            font-weight: 500;
            padding: 10px 14px;
            background: rgba(255, 255, 255, 0.3);
            border-radius: 12px;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            cursor: default;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .login-brand .feature-item:hover {
            transform: translateY(-2px) scale(1.02);
            background: rgba(255, 255, 255, 0.5);
            box-shadow: 0 4px 15px rgba(77, 171, 247, 0.1);
            border-color: rgba(77, 171, 247, 0.2);
        }

        .login-brand .feature-item i {
            width: 32px;
            height: 32px;
            background: rgba(77, 171, 247, 0.15);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #4dabf7;
            font-size: 14px;
            flex-shrink: 0;
            transition: all 0.3s ease;
        }

        .login-brand .feature-item:hover i {
            transform: rotate(8deg) scale(1.1);
            background: rgba(77, 171, 247, 0.25);
        }

        .login-brand .feature-item span {
            line-height: 1.3;
        }

        /* Quote - Lebih rapi */
        .login-brand .brand-quote {
            padding: 16px 20px;
            background: rgba(255, 255, 255, 0.4);
            border-radius: 14px;
            border-left: 4px solid #4dabf7;
            animation: fadeInRight 0.8s ease-out 1s both;
            transition: all 0.3s ease;
            margin-top: 4px;
        }

        .login-brand .brand-quote:hover {
            transform: translateX(4px);
            background: rgba(255, 255, 255, 0.6);
            box-shadow: 0 8px 25px rgba(77, 171, 247, 0.1);
        }

        .login-brand .brand-quote p {
            margin: 0;
            font-size: 14px;
            color: #1a237e;
            font-weight: 500;
            font-style: italic;
            line-height: 1.5;
        }

        .login-brand .brand-quote small {
            color: #4a6fa5;
            font-size: 12px;
            display: block;
            margin-top: 4px;
            font-style: normal;
            font-weight: 400;
        }

        /* ============================================ */
        /* KOLOM KANAN - FORM LOGIN */
        /* ============================================ */
        .login-form-col {
            flex: 1 1 55%;
            padding: 50px 48px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            background: rgba(255, 255, 255, 0.6);
            min-height: 500px;
            animation: slideInRight 0.8s ease-out 0.3s both;
        }

        @keyframes slideInRight {
            0% {
                opacity: 0;
                transform: translateX(40px);
            }
            100% {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .login-form-col .form-header {
            margin-bottom: 28px;
            animation: fadeInUp 0.6s ease-out 0.5s both;
        }

        @keyframes fadeInUp {
            0% {
                opacity: 0;
                transform: translateY(20px);
            }
            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .login-form-col .form-header h2 {
            font-size: 28px;
            font-weight: 800;
            color: #1a237e;
            margin: 0;
            letter-spacing: -0.5px;
        }

        .login-form-col .form-header p {
            color: #6b8ba8;
            font-size: 14px;
            margin: 4px 0 0;
        }

        .login-form .form-group {
            margin-bottom: 18px;
            animation: fadeInUp 0.6s ease-out 0.7s both;
        }

        .login-form .form-group:nth-child(2) {
            animation-delay: 0.8s;
        }

        .login-form .form-label {
            color: #1a237e;
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 5px;
            display: block;
            transition: all 0.3s ease;
        }

        .login-form .form-label i {
            margin-right: 8px;
            color: #4dabf7;
            transition: all 0.3s ease;
        }

        .login-form .form-group:hover .form-label i {
            transform: scale(1.2);
        }

        .login-form .form-control {
            background: white;
            border: 2px solid #e8f0fe;
            border-radius: 14px;
            padding: 13px 18px;
            color: #1a237e;
            font-size: 14px;
            transition: all 0.3s ease;
            height: 50px;
            font-weight: 500;
        }

        .login-form .form-control::placeholder {
            color: #a0b8d0;
            font-weight: 400;
        }

        .login-form .form-control:focus {
            background: white;
            border-color: #4dabf7;
            box-shadow: 0 0 0 4px rgba(77, 171, 247, 0.08);
            outline: none;
            transform: translateY(-1px);
        }

        .login-form .form-control.is-invalid {
            border-color: #ff6b6b;
            box-shadow: 0 0 0 4px rgba(255, 107, 107, 0.08);
            animation: shake 0.5s ease;
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
            20%, 40%, 60%, 80% { transform: translateX(5px); }
        }

        .login-form .invalid-feedback {
            color: #ff6b6b;
            font-size: 12px;
            margin-top: 4px;
            font-weight: 500;
            animation: fadeInUp 0.3s ease;
        }

        .password-wrapper {
            position: relative;
        }

        .password-wrapper .form-control {
            padding-right: 52px;
        }

        .password-toggle {
            position: absolute;
            right: 16px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #a0b8d0;
            cursor: pointer;
            padding: 8px;
            transition: all 0.3s ease;
            font-size: 18px;
        }

        .password-toggle:hover {
            color: #4dabf7;
            transform: translateY(-50%) scale(1.1);
        }

        .password-toggle:active {
            transform: translateY(-50%) scale(0.9);
        }

        .form-options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 2px;
            margin-bottom: 22px;
            animation: fadeInUp 0.6s ease-out 0.9s both;
        }

        .form-options .remember-me {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #4a6fa5;
            font-size: 13px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .form-options .remember-me:hover {
            color: #1a237e;
        }

        .form-options .remember-me input[type="checkbox"] {
            width: 18px;
            height: 18px;
            accent-color: #4dabf7;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .form-options .remember-me input[type="checkbox"]:hover {
            transform: scale(1.1);
        }

        .form-options .forgot-link a {
            color: #4dabf7;
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
            position: relative;
        }

        .form-options .forgot-link a::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 0;
            height: 2px;
            background: #4dabf7;
            transition: width 0.3s ease;
        }

        .form-options .forgot-link a:hover::after {
            width: 100%;
        }

        .form-options .forgot-link a:hover {
            color: #3498db;
        }

        .btn-login {
            width: 100%;
            height: 52px;
            background: linear-gradient(135deg, #4dabf7, #3498db);
            border: none;
            border-radius: 14px;
            color: white;
            font-weight: 700;
            font-size: 16px;
            transition: all 0.4s ease;
            cursor: pointer;
            position: relative;
            overflow: hidden;
            box-shadow: 0 8px 30px rgba(77, 171, 247, 0.3);
            animation: fadeInUp 0.6s ease-out 1s both;
        }

        .btn-login::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 200%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.15), transparent);
            transition: all 0.6s ease;
        }

        .btn-login:hover::before {
            left: 100%;
        }

        .btn-login:hover {
            transform: translateY(-3px) scale(1.01);
            box-shadow: 0 12px 40px rgba(77, 171, 247, 0.4);
        }

        .btn-login:active {
            transform: translateY(0) scale(0.98);
        }

        .btn-login i {
            margin-right: 10px;
            transition: all 0.3s ease;
        }

        .btn-login:hover i {
            transform: translateX(4px);
        }

        .btn-login.loading {
            pointer-events: none;
            opacity: 0.8;
        }

        .btn-login.loading i {
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .alert-custom {
            background: rgba(255, 107, 107, 0.08);
            border: 1px solid rgba(255, 107, 107, 0.15);
            border-radius: 14px;
            color: #ff6b6b;
            padding: 12px 16px;
            font-size: 13px;
            margin-bottom: 18px;
            display: flex;
            align-items: center;
            gap: 12px;
            font-weight: 500;
            animation: slideInLeft 0.4s ease;
        }

        .alert-custom i {
            font-size: 18px;
        }

        .alert-custom-success {
            background: rgba(46, 204, 113, 0.08);
            border: 1px solid rgba(46, 204, 113, 0.15);
            color: #2ecc71;
        }

        .login-footer {
            text-align: center;
            margin-top: 22px;
            padding-top: 18px;
            border-top: 1px solid #e8f0fe;
            animation: fadeInUp 0.6s ease-out 1.1s both;
        }

        .login-footer small {
            color: #8aa3c0;
            font-size: 12px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .login-footer small:hover {
            color: #4a6fa5;
        }

        .login-footer small i {
            color: #4dabf7;
            animation: heartBeat 2s ease-in-out infinite;
        }

        @keyframes heartBeat {
            0%, 100% { transform: scale(1); }
            14% { transform: scale(1.3); }
            28% { transform: scale(1); }
            42% { transform: scale(1.3); }
            70% { transform: scale(1); }
        }

        .login-footer .footer-links {
            margin-top: 6px;
            display: flex;
            justify-content: center;
            gap: 16px;
            flex-wrap: wrap;
        }

        .login-footer .footer-links a {
            color: #8aa3c0;
            font-size: 12px;
            text-decoration: none;
            transition: all 0.3s ease;
            font-weight: 500;
            position: relative;
        }

        .login-footer .footer-links a::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 0;
            height: 1px;
            background: #4dabf7;
            transition: width 0.3s ease;
        }

        .login-footer .footer-links a:hover::after {
            width: 100%;
        }

        .login-footer .footer-links a:hover {
            color: #4dabf7;
        }

        .login-footer .footer-links .divider {
            color: #dce6f0;
        }

        /* ============================================ */
        /* RESPONSIVE */
        /* ============================================ */
        @media (max-width: 768px) {
            .login-container {
                flex-direction: column;
                max-width: 440px;
                border-radius: 24px;
            }

            .login-brand {
                flex: 1 1 auto;
                padding: 35px 30px;
                min-height: auto;
                border-radius: 24px 24px 0 0;
                animation: slideUpFade 0.8s ease-out 0.2s both;
            }

            .login-brand .brand-features {
                grid-template-columns: 1fr 1fr;
                gap: 10px;
            }

            .login-brand .feature-item {
                padding: 8px 12px;
                font-size: 12px;
            }

            .login-brand .feature-item i {
                width: 28px;
                height: 28px;
                font-size: 12px;
            }

            .login-brand h1 {
                font-size: 26px;
            }

            .login-brand .brand-quote {
                padding: 14px 18px;
            }

            .login-brand .brand-quote p {
                font-size: 13px;
            }

            .login-form-col {
                flex: 1 1 auto;
                padding: 35px 28px;
                min-height: auto;
                border-radius: 0 0 24px 24px;
                animation: slideUpFade 0.8s ease-out 0.3s both;
            }

            .login-form-col .form-header h2 {
                font-size: 24px;
            }

            .form-options {
                flex-direction: column;
                gap: 12px;
                align-items: flex-start;
            }
        }

        @media (max-width: 480px) {
            body {
                padding: 12px;
            }

            .login-brand {
                padding: 28px 20px;
            }

            .login-brand .brand-logo-wrapper {
                gap: 12px;
            }

            .login-brand .brand-icon {
                width: 55px;
                height: 55px;
                padding: 10px;
            }

            .login-brand h1 {
                font-size: 22px;
            }

            .login-brand .brand-subtitle {
                font-size: 12px;
            }

            .login-brand .brand-features {
                grid-template-columns: 1fr;
                gap: 8px;
            }

            .login-brand .feature-item {
                padding: 8px 12px;
                font-size: 12px;
            }

            .login-brand .brand-quote {
                padding: 12px 16px;
            }

            .login-brand .brand-quote p {
                font-size: 12px;
            }

            .login-form-col {
                padding: 28px 18px;
            }

            .login-form .form-control {
                height: 44px;
                padding: 10px 14px;
                font-size: 13px;
            }

            .btn-login {
                height: 46px;
                font-size: 14px;
            }

            .login-form-col .form-header h2 {
                font-size: 22px;
            }
        }
    </style>
</head>
<body>

    <!-- Floating Particles -->
    <div class="particle-container">
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
    </div>

    <!-- Floating Bubbles -->
    <div class="bubble"></div>
    <div class="bubble"></div>
    <div class="bubble"></div>

    <!-- ============================================ -->
    <!-- LOGIN CONTAINER - 2 KOLOM -->
    <!-- ============================================ -->
    <div class="login-container">

        <!-- KOLOM KIRI: BRANDING (DIPERBAIKI) -->
        <div class="login-brand">
            <div class="brand-content">

                <!-- Logo + Title dalam satu baris -->
                <div class="brand-logo-wrapper">
                    <div class="brand-icon">
                        <img src="{{ asset('assets/img/one-finance.png') }}" alt="Logo ONE FINANCE">
                    </div>
                    <div class="brand-title-group">
                        <h1>ONE <span>FINANCE</span></h1>
                        <p class="brand-subtitle">Lembaga Keuangan Mikro</p>
                    </div>
                </div>

                <!-- Divider -->
                <div class="brand-divider"></div>

                <!-- Features - Grid 2 kolom -->
                <div class="brand-features">
                    <div class="feature-item">
                        <i class="fas fa-shield-alt"></i>
                        <span>Keamanan Data</span>
                    </div>
                    <div class="feature-item">
                        <i class="fas fa-rocket"></i>
                        <span>Akses Cepat</span>
                    </div>
                    <div class="feature-item">
                        <i class="fas fa-headset"></i>
                        <span>Dukungan 24/7</span>
                    </div>
                    <div class="feature-item">
                        <i class="fas fa-phone-alt"></i>
                        <span>Layanan Prima</span>
                    </div>
                </div>

                <!-- Quote -->
                <div class="brand-quote">
                    <p>"Bersama ONE FINANCE, kelola keuangan Anda dengan lebih bijak dan modern."</p>
                    <small>— Tim ONE FINANCE</small>
                </div>

            </div>
        </div>

        <!-- KOLOM KANAN: FORM LOGIN -->
        <div class="login-form-col">
            <div class="form-header">
                <h2>Selamat Datang</h2>
                <p>Masuk ke akun Anda untuk melanjutkan</p>
            </div>

            <!-- ALERT -->
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

            <!-- FORM -->
            <form method="POST" action="/login" class="login-form" id="loginForm">
                @csrf

                <!-- EMAIL -->
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-envelope"></i> Email
                    </label>
                    <input type="email" 
                           name="email" 
                           id="email"
                           class="form-control @error('email') is-invalid @enderror" 
                           value="{{ old('email') }}" 
                           placeholder="Masukkan email Anda" 
                           required autofocus>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- PASSWORD -->
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-lock"></i> Password
                    </label>
                    <div class="password-wrapper">
                        <input type="password" 
                               name="password" 
                               id="password"
                               class="form-control @error('password') is-invalid @enderror" 
                               placeholder="Masukkan password Anda" 
                               required>
                        <button type="button" class="password-toggle" id="togglePassword">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- OPTIONS -->
                <div class="form-options">
                    <label class="remember-me">
                        <input type="checkbox" name="remember"> 
                        Ingat saya
                    </label>
                    <div class="forgot-link">
                        <a href="{{ route('forgot-password') }}">
                            <i class="fas fa-question-circle"></i> Lupa Password?
                        </a>
                    </div>
                </div>

                <!-- BUTTON -->
                <button type="submit" class="btn-login" id="loginBtn">
                    <i class="fas fa-sign-in-alt"></i> <span id="btnText">Masuk</span>
                </button>

            </form>

            <!-- FOOTER -->
            <div class="login-footer">
                <small>
                    <i class="fas fa-heart"></i>
                    Sistem Koperasi CV. ONE FINANCE v1.0
                </small>
                
                @auth
                <div class="footer-links">
                    <a href="{{ route('change-password') }}">
                        <i class="fas fa-key"></i> Ganti Sandi
                    </a>
                </div>
                @endauth
            </div>

        </div>

    </div>

    <!-- ============================================ -->
    <!-- SCRIPTS -->
    <!-- ============================================ -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js">
    </script>

    <script>
        // ============================================
        // PASSWORD TOGGLE
        // ============================================
        document.getElementById('togglePassword')?.addEventListener('click', function() {
            const password = document.getElementById('password');
            const icon = this.querySelector('i');

            if (password.type === 'password') {
                password.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                password.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });

        // ============================================
        // AUTO FOCUS
        // ============================================
        document.addEventListener('DOMContentLoaded', function() {
            const emailInput = document.querySelector('input[name="email"]');
            if (emailInput) {
                setTimeout(() => emailInput.focus(), 500);
            }
        });

        // ============================================
        // LOADING STATE ON SUBMIT
        // ============================================
        document.getElementById('loginForm')?.addEventListener('submit', function(e) {
            const btn = document.getElementById('loginBtn');
            const btnText = document.getElementById('btnText');
            
            btn.classList.add('loading');
            btnText.textContent = 'Memproses...';
            
            setTimeout(() => {
                btn.classList.remove('loading');
                btnText.textContent = 'Masuk';
            }, 3000);
        });

        // ============================================
        // KEYBOARD SHORTCUT
        // ============================================
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                const activeElement = document.activeElement;
                if (activeElement && (activeElement.tagName === 'INPUT')) {
                    const form = activeElement.closest('form');
                    if (form) {
                        form.submit();
                    }
                }
            }
        });

        // ============================================
        // INTERAKTIF: Input focus effect
        // ============================================
        document.querySelectorAll('.form-control').forEach(input => {
            input.addEventListener('focus', function() {
                this.closest('.form-group').querySelector('.form-label i').style.color = '#4dabf7';
            });
            
            input.addEventListener('blur', function() {
                this.closest('.form-group').querySelector('.form-label i').style.color = '';
            });
        });
    </script>

</body>
</html>
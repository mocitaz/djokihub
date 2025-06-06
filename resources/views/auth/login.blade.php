<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - DjokiHub</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.2.0/remixicon.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@500;600;700;800;900&display=swap');

        :root {
            --primary-color: #986eec;
            --secondary-color: #7c3aed;
            --primary-gradient: linear-gradient(135deg, #986eec 0%, #7c3aed 100%);
            --glass-bg: rgba(255, 255, 255, 0.9);
            --glass-border: rgba(209, 213, 219, 0.1);
            --bg-gradient: linear-gradient(135deg, #f0f2f5 0%, #ffffff 100%);
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg-gradient);
            overflow-x: hidden;
            min-height: 100vh;
            position: relative;
        }

        .glass-morphism {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: 24px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
        }

        .background-particles {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            overflow: hidden;
        }

        .particle {
            position: absolute;
            background: rgba(152, 110, 236, 0.2);
            border-radius: 50%;
            animation: particleFloat 15s linear infinite;
        }

        .particle:nth-child(1) { width: 20px; height: 20px; left: 10%; top: 15%; animation-delay: 0s; animation-duration: 18s; }
        .particle:nth-child(2) { width: 15px; height: 15px; left: 35%; top: 45%; animation-delay: 2s; animation-duration: 20s; }
        .particle:nth-child(3) { width: 25px; height: 25px; left: 65%; top: 25%; animation-delay: 4s; animation-duration: 16s; }
        .particle:nth-child(4) { width: 18px; height: 18px; left: 80%; top: 65%; animation-delay: 6s; animation-duration: 22s; }

        @keyframes particleFloat {
            0% { transform: translateY(150vh) scale(0.5) rotate(0deg); opacity: 0; }
            20% { opacity: 0.7; }
            80% { opacity: 0.7; }
            100% { transform: translateY(-40vh) scale(1.5) rotate(360deg); opacity: 0; }
        }

        .login-card {
            opacity: 0;
            transform: translateY(60px) rotateY(20deg) scale(0.9);
            animation: cardEntrance 1.5s cubic-bezier(0.4, 0, 0.2, 1) forwards;
        }

        @keyframes cardEntrance {
            0% { opacity: 0; transform: translateY(60px) rotateY(20deg) scale(0.9); }
            40% { opacity: 0.6; transform: translateY(-20px) rotateY(-15deg) scale(1.1); }
            70% { opacity: 0.9; transform: translateY(10px) rotateY(5deg) scale(0.95); }
            100% { opacity: 1; transform: translateY(0) rotateY(0) scale(1); }
        }

        .form-input {
            transition: all 0.6s cubic-bezier(0.4, 0, 0.2, 1);
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 16px;
            padding: 16px 52px 16px 48px;
        }

        .form-input:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 6px rgba(152, 110, 236, 0.25), 0 0 20px rgba(152, 110, 236, 0.5);
            transform: scale(1.05) translateY(-3px);
            animation: inputRipple 0.7s ease;
        }

        .input-wrapper {
            position: relative;
        }

        .input-wrapper .input-icon {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
            transition: all 0.4s ease;
        }

        .input-wrapper:focus-within .input-icon {
            color: var(--primary-color);
            transform: translateY(-50%) rotate(360deg) scale(1.3);
            animation: iconSpin 0.6s ease;
        }

        .input-wrapper::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            width: 0;
            height: 4px;
            background: var(--primary-gradient);
            transform: translateX(-50%);
            transition: width 0.6s ease;
        }

        .input-wrapper:focus-within::after {
            width: 98%;
        }

        @keyframes inputRipple {
            0% { box-shadow: 0 0 0 0 rgba(152, 110, 236, 0.2); }
            50% { box-shadow: 0 0 0 12px rgba(152, 110, 236, 0.15); }
            100% { box-shadow: 0 0 0 6px rgba(152, 110, 236, 0.25); }
        }

        @keyframes iconSpin {
            0% { transform: translateY(-50%) rotate(0deg) scale(1); }
            100% { transform: translateY(-50%) rotate(360deg) scale(1.3); }
        }

        .btn-primary {
            background: var(--primary-gradient);
            transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }

        .btn-primary:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 50px rgba(152, 110, 236, 0.5);
            animation: buttonPulse 1.2s infinite;
        }

        @keyframes buttonPulse {
            0% { box-shadow: 0 20px 50px rgba(152, 110, 236, 0.5); }
            50% { box-shadow: 0 25px 60px rgba(152, 110, 236, 0.7); }
            100% { box-shadow: 0 20px 50px rgba(152, 110, 236, 0.5); }
        }

        .btn-primary::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            background: rgba(255, 255, 255, 0.7);
            border-radius: 50%;
            transform: translate(-50%, -50%);
            transition: width 0.8s ease, height 0.8s ease;
        }

        .btn-primary:active::after {
            width: 400px;
            height: 400px;
            transition: none;
        }

        .btn-primary:active .particle-burst {
            animation: particleBurst 0.8s ease;
        }

        .particle-burst {
            position: absolute;
            width: 10px;
            height: 10px;
            background: var(--primary-color);
            border-radius: 50%;
            opacity: 0;
        }

        @keyframes particleBurst {
            0% { transform: translate(-50%, -50%) scale(0); opacity: 0.9; }
            100% { transform: translate(-50%, -50%) scale(5); opacity: 0; }
        }

        .logo-animation {
            position: relative;
            animation: logoGlow 3s ease-in-out infinite;
        }

        .logo-animation::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 140%;
            height: 140%;
            border: 2px solid rgba(152, 110, 236, 0.5);
            border-radius: 50%;
            transform: translate(-50%, -50%) rotate(0deg);
            animation: logoOrbitInner 4s linear infinite;
            z-index: -1;
        }

        .logo-animation::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 160%;
            height: 160%;
            border: 1px dashed rgba(152, 110, 236, 0.4);
            border-radius: 50%;
            transform: translate(-50%, -50%) rotate(0deg);
            animation: logoOrbitOuter 6s linear infinite reverse;
            z-index: -2;
        }

        @keyframes logoGlow {
            0% { transform: scale(1); text-shadow: 0 0 12px rgba(152, 110, 236, 0.5); }
            50% { transform: scale(1.08); text-shadow: 0 0 30px rgba(152, 110, 236, 0.7), 0 0 40px rgba(124, 58, 237, 0.5); }
            100% { transform: scale(1); text-shadow: 0 0 12px rgba(152, 110, 236, 0.5); }
        }

        @keyframes logoOrbitInner {
            0% { transform: translate(-50%, -50%) rotate(0deg) scale(1); }
            100% { transform: translate(-50%, -50%) rotate(360deg) scale(1.15); }
        }

        @keyframes logoOrbitOuter {
            0% { transform: translate(-50%, -50%) rotate(0deg) scale(1); }
            100% { transform: translate(-50%, -50%) rotate(-360deg) scale(1.25); }
        }

        .stagger-animation {
            opacity: 0;
            transform: translateX(-30px) scale(0.9);
            animation: slideIn 1s cubic-bezier(0.4, 0, 0.2, 1) forwards;
        }

        .stagger-animation:nth-child(1) { animation-delay: 0.4s; }
        .stagger-animation:nth-child(2) { animation-delay: 0.6s; }
        .stagger-animation:nth-child(3) { animation-delay: 0.8s; }
        .stagger-animation:nth-child(4) { animation-delay: 1.0s; }

        @keyframes slideIn {
            to { opacity: 1; transform: translateX(0) scale(1); }
        }

        .checkbox-custom {
            appearance: none;
            width: 16px;
            height: 16px;
            border: 2px solid #d1d5db;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
        }

        .checkbox-custom:checked {
            background: var(--primary-color);
            border-color: transparent;
        }

        .checkbox-custom:checked::after {
            content: '✓';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) scale(0);
            color: white;
            font-size: 10px;
            font-weight: bold;
            animation: checkPop 0.4s ease forwards;
        }

        @keyframes checkPop {
            0% { transform: translate(-50%, -50%) scale(0); }
            50% { transform: translate(-50%, -50%) scale(1.4); }
            100% { transform: translate(-50%, -50%) scale(1); }
        }

        .error-shake {
            animation: shake 0.8s cubic-bezier(0.36, 0.07, 0.19, 0.97);
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            20% { transform: translateX(-12px); }
            40% { transform: translateX(12px); }
            60% { transform: translateX(-6px); }
            80% { transform: translateX(6px); }
        }

        .success-bounce {
            animation: bounce 0.8s ease;
        }

        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-15px); }
        }

        .running-text {
            display: inline-block;
            color: #000;
            font-weight: bold;
            animation: textScroll 10s linear infinite, text3DRotate 4s ease-in-out infinite, textFadeShift 6s ease-in-out infinite;
        }

        @keyframes textScroll {
            0% { transform: translateX(100%) rotateY(0deg); }
            100% { transform: translateX(-100%) rotateY(0deg); }
        }

        @keyframes text3DRotate {
            0% { text-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); transform: perspective(800px) translateZ(0) rotateX(0deg); }
            50% { text-shadow: 0 8px 16px rgba(0, 0, 0, 0.3); transform: perspective(800px) translateZ(15px) rotateX(20deg); }
            100% { text-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); transform: perspective(800px) translateZ(0) rotateX(0deg); }
        }

        @keyframes textFadeShift {
            0% { color: #000; opacity: 0.8; }
            50% { color: #333; opacity: 1; }
            100% { color: #000; opacity: 0.8; }
        }

        .label-hover {
            transition: all 0.4s ease;
        }

        .label-hover:hover {
            color: var(--primary-color);
            transform: scale(1.08);
        }

        @media (max-width: 640px) {
            .login-card {
                margin: 1rem;
                padding: 8;
            }
            .particle { display: none; }
        }
    </style>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        inter: ['Inter', 'sans-serif'],
                        poppins: ['Poppins', 'sans-serif'],
                    },
                    colors: {
                        primary: '#986eec',
                    }
                }
            }
        }
    </script>
</head>
<body class="min-h-screen flex flex-col items-center justify-center p-6 bg-white relative">
    <!-- Background Particles -->
    <div class="background-particles">
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
    </div>

    <!-- Login Card -->
    <div class="login-card w-full max-w-md glass-morphism p-12">
        <!-- Header -->
        <div class="text-center mb-12">
            <h1 class="font-poppins text-6xl font-extrabold text-gray-900 logo-animation">DjokiHub</h1>
            <p class="text-gray-600 text-lg mt-4 font-medium">Sign in to access your account</p>
        </div>

        <!-- Form -->
        <div class="space-y-12">
            @if (session('status') && (!session('status_type') || session('status_type') != 'error'))
                <div class="success-bounce p-4 bg-green-100 border border-green-300 text-green-700 rounded-xl text-base">
                    <i class="ri-check-circle-line mr-2"></i>{{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" id="loginForm">
                @csrf

                <!-- Email Field -->
                <div class="stagger-animation space-y-4">
                    <label for="email" class="block text-base font-semibold text-gray-700 label-hover">Email</label>
                    <div class="input-wrapper relative">
                        <i class="ri-mail-line input-icon text-2xl"></i>
                        <input id="email"
                               class="form-input w-full text-gray-700 placeholder-gray-400 focus:outline-none text-base"
                               type="email"
                               name="email"
                               value="{{ old('email') }}"
                               required
                               autofocus
                               autocomplete="email"
                               placeholder="Enter your email">
                    </div>
                    @error('email')
                        <p class="text-red-500 text-sm mt-2 flex items-center">
                            <i class="ri-error-warning-line mr-1"></i>{{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Password Field -->
                <div class="stagger-animation space-y-4">
                    <label for="password" class="block text-base font-semibold text-gray-700 label-hover">Password</label>
                    <div class="input-wrapper relative">
                        <i class="ri-lock-2-line input-icon text-2xl"></i>
                        <input id="password"
                               class="form-input w-full text-gray-700 placeholder-gray-400 focus:outline-none text-base"
                               type="password"
                               name="password"
                               required
                               autocomplete="current-password"
                               placeholder="Enter your password">
                        <button type="button" id="togglePassword" class="absolute right-5 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-primary">
                            <i class="ri-eye-off-line text-xl input-icon"></i>
                        </button>
                    </div>
                    @error('password')
                        <p class="text-red-500 text-sm mt-2 flex items-center">
                            <i class="ri-error-warning-line mr-1"></i>{{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Remember Me -->
                <div class="stagger-animation flex items-center text-xs mt-6">
                    <label class="flex items-center space-x-2">
                        <input type="checkbox" name="remember" class="checkbox-custom">
                        <span class="text-gray-600 label-hover font-medium">Remember me</span>
                    </label>
                </div>

                <!-- Login Button -->
                <div class="stagger-animation relative mt-8">
                    <button type="submit" class="btn-primary w-full py-5 rounded-2xl text-white text-lg font-bold flex items-center justify-center space-x-4">
                        <i class="ri-login-circle-line text-2xl"></i>
                        <span>Sign In</span>
                        <div class="particle-burst"></div>
                        <div class="particle-burst" style="left: 20%; top: 20%;"></div>
                        <div class="particle-burst" style="left: 80%; top: 80%;"></div>
                    </button>
                </div>

                @if (session('status') && session('status_type') == 'error')
                    <div class="error-shake p-4 bg-red-100 border border-red-300 text-red-700 rounded-xl text-base text-center">
                        <i class="ri-error-warning-line mr-2"></i>{{ session('status') }}
                    </div>
                @endif
            </form>
        </div>

        <!-- Running Text -->
        <div class="mt-6 text-center overflow-hidden">
            <div class="running-text text-sm font-bold text-black">🚀 For account access, please contact administrator</div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="text-gray-500 text-base mt-12 text-center space-y-2">
        <p class="font-medium">© 2025 DjokiHub. All rights reserved.</p>
        <p class="font-medium">Built by the <span class="group hover:text-primary transition-colors font-semibold">Djoki Developer team</span></p>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Password visibility toggle
            const togglePassword = document.getElementById('togglePassword');
            const passwordInput = document.getElementById('password');

            if (togglePassword && passwordInput) {
                togglePassword.addEventListener('click', function() {
                    const icon = this.querySelector('i');
                    passwordInput.type = passwordInput.type === 'password' ? 'text' : 'password';
                    icon.className = passwordInput.type === 'password' ? 'ri-eye-off-line text-xl input-icon' : 'ri-eye-line text-xl input-icon';
                });
            }

            // Form validation feedback
            const form = document.getElementById('loginForm');
            const inputs = form.querySelectorAll('input[required]');

            inputs.forEach(input => {
                input.addEventListener('blur', function() {
                    if (!this.value.trim()) {
                        this.parentElement.classList.add('error-shake');
                        setTimeout(() => this.parentElement.classList.remove('error-shake'), 800);
                    }
                });

                input.addEventListener('input', function() {
                    if (this.value.trim()) {
                        this.parentElement.classList.add('success-bounce');
                        setTimeout(() => this.parentElement.classList.remove('success-bounce'), 800);
                    }
                });
            });

            // Button loading state
            form.addEventListener('submit', function() {
                const button = this.querySelector('button[type="submit"]');
                const buttonText = button.querySelector('span');
                const buttonIcon = button.querySelector('i');
                buttonIcon.className = 'ri-loader-4-line animate-spin text-2xl';
                buttonText.textContent = 'Signing In...';
                button.disabled = true;
            });
        });
    </script>
</body>
</html>
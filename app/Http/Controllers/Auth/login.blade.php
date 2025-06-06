{{-- Simpan sebagai resources/views/auth/login.blade.php atau path yang sesuai --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login & Register - DjokiHub</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.2.0/remixicon.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Pacifico&display=swap');
        .font-pacifico {
            font-family: 'Pacifico', cursive;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fadeIn {
            animation: fadeIn 0.5s ease-out forwards;
        }
        .password-strength-meter {
            height: 6px;
            background-color: #e5e7eb; /* gray-200 */
            border-radius: 3px;
            overflow: hidden;
        }
        .password-strength-bar {
            height: 100%;
            width: 0%;
            border-radius: 3px;
            transition: width 0.3s ease, background-color 0.3s ease;
        }
        .strength-weak { background-color: #ef4444; /* red-500 */ }
        .strength-medium { background-color: #f59e0b; /* amber-500 */ }
        .strength-strong { background-color: #22c55e; /* green-500 */ }

        /* Basic styling for dark mode if you implement the toggle */
        .dark-theme {
            /* Example dark theme styles - you'll need to expand this */
            background-color: #1f2937; /* gray-800 */
            color: #f3f4f6; /* gray-100 */
        }
        .dark-theme .bg-white {
            background-color: #374151; /* gray-700 */
        }
        .dark-theme .text-gray-700, .dark-theme .text-gray-600, .dark-theme .text-gray-500 {
            color: #d1d5db; /* gray-300 */
        }
         .dark-theme .text-gray-900 {
            color: #f9fafb; /* gray-50 */
        }
        .dark-theme .border-gray-200 {
            border-color: #4b5563; /* gray-600 */
        }
        .dark-theme .border-gray-300 {
            border-color: #4b5563; /* gray-600 */
        }
         .dark-theme input::placeholder {
            color: #9ca3af; /* gray-400 */
        }
        .dark-theme .bg-gray-50 {
            background-color: #4b5563; /* gray-600 */
        }
    </style>
    <script>
        tailwind.config = {
            darkMode: 'class', // Enable class-based dark mode
            theme: {
                extend: {
                    colors: {
                        primary: '#6366F1', // Indigo-500
                        secondary: '#10B981', // Emerald-500
                        accent: '#EC4899', // Pink-500
                    },
                    borderRadius: {
                        'button': '0.375rem', // rounded-md
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gradient-to-br from-primary via-purple-500 to-accent min-h-screen flex flex-col items-center justify-center p-4 selection:bg-primary selection:text-white">

    {{-- Optional Theme Toggle --}}
    <div class="absolute top-4 right-4 flex items-center space-x-2 z-50">
        <span class="text-white text-sm font-medium">Light</span>
        <div class="relative inline-block w-10 align-middle select-none transition duration-200 ease-in">
            <input type="checkbox" id="theme-toggle" class="toggle-checkbox absolute block w-5 h-5 rounded-full bg-white border-2 appearance-none cursor-pointer transition-all duration-300" style="top: 0.125rem; left: 0.125rem"/>
            <label for="theme-toggle" class="toggle-label block overflow-hidden h-5 rounded-full bg-gray-300 cursor-pointer transition-all duration-300"></label>
        </div>
        <span class="text-white text-sm font-medium">Dark</span>
    </div>


    <div class="w-full max-w-md bg-white rounded-xl shadow-2xl overflow-hidden animate-fadeIn">
        <div class="p-6 sm:p-8 text-center border-b border-gray-200">
            <h1 class="text-4xl font-pacifico text-primary">DjokiHub</h1>
            <p class="text-sm text-gray-500 mt-1">Welcome! Please login or register.</p>
        </div>
        
        <div class="flex">
            <button id="login-tab-button" class="w-1/2 py-3 font-medium text-center text-primary border-b-2 border-primary focus:outline-none transition-colors duration-150">
                Login
            </button>
            <button id="register-tab-button" class="w-1/2 py-3 font-medium text-center text-gray-500 hover:text-gray-700 focus:outline-none border-b-2 border-transparent hover:border-gray-300 transition-colors duration-150">
                Register
            </button>
        </div>
        
        {{-- Login Form --}}
        <div id="login-form-container" class="p-6 sm:p-8">
            {{-- Jika menggunakan Laravel, form action akan ke route('login') --}}
            <form id="login-form-actual" method="POST" action="{{ route('login') }}">
                @csrf
                <div class="mb-4">
                    <label for="login-email" class="block text-sm font-medium text-gray-700 mb-1">Email atau Username</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <i class="ri-user-3-line text-gray-400"></i>
                        </div>
                        <input type="text" id="login-email" name="email" {{-- 'email' atau 'username' tergantung setup Laravel Anda --}}
                               class="w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-button focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent placeholder-gray-400" 
                               placeholder="your@email.com" required value="{{ old('email') }}">
                    </div>
                    @error('email') {{-- Contoh display error Laravel --}}
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="mb-4">
                    <label for="login-password-input" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <i class="ri-lock-password-line text-gray-400"></i>
                        </div>
                        <input type="password" id="login-password-input" name="password" 
                               class="w-full pl-10 pr-10 py-2.5 border border-gray-300 rounded-button focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent placeholder-gray-400" 
                               placeholder="Enter your password" required>
                        <button type="button" class="absolute inset-y-0 right-0 flex items-center pr-3 focus:outline-none password-toggle-button">
                            <i class="ri-eye-off-line text-gray-400"></i>
                        </button>
                    </div>
                     @error('password') {{-- Contoh display error Laravel --}}
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center">
                        <input type="checkbox" id="login-remember-me" name="remember" class="w-4 h-4 text-primary border-gray-300 rounded focus:ring-primary">
                        <label for="login-remember-me" class="ml-2 text-sm text-gray-600">Remember me</label>
                    </div>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="text-sm text-primary hover:underline">
                            Forgot password?
                        </a>
                    @endif
                </div>
                
                <button type="submit" class="w-full bg-primary text-white py-2.5 px-4 rounded-button hover:bg-opacity-90 transition duration-150 flex items-center justify-center">
                    <span>Login</span>
                    {{-- Spinner bisa ditambahkan di sini jika ada proses login async --}}
                </button>
            </form>
        </div>
        
        {{-- Register Form --}}
        <div id="register-form-container" class="p-6 sm:p-8 hidden">
            {{-- Jika menggunakan Laravel, form action akan ke route('register') --}}
            <form id="register-form-actual" method="POST" action="{{ route('register') }}">
                @csrf
                <div class="mb-4">
                    <label for="register-name" class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <i class="ri-user-line text-gray-400"></i>
                        </div>
                        <input type="text" id="register-name" name="name" 
                               class="w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-button focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent placeholder-gray-400" 
                               placeholder="Enter your full name" required value="{{ old('name') }}">
                    </div>
                     @error('name')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="mb-4">
                    <label for="register-email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <i class="ri-mail-line text-gray-400"></i>
                        </div>
                        <input type="email" id="register-email" name="email" 
                               class="w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-button focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent placeholder-gray-400" 
                               placeholder="Enter your email" required value="{{ old('email') }}">
                    </div>
                     @error('email')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="mb-4">
                    <label for="register-password-input" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <i class="ri-lock-line text-gray-400"></i>
                        </div>
                        <input type="password" id="register-password-input" name="password" 
                               class="w-full pl-10 pr-10 py-2.5 border border-gray-300 rounded-button focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent placeholder-gray-400" 
                               placeholder="Create a password" required>
                        <button type="button" class="absolute inset-y-0 right-0 flex items-center pr-3 focus:outline-none password-toggle-button">
                            <i class="ri-eye-off-line text-gray-400"></i>
                        </button>
                    </div>
                    <div class="mt-1 w-full password-strength-meter">
                        <div id="password-strength-bar" class="password-strength-bar"></div>
                    </div>
                    <p id="password-feedback" class="mt-1 text-xs text-gray-500">Password must be at least 8 characters.</p>
                     @error('password')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-6">
                    <label for="register-password-confirmation" class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
                    <div class="relative">
                         <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <i class="ri-lock-password-line text-gray-400"></i>
                        </div>
                        <input type="password" id="register-password-confirmation" name="password_confirmation" 
                               class="w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-button focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent placeholder-gray-400" 
                               placeholder="Confirm your password" required>
                    </div>
                </div>
                
                <button type="submit" class="w-full bg-primary text-white py-2.5 px-4 rounded-button hover:bg-opacity-90 transition duration-150 flex items-center justify-center">
                    <span>Register</span>
                    {{-- Spinner bisa ditambahkan di sini jika ada proses registrasi async --}}
                </button>
            </form>
        </div>
    </div>

    {{-- Toast Notification (Contoh) --}}
    <div id="toast-notification" class="fixed top-5 right-5 bg-green-500 text-white px-6 py-3 rounded-lg shadow-md hidden animate-fadeIn z-50">
        <span id="toast-message"></span>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const loginTabButton = document.getElementById('login-tab-button');
            const registerTabButton = document.getElementById('register-tab-button');
            const loginFormContainer = document.getElementById('login-form-container');
            const registerFormContainer = document.getElementById('register-form-container');

            const passwordToggleButtons = document.querySelectorAll('.password-toggle-button');

            const registerPasswordInput = document.getElementById('register-password-input');
            const strengthBar = document.getElementById('password-strength-bar');
            const passwordFeedback = document.getElementById('password-feedback');

            const themeToggle = document.getElementById('theme-toggle');

            // Theme Toggle
            if (themeToggle) {
                // Apply saved theme or system preference
                if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                    document.documentElement.classList.add('dark');
                    themeToggle.checked = true;
                } else {
                    document.documentElement.classList.remove('dark');
                    themeToggle.checked = false;
                }

                themeToggle.addEventListener('change', function() {
                    if (this.checked) {
                        document.documentElement.classList.add('dark');
                        localStorage.theme = 'dark';
                    } else {
                        document.documentElement.classList.remove('dark');
                        localStorage.theme = 'light';
                    }
                });
            }


            // Tab Switching
            if (loginTabButton && registerTabButton && loginFormContainer && registerFormContainer) {
                loginTabButton.addEventListener('click', function () {
                    loginFormContainer.classList.remove('hidden');
                    registerFormContainer.classList.add('hidden');
                    loginTabButton.classList.add('text-primary', 'border-primary');
                    loginTabButton.classList.remove('text-gray-500', 'border-transparent', 'hover:border-gray-300');
                    registerTabButton.classList.add('text-gray-500', 'border-transparent', 'hover:border-gray-300');
                    registerTabButton.classList.remove('text-primary', 'border-primary');
                });

                registerTabButton.addEventListener('click', function () {
                    registerFormContainer.classList.remove('hidden');
                    loginFormContainer.classList.add('hidden');
                    registerTabButton.classList.add('text-primary', 'border-primary');
                    registerTabButton.classList.remove('text-gray-500', 'border-transparent', 'hover:border-gray-300');
                    loginTabButton.classList.add('text-gray-500', 'border-transparent', 'hover:border-gray-300');
                    loginTabButton.classList.remove('text-primary', 'border-primary');
                });
            }

            // Password Visibility Toggle
            passwordToggleButtons.forEach(button => {
                button.addEventListener('click', function () {
                    const passwordInput = this.previousElementSibling; // Assumes input is direct sibling before button
                    const icon = this.querySelector('i');
                    if (passwordInput.type === 'password') {
                        passwordInput.type = 'text';
                        icon.classList.remove('ri-eye-off-line');
                        icon.classList.add('ri-eye-line');
                    } else {
                        passwordInput.type = 'password';
                        icon.classList.remove('ri-eye-line');
                        icon.classList.add('ri-eye-off-line');
                    }
                });
            });

            // Password Strength Meter
            if (registerPasswordInput && strengthBar && passwordFeedback) {
                registerPasswordInput.addEventListener('input', function () {
                    const password = this.value;
                    let strength = 0;
                    let feedbackText = "Password must be at least 8 characters.";
                    let barClass = 'strength-weak';

                    if (password.length >= 8) strength += 25;
                    if (password.match(/[A-Z]/)) strength += 25;
                    if (password.match(/[0-9]/)) strength += 25;
                    if (password.match(/[^A-Za-z0-9]/)) strength += 25;
                    
                    strengthBar.style.width = Math.min(strength, 100) + '%';

                    if (strength <= 25 && password.length > 0) {
                        feedbackText = "Weak";
                        barClass = 'strength-weak';
                    } else if (strength <= 50 && password.length > 0) {
                        feedbackText = "Medium";
                        barClass = 'strength-medium';
                    } else if (strength > 50 && password.length > 0) {
                        feedbackText = "Strong";
                        barClass = 'strength-strong';
                    } else if (password.length === 0) {
                         feedbackText = "Password must be at least 8 characters.";
                         strengthBar.style.width = '0%';
                    }
                    
                    strengthBar.className = 'password-strength-bar ' + barClass;
                    passwordFeedback.textContent = feedbackText;
                });
            }

            // Example Toast Notification (jika diperlukan setelah submit form dari backend)
            // window.showToast = function(message, type = 'success') {
            //     const toast = document.getElementById('toast-notification');
            //     const toastMessageEl = document.getElementById('toast-message');
            //     if (toast && toastMessageEl) {
            //         toastMessageEl.textContent = message;
            //         toast.classList.remove('bg-green-500', 'bg-red-500'); // Remove previous color classes
            //         if (type === 'success') {
            //             toast.classList.add('bg-green-500');
            //         } else if (type === 'error') {
            //             toast.classList.add('bg-red-500');
            //         }
            //         toast.classList.remove('hidden');
            //         setTimeout(() => {
            //             toast.classList.add('hidden');
            //         }, 3000);
            //     }
            // }
            // Contoh penggunaan:
            // if (session()->has('success')) showToast("{{ session('success') }}", 'success');
            // if (session()->has('error')) showToast("{{ session('error') }}", 'error');

        });
    </script>
</body>
</html>

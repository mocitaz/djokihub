<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'Laravel')) - DjokiHub</title>

    <!-- Preload Critical Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700,800|space-grotesk:400,500,600,700,800" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.2.0/remixicon.min.css" rel="stylesheet">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'ui-sans-serif', 'system-ui', '-apple-system', 'BlinkMacSystemFont', 'Segoe UI', 'Roboto', 'Helvetica Neue', 'Arial', 'sans-serif'],
                        display: ['Space Grotesk', 'Inter', 'sans-serif'],
                        mono: ['JetBrains Mono', 'SF Mono', 'Monaco', 'Inconsolata', 'Roboto Mono', 'monospace'],
                    },
                    colors: {
                        primary: {
                            DEFAULT: '#6366F1',
                            50: '#F0F2FF', 100: '#E0E7FF', 200: '#C7D2FE', 
                            300: '#A5B4FC', 400: '#818CF8', 500: '#6366F1',
                            600: '#4F46E5', 700: '#4338CA', 800: '#3730A3',
                            900: '#312E81', 950: '#1E1B4B'
                        },
                        neutral: {
                            50: '#FAFAFA', 100: '#F5F5F5', 200: '#E5E5E5',
                            300: '#D4D4D4', 400: '#A3A3A3', 500: '#737373',
                            600: '#525252', 700: '#404040', 800: '#262626',
                            900: '#171717', 950: '#0A0A0A'
                        },
                        slate: {
                            50: '#F8FAFC', 100: '#F1F5F9', 200: '#E2E8F0',
                            300: '#CBD5E1', 400: '#94A3B8', 500: '#64748B',
                            600: '#475569', 700: '#334155', 800: '#1E293B',
                            900: '#0F172A', 950: '#020617'
                        },
                        success: {
                            DEFAULT: '#10B981', 50: '#ECFDF5', 100: '#D1FAE5', 
                            500: '#10B981', 600: '#059669', 700: '#047857'
                        },
                        warning: {
                            DEFAULT: '#F59E0B', 50: '#FFFBEB', 100: '#FEF3C7',
                            500: '#F59E0B', 600: '#D97706', 700: '#B45309'
                        },
                        error: {
                            DEFAULT: '#EF4444', 50: '#FEF2F2', 100: '#FEE2E2',
                            500: '#EF4444', 600: '#DC2626', 700: '#B91C1C'
                        }
                    },
                    fontSize: {
                        'xs': ['0.75rem', { lineHeight: '1rem' }],
                        'sm': ['0.875rem', { lineHeight: '1.25rem' }],
                        'base': ['1rem', { lineHeight: '1.5rem' }],
                        'lg': ['1.125rem', { lineHeight: '1.75rem' }],
                        'xl': ['1.25rem', { lineHeight: '1.75rem' }],
                        '2xl': ['1.5rem', { lineHeight: '2rem' }],
                        '3xl': ['1.875rem', { lineHeight: '2.25rem' }],
                        '4xl': ['2.25rem', { lineHeight: '2.5rem' }],
                    },
                    spacing: {
                        '18': '4.5rem',
                        '88': '22rem',
                    },
                    borderRadius: {
                        'xl': '0.75rem',
                        '2xl': '1rem',
                        '3xl': '1.5rem',
                    },
                    boxShadow: {
                        'soft': '0 2px 15px -3px rgba(0, 0, 0, 0.07), 0 10px 20px -2px rgba(0, 0, 0, 0.04)',
                        'medium': '0 4px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04)',
                        'large': '0 25px 50px -12px rgba(0, 0, 0, 0.25)',
                        'glow': '0 0 0 1px rgba(99, 102, 241, 0.05), 0 1px 2px rgba(0, 0, 0, 0.04), 0 0 0 2px rgba(99, 102, 241, 0.05)',
                        'glow-lg': '0 0 0 1px rgba(99, 102, 241, 0.06), 0 2px 4px rgba(0, 0, 0, 0.06), 0 0 0 3px rgba(99, 102, 241, 0.05)',
                    },
                    backdropBlur: {
                        'xs': '2px',
                    },
                    animation: {
                        'fade-in': 'fadeIn 0.6s ease-out',
                        'fade-in-up': 'fadeInUp 0.6s ease-out',
                        'slide-down': 'slideDown 0.4s ease-out',
                        'slide-up': 'slideUp 0.4s ease-out',
                        'scale-in': 'scaleIn 0.3s ease-out',
                        'pulse-soft': 'pulseSoft 3s ease-in-out infinite',
                        'float': 'float 6s ease-in-out infinite',
                        'gradient': 'gradient 8s ease-in-out infinite',
                    },
                    keyframes: {
                        fadeIn: {
                            '0%': { opacity: '0' },
                            '100%': { opacity: '1' },
                        },
                        fadeInUp: {
                            '0%': { opacity: '0', transform: 'translateY(20px)' },
                            '100%': { opacity: '1', transform: 'translateY(0)' },
                        },
                        slideDown: {
                            '0%': { opacity: '0', transform: 'translateY(-10px)' },
                            '100%': { opacity: '1', transform: 'translateY(0)' },
                        },
                        slideUp: {
                            '0%': { opacity: '0', transform: 'translateY(10px)' },
                            '100%': { opacity: '1', transform: 'translateY(0)' },
                        },
                        scaleIn: {
                            '0%': { opacity: '0', transform: 'scale(0.95)' },
                            '100%': { opacity: '1', transform: 'scale(1)' },
                        },
                        pulseSoft: {
                            '0%, 100%': { opacity: '1' },
                            '50%': { opacity: '0.8' },
                        },
                        float: {
                            '0%, 100%': { transform: 'translateY(0)' },
                            '50%': { transform: 'translateY(-5px)' },
                        },
                        gradient: {
                            '0%, 100%': { backgroundPosition: '0% 50%' },
                            '50%': { backgroundPosition: '100% 50%' },
                        }
                    }
                }
            }
        }
    </script>

    @stack('styles')

    <style>
        /* Base Typography */
        html {
            scroll-behavior: smooth;
            font-feature-settings: "cv02", "cv03", "cv04", "cv11";
        }

        body {
            font-family: 'Inter', sans-serif;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            text-rendering: optimizeLegibility;
        }

        /* Display Typography */
        .font-display {
            font-family: 'Space Grotesk', 'Inter', sans-serif;
            font-feature-settings: "ss01", "ss02";
            letter-spacing: -0.025em;
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        ::-webkit-scrollbar-track {
            background: transparent;
        }
        ::-webkit-scrollbar-thumb {
            background: linear-gradient(180deg, rgb(99 102 241 / 0.3), rgb(99 102 241 / 0.6));
            border-radius: 3px;
            border: 1px solid transparent;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(180deg, rgb(99 102 241 / 0.5), rgb(99 102 241 / 0.8));
        }

        /* Alpine.js cloak */
        [x-cloak] { 
            display: none !important; 
        }

        /* Glassmorphism Header */
        .glass-header {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(226, 232, 240, 0.8);
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* Logo Styling */
        .logo-text {
            font-family: 'Space Grotesk', sans-serif;
            font-weight: 800;
            font-size: 1.75rem;
            line-height: 1.2;
            background: linear-gradient(135deg, #6366F1 0%, #8B5CF6 50%, #06B6D4 100%);
            background-size: 200% 200%;
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
            animation: gradient 8s ease-in-out infinite;
            letter-spacing: -0.02em;
        }

        /* Navigation Links */
        .nav-link {
            position: relative;
            padding: 0.5rem 1rem;
            font-size: 0.875rem;
            font-weight: 500;
            color: rgb(71 85 105);
            border-radius: 0.5rem;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            text-decoration: none;
        }

        .nav-link:hover {
            color: rgb(99 102 241);
            background-color: rgba(99, 102, 241, 0.05);
            transform: translateY(-1px);
        }

        .nav-link.active {
            color: rgb(99 102 241);
            background-color: rgba(99, 102, 241, 0.1);
            font-weight: 600;
            box-shadow: 0 0 0 1px rgba(99, 102, 241, 0.1);
        }

        /* User Button (keeping avatar unchanged as requested) */
        .user-button {
            display: inline-flex;
            align-items: center;
            padding: 0.5rem 0.75rem;
            background: rgba(255, 255, 255, 0.8);
            border: 1px solid rgba(226, 232, 240, 0.8);
            border-radius: 0.75rem;
            font-size: 0.875rem;
            font-weight: 500;
            color: rgb(71 85 105);
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
        }

        .user-button:hover {
            background: rgba(255, 255, 255, 0.95);
            border-color: rgba(99, 102, 241, 0.2);
            box-shadow: 0 4px 12px -2px rgba(99, 102, 241, 0.1);
            transform: translateY(-1px);
        }

        .user-button:focus {
            outline: none;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
        }

        /* Dropdown Menu */
        .dropdown-menu {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(226, 232, 240, 0.8);
            border-radius: 0.75rem;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            overflow: hidden;
        }

        .dropdown-item {
            display: flex;
            align-items: center;
            width: 100%;
            padding: 0.75rem 1rem;
            font-size: 0.875rem;
            font-weight: 500;
            color: rgb(71 85 105);
            transition: all 0.15s ease-in-out;
            border: none;
            background: none;
            text-decoration: none;
        }

        .dropdown-item:hover {
            background-color: rgba(99, 102, 241, 0.05);
            color: rgb(99 102 241);
        }

        .dropdown-item i {
            margin-right: 0.75rem;
            font-size: 1rem;
            color: rgb(148 163 184);
            transition: color 0.15s ease-in-out;
        }

        .dropdown-item:hover i {
            color: rgb(99 102 241);
        }

        .dropdown-item.danger:hover {
            background-color: rgba(239, 68, 68, 0.05);
            color: rgb(239 68 68);
        }

        .dropdown-item.danger:hover i {
            color: rgb(239 68 68);
        }

        /* Primary Button */
        .btn-primary {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.625rem 1.25rem;
            font-size: 0.875rem;
            font-weight: 600;
            color: white;
            background: linear-gradient(135deg, rgb(99 102 241) 0%, rgb(139 92 246) 100%);
            border: none;
            border-radius: 0.75rem;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 0 0 1px rgba(99, 102, 241, 0.05);
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            text-decoration: none;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, rgb(79 70 229) 0%, rgb(124 58 237) 100%);
            box-shadow: 0 4px 12px -2px rgba(99, 102, 241, 0.25);
            transform: translateY(-1px);
        }

        .btn-primary:active {
            transform: translateY(0);
        }

        .btn-primary:focus {
            outline: none;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
        }

        /* Mobile Menu */
        .mobile-menu {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border-top: 1px solid rgba(226, 232, 240, 0.8);
        }

        /* Mobile Menu Button */
        .mobile-menu-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.5rem;
            color: rgb(71 85 105);
            border-radius: 0.5rem;
            transition: all 0.15s ease-in-out;
        }

        .mobile-menu-button:hover {
            background-color: rgba(99, 102, 241, 0.05);
            color: rgb(99 102 241);
        }

        /* Footer */
        .footer-enhanced {
            background: linear-gradient(to right, rgba(248, 250, 252, 0.8), rgba(241, 245, 249, 0.6));
            border-top: 1px solid rgba(226, 232, 240, 0.8);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
        }

        /* Toast Notifications */
        .toast {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(226, 232, 240, 0.8);
            border-radius: 0.75rem;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
        }

        /* Responsive Typography */
        @media (max-width: 640px) {
            .logo-text {
                font-size: 1.5rem;
            }
            
            .nav-link {
                padding: 0.75rem 1rem;
                font-size: 1rem;
            }
            
            .user-button {
                padding: 0.5rem;
                font-size: 0.875rem;
            }
        }

        /* Focus Styles */
        .focus-ring {
            transition: box-shadow 0.15s ease-in-out;
        }

        .focus-ring:focus {
            outline: none;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
        }

        /* Utility Classes */
        .text-balance {
            text-wrap: balance;
        }

        .backdrop-blur-glass {
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
        }
    </style>
</head>

<body class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-slate-50 text-slate-800 selection:bg-primary-100 selection:text-primary-900">
    <div x-data="{ openMobileMenu: false }" class="min-h-screen flex flex-col">
        <!-- Header -->
        <header class="glass-header sticky top-0 z-50 animate-fade-in">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <!-- Left side: Logo and Navigation -->
                    <div class="flex items-center space-x-8">
                        <!-- Logo -->
                        <div class="flex-shrink-0">
                            <a href="{{ route('dashboard') }}" class="focus-ring rounded-lg p-1 -m-1">
                                <span class="logo-text">DjokiHub</span>
                            </a>
                        </div>

                        <!-- Desktop Navigation -->
                        <nav class="hidden md:flex space-x-1">
                            @yield('navigation_links')
                        </nav>
                    </div>

                    <!-- Right side: User Menu -->
                    <div class="flex items-center space-x-4">
                        <!-- Desktop User Menu -->
                        <div class="hidden md:block">
                            @auth
                                <div x-data="{ open: false }" class="relative">
                                    <button @click="open = !open" 
                                            class="user-button focus-ring"
                                            :class="{ 'bg-white/95 border-primary-200': open }">
                                        <div class="flex items-center space-x-2">
                                            <img class="h-7 w-7 rounded-full object-cover user-avatar" 
                                                src="{{ Auth::user()->profile_photo_url ?? 'https://ui-avatars.com/api/?name='.urlencode(Auth::user()->name).'&background=4F46E5&color=fff&size=32&bold=true&font-size=0.4' }}" 
                                                alt="{{ Auth::user()->name }}">
                                            <span class="hidden sm:block max-w-[120px] truncate font-medium text-slate-700">
                                                {{ Auth::user()->name }}
                                            </span>
                                            <i class="ri-arrow-down-s-line text-slate-400 transition-transform duration-200" 
                                               :class="{ 'rotate-180': open }"></i>
                                        </div>
                                    </button>

                                    <!-- Dropdown Menu -->
                                    <div x-show="open" 
                                         @click.away="open = false"
                                         x-transition:enter="transition ease-out duration-200"
                                         x-transition:enter-start="opacity-0 scale-95"
                                         x-transition:enter-end="opacity-100 scale-100"
                                         x-transition:leave="transition ease-in duration-150"
                                         x-transition:leave-start="opacity-100 scale-100"
                                         x-transition:leave-end="opacity-0 scale-95"
                                         class="dropdown-menu absolute right-0 mt-2 w-56 origin-top-right"
                                         style="display: none;" 
                                         x-cloak>
                                        
                                        <div class="py-1">
                                            <div class="px-4 py-2 text-xs font-medium text-slate-400 uppercase tracking-wider border-b border-slate-100">
                                                Account
                                            </div>
                                            
                                            <a href="{{ route('staff.edit', ['user' => Auth::user()->id]) }}" 
                                               class="dropdown-item">
                                                <i class="ri-user-line"></i>
                                                Profile Settings
                                            </a>
                                            
                                            <div class="border-t border-slate-100 my-1"></div>
                                            
                                            <form method="POST" action="{{ route('logout') }}">
                                                @csrf
                                                <button type="submit" class="dropdown-item danger w-full text-left">
                                                    <i class="ri-logout-circle-line"></i>
                                                    Sign Out
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <a href="{{ route('login') }}" class="btn-primary focus-ring">
                                    <i class="ri-login-circle-line mr-2"></i>
                                    Sign In
                                </a>
                            @endauth
                        </div>

                        <!-- Mobile Menu Button -->
                        <div class="md:hidden">
                            <button @click="openMobileMenu = !openMobileMenu" 
                                    class="mobile-menu-button focus-ring">
                                <span class="sr-only">Open main menu</span>
                                <i class="ri-menu-line text-xl" 
                                   :class="{ 'hidden': openMobileMenu, 'block': !openMobileMenu }"></i>
                                <i class="ri-close-line text-xl" 
                                   :class="{ 'hidden': !openMobileMenu, 'block': openMobileMenu }" 
                                   x-cloak></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Mobile Menu -->
            <div x-show="openMobileMenu" 
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 -translate-y-2"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 translate-y-0"
                 x-transition:leave-end="opacity-0 -translate-y-2"
                 class="mobile-menu md:hidden" 
                 x-cloak>
                <div class="px-4 pt-2 pb-3 space-y-1">
                    @yield('navigation_links')
                </div>
                
                @auth
                    <div class="pt-4 pb-3 border-t border-slate-200">
                        <div class="flex items-center px-4 mb-3">
                            <div class="flex-shrink-0">
                                <img class="h-10 w-10 rounded-full object-cover user-avatar" 
                                     src="{{ Auth::user()->profile_photo_url ?? 'https://ui-avatars.com/api/?name='.urlencode(Auth::user()->name).'&background=4F46E5&color=fff&size=40&bold=true&font-size=0.4' }}" 
                                     alt="{{ Auth::user()->name }}">
                            </div>
                            <div class="ml-3">
                                <div class="text-base font-semibold text-slate-800">{{ Auth::user()->name }}</div>
                                <div class="text-sm text-slate-500">{{ Auth::user()->email }}</div>
                            </div>
                        </div>
                        
                        <div class="px-4 space-y-1">
                            <a href="{{ route('staff.edit', ['user' => Auth::user()->id]) }}" 
                               class="dropdown-item rounded-lg">
                                <i class="ri-user-line"></i>
                                Profile Settings
                            </a>
                            
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item danger w-full text-left rounded-lg">
                                    <i class="ri-logout-circle-line"></i>
                                    Sign Out
                                </button>
                            </form>
                        </div>
                    </div>
                @endauth
            </div>
        </header>

        <!-- Main Content -->
        <main class="flex-1 animate-fade-in-up">
            @yield('content')
        </main>

        <!-- Footer -->
        <footer class="footer-enhanced py-12 mt-auto">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col md:flex-row items-center justify-center space-y-4 md:space-y-0 md:space-x-8">
                    <div class="text-sm text-slate-500">
                        <span class="font-medium">© {{ date('Y') }} DjokiHub.</span> 
                        All rights reserved.
                    </div>
                    
                    <div class="flex items-center space-x-2 text-sm text-slate-400">
                        <span class="hidden md:block">•</span>
                        <span class="flex items-center space-x-1">
                            <span>Built by the Djoki Developer team</span>
                            <i class="ri-terminal-fill text-primary-500 animate-pulse-soft"></i>
                        </span>
                    </div>
                </div>
            </div>
        </footer>

        <!-- Toast Container -->
        <div id="global-toast-container" 
             class="fixed bottom-6 right-6 z-50 space-y-3 w-full max-w-sm pointer-events-none">
        </div>
    </div>

    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- Application Scripts -->
    <script>
        // Global Toast Function
        window.showGlobalToast = function(message, type = 'success', duration = 5000) {
            const container = document.getElementById('global-toast-container');
            if (!container) return;

            const toast = document.createElement('div');
            toast.className = 'toast flex items-start p-4 mb-3 animate-slide-up pointer-events-auto';
            
            const colors = {
                success: { bg: 'bg-emerald-50', text: 'text-emerald-800', icon: 'ri-checkbox-circle-fill text-emerald-500' },
                error: { bg: 'bg-red-50', text: 'text-red-800', icon: 'ri-error-warning-fill text-red-500' },
                warning: { bg: 'bg-amber-50', text: 'text-amber-800', icon: 'ri-alert-fill text-amber-500' },
                info: { bg: 'bg-blue-50', text: 'text-blue-800', icon: 'ri-information-fill text-blue-500' }
            };

            const color = colors[type] || colors.success;
            toast.classList.add(color.bg, color.text);

            toast.innerHTML = `
                <div class="flex-shrink-0">
                    <i class="${color.icon} text-lg"></i>
                </div>
                <div class="ml-3 flex-1">
                    <div class="font-medium text-sm">${message}</div>
                </div>
                <div class="ml-4 flex-shrink-0">
                    <button type="button" 
                            class="inline-flex text-slate-400 hover:text-slate-600 transition-colors" 
                            onclick="this.closest('.toast').remove()">
                        <i class="ri-close-line text-lg"></i>
                    </button>
                </div>
            `;

            container.appendChild(toast);

            setTimeout(() => {
                toast.style.transform = 'translateX(100%)';
                toast.style.opacity = '0';
                setTimeout(() => toast.remove(), 300);
            }, duration);
        };

        // Header scroll behavior
        let lastScrollY = window.scrollY;
        const header = document.querySelector('header');
        
        window.addEventListener('scroll', () => {
            const currentScrollY = window.scrollY;
            
            if (currentScrollY > lastScrollY && currentScrollY > 100) {
                header.style.transform = 'translateY(-100%)';
            } else {
                header.style.transform = 'translateY(0)';
            }
            
            lastScrollY = currentScrollY;
        }, { passive: true });

        // Enhanced focus management
        document.addEventListener('DOMContentLoaded', function() {
            // Add focus styles to interactive elements
            const focusableElements = document.querySelectorAll('button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])');
            
            focusableElements.forEach(element => {
                if (!element.classList.contains('focus-ring')) {
                    element.classList.add('focus-ring');
                }
            });
        });
    </script>

    @stack('scripts')
</body>
</html>
</html>
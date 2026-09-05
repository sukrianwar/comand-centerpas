<!DOCTYPE html>
<html class="light" lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Login | Prakarsa Command Center</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #F5F7FA; }
        
        .diagonal-clip {
            clip-path: polygon(0 0, 100% 0, 85% 100%, 0% 100%);
        }
        
        @media (max-width: 767px) {
            .diagonal-clip {
                clip-path: none;
            }
        }

        .fade-in-up {
            animation: fadeInUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            opacity: 0;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .input-focus-ring:focus-within {
            box-shadow: 0 0 0 2px rgba(15, 76, 129, 0.1);
        }

        .login-btn-gradient {
            background: linear-gradient(135deg, #0F4C81 0%, #18A999 100%);
        }

        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-gutter md:p-xl overflow-x-hidden">
<!-- Login Container -->
<main class="fade-in-up w-full max-w-[1100px] h-full min-h-[600px] md:h-[700px] bg-white rounded-[24px] shadow-[0_4px_20px_rgba(30,41,59,0.05)] flex flex-col md:flex-row overflow-hidden">
    <!-- Left Panel: Brand & Imagery -->
    <section class="relative w-full md:w-[45%] h-[240px] md:h-full overflow-hidden shrink-0">
        <div class="absolute inset-0 z-0 bg-cover bg-center diagonal-clip scale-105" 
     style="background-image: url('{{ asset('build/assets/logo2.png') }}');">
            <!-- Dark Overlay -->
            <div class="absolute inset-0 bg-primary/20 backdrop-brightness-75"></div>
        </div>
        <!-- Branding Overlay Content -->
        <div class="relative z-10 p-xl flex flex-col h-full justify-between pointer-events-none">
            <div class="mt-4">
                <h1 class="font-headline-lg-mobile md:font-headline-lg text-headline-lg-mobile md:text-headline-lg text-white leading-tight">
                    COMMAND CENTER<br/>
                    PT PRAKARSA ALAM SEGAR
                </h1>
                <p class="font-body-sm text-body-sm text-white/80 mt-xs tracking-wide">
                    Integrated Operations &amp; Monitoring
                </p>
            </div>
            <!-- Bottom Decorative Element -->
            <div class="hidden md:block">
                <div class="flex items-center gap-xs">
                    <span class="w-12 h-1 bg-white/40 rounded-full"></span>
                    <span class="w-2 h-1 bg-white/20 rounded-full"></span>
                    <span class="w-2 h-1 bg-white/20 rounded-full"></span>
                </div>
            </div>
        </div>
    </section>

    <!-- Right Panel: Login Form -->
    <section class="w-full md:w-[55%] flex flex-col justify-center items-center px-gutter md:px-xl py-xl bg-white">
        <div class="w-full max-w-[400px] space-y-lg">
            <!-- Header -->
            <div class="text-center space-y-xs">
                <div class="flex justify-center mb-md">
                    <img alt="Prakarsa Logo" class="h-12 w-auto object-contain" src="https://lh3.googleusercontent.com/aida-public/AB6AXuAomBp9dXMc6Pqj6LCXhLyt55ONmIaFtRfGUAE6Em9l0qb0DwRWY2EPLHa2bMC_JPHx_IdvFFPXDttIJQb6GLq3hWX_meooEajmb3XieJ1LlijqF182ID1JHd4D75W9BkNB0-sqZ7k6Jq9lFWYN7lK1Xa2xdMkJsuLHO_EBBG0gQKlDaEQT7g5CXw3Xlnd8H5ybObPO5pkUB2CHyo_6TuJH3G9knafGwWO7Q16_m_eE-E_I5XXFfnYMCU7ord0ioKUt6Q"/>
                </div>
                <h2 class="font-headline-lg-mobile text-headline-lg-mobile md:font-headline-lg md:text-headline-lg text-on-surface">
                    Welcome Back
                </h2>
                <p class="font-body-sm text-body-sm text-on-surface-variant">
                    Sign in to Command Center
                </p>

                <!-- Session Status -->
                <x-auth-session-status class="mb-4" :status="session('status')" />
            </div>

            <!-- Form -->
            <form class="space-y-md" id="loginForm" method="POST" action="{{ route('login') }}">
                @csrf

                <!-- Email Field -->
                <div class="space-y-base">
                    <label class="font-label-caps text-label-caps text-on-surface-variant" for="email">Email Address</label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-md flex items-center pointer-events-none text-outline">
                            <span class="material-symbols-outlined text-[20px]">mail</span>
                        </div>
                        <input class="w-full h-[52px] pl-[48px] pr-md bg-surface-container-low border border-outline-variant rounded-[12px] font-body-sm text-body-sm focus:outline-none focus:ring-2 focus:ring-primary/10 focus:border-primary transition-all duration-200" id="email" name="email" value="{{ old('email') }}" placeholder="name@prakarsa.com" required autofocus autocomplete="username" type="email"/>
                    </div>
                    <x-input-error :messages="$errors->get('email')" class="mt-2 text-error text-sm" />
                </div>

                <!-- Password Field -->
                <div class="space-y-base">
                    <div class="flex justify-between items-center">
                        <label class="font-label-caps text-label-caps text-on-surface-variant" for="password">Password</label>
                    </div>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-md flex items-center pointer-events-none text-outline">
                            <span class="material-symbols-outlined text-[20px]">lock</span>
                        </div>
                        <input class="w-full h-[52px] pl-[48px] pr-[48px] bg-surface-container-low border border-outline-variant rounded-[12px] font-body-sm text-body-sm focus:outline-none focus:ring-2 focus:ring-primary/10 focus:border-primary transition-all duration-200" id="password" name="password" placeholder="••••••••" required autocomplete="current-password" type="password"/>
                        <button class="absolute inset-y-0 right-0 pr-md flex items-center text-outline hover:text-on-surface transition-colors" id="togglePassword" type="button">
                            <span class="material-symbols-outlined text-[20px]" id="passwordIcon">visibility</span>
                        </button>
                    </div>
                    <x-input-error :messages="$errors->get('password')" class="mt-2 text-error text-sm" />
                </div>

                <!-- Options -->
                <div class="flex items-center justify-between py-base">
                    <label class="flex items-center gap-xs cursor-pointer group">
                        <input class="w-5 h-5 rounded border-outline-variant text-primary focus:ring-primary" type="checkbox" name="remember" id="remember_me"/>
                        <span class="font-body-sm text-body-sm text-on-surface-variant group-hover:text-on-surface transition-colors">Remember Me</span>
                    </label>
                    @if (Route::has('password.request'))
                        <a class="font-body-sm text-body-sm text-primary font-medium hover:underline" href="{{ route('password.request') }}">Forgot Password?</a>
                    @endif
                </div>

                <!-- Submit Button -->
                <button class="w-full h-[52px] login-btn-gradient text-white font-semibold rounded-[12px] shadow-lg shadow-primary/20 active:scale-[0.98] transition-all duration-200 flex items-center justify-center gap-xs" type="submit">
                    Login
                    <span class="material-symbols-outlined text-[20px]">arrow_forward</span>
                </button>
            </form>

            <!-- Divider -->
            <div class="flex items-center gap-md py-md">
                <span class="flex-grow h-[1px] bg-outline-variant"></span>
                <span class="font-label-caps text-label-caps text-outline">OR</span>
                <span class="flex-grow h-[1px] bg-outline-variant"></span>
            </div>

            <!-- Social Login -->
            <button class="w-full h-[52px] border border-outline-variant bg-white text-on-surface font-medium rounded-[12px] hover:bg-surface-container-low active:scale-[0.98] transition-all duration-200 flex items-center justify-center gap-md" type="button">
                <svg class="w-5 h-5" viewBox="0 0 24 24">
                    <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"></path>
                    <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"></path>
                    <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l3.66-2.84z" fill="#FBBC05"></path>
                    <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 12-4.53z" fill="#EA4335"></path>
                </svg>
                Continue with Google
            </button>

            <!-- Footer -->
            <div class="pt-xl text-center">
                <p class="font-label-caps text-label-caps text-outline leading-relaxed">
                    © 2026 PT PRAKARSA ALAM SEGAR<br/>
                    <span class="opacity-70">INTEGRATED OPERATIONS &amp; MONITORING</span>
                </p>
            </div>
        </div>
    </section>
</main>

<!-- Micro-interactions Script -->
<script>
    // Password Visibility Toggle
    const passwordInput = document.getElementById('password');
    const toggleBtn = document.getElementById('togglePassword');
    const icon = document.getElementById('passwordIcon');

    toggleBtn.addEventListener('click', () => {
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);
        icon.textContent = type === 'password' ? 'visibility' : 'visibility_off';
    });

    // Form Submit visual feedback (does not prevent actual submission)
    const form = document.getElementById('loginForm');
    form.addEventListener('submit', (e) => {
        const btn = form.querySelector('button[type="submit"]');
        
        // Show loading state while the form actually submits
        btn.innerHTML = `<span class="material-symbols-outlined animate-spin">sync</span> Validating...`;
        btn.classList.add('opacity-80', 'cursor-not-allowed');
    });

    // Background Paralax Micro-Effect
    document.addEventListener('mousemove', (e) => {
        const leftPanel = document.querySelector('.diagonal-clip');
        if (window.innerWidth > 768 && leftPanel) {
            const moveX = (e.clientX - window.innerWidth / 2) * 0.01;
            const moveY = (e.clientY - window.innerHeight / 2) * 0.01;
            leftPanel.style.transform = `scale(1.05) translate(${moveX}px, ${moveY}px)`;
        }
    });
</script>

</body>
</html>

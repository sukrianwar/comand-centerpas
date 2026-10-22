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

        .login-panel-gradient {
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
        <div class="absolute inset-0 z-0 diagonal-clip scale-105 login-panel-gradient"></div>
        <!-- Brand Logo -->
        <div class="relative z-10 flex h-full items-center justify-center p-xl">
            <div class="w-full max-w-[200px] md:max-w-[300px] rounded-2xl bg-white/95 p-4 md:p-5 shadow-2xl">
                <img alt="Command Center PT Prakarsa Alam Segar" class="h-auto w-full object-contain" src="{{ asset('images/logo2.png') }}"/>
            </div>
        </div>
        <!-- Bottom Decorative Element -->
        <div class="absolute bottom-0 left-0 z-10 hidden w-full p-xl md:block">
            <div class="flex items-center gap-xs">
                <span class="w-12 h-1 bg-white/40 rounded-full"></span>
                <span class="w-2 h-1 bg-white/20 rounded-full"></span>
                <span class="w-2 h-1 bg-white/20 rounded-full"></span>
            </div>
        </div>
    </section>

    <!-- Right Panel: Login Form -->
    <section class="w-full md:w-[55%] flex flex-col justify-center items-center px-gutter md:px-xl py-xl bg-white">
        <div class="w-full max-w-[400px] space-y-lg">
            <!-- Header -->
            <div class="text-center space-y-xs">
                <div class="flex justify-center mb-md">
                    <img alt="Prakarsa Logo" class="h-14 w-14 object-contain" src="{{ asset('images/logo-mark.png') }}"/>
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

                <!-- Login Field -->
                <div class="space-y-base">
                    <label class="font-label-caps text-label-caps text-on-surface-variant" for="login">Username atau Email</label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-md flex items-center pointer-events-none text-outline">
                            <span class="material-symbols-outlined text-[20px]">person</span>
                        </div>
                        <input class="w-full h-[52px] pl-[48px] pr-md bg-surface-container-low border border-outline-variant rounded-[12px] font-body-sm text-body-sm focus:outline-none focus:ring-2 focus:ring-primary/10 focus:border-primary transition-all duration-200" id="login" name="login" value="{{ old('login') }}" placeholder="username atau nama@prakarsa.com" required autofocus autocomplete="username" type="text"/>
                    </div>
                    <x-input-error :messages="$errors->get('login')" class="mt-2 text-error text-sm" />
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
                </div>

                <!-- Submit Button -->
                <button class="w-full h-[52px] login-btn-gradient text-white font-semibold rounded-[12px] shadow-lg shadow-primary/20 active:scale-[0.98] transition-all duration-200 flex items-center justify-center gap-xs" type="submit">
                    Login
                    <span class="material-symbols-outlined text-[20px]">arrow_forward</span>
                </button>
            </form>

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

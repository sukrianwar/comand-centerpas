<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Sentinel Guard Systems') }}</title>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <style>
            body { font-family: 'Inter', sans-serif; }
            h1, h2, h3, h4, h5, h6 { font-family: 'Hanken Grotesk', sans-serif; }
        </style>
    </head>
    <body class="text-on-surface antialiased">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-8 sm:pt-0 px-4 relative overflow-hidden">
            {{-- Dekorasi latar lembut --}}
            <div class="absolute -top-40 -left-32 w-[28rem] h-[28rem] rounded-full bg-primary/5 blur-3xl pointer-events-none"></div>
            <div class="absolute -bottom-40 -right-32 w-[28rem] h-[28rem] rounded-full bg-blue-500/5 blur-3xl pointer-events-none"></div>

            <div class="relative z-10 w-full flex flex-col items-center">
                <a href="/" class="mb-6">
                    <x-application-logo class="w-16 h-16 fill-current text-primary" />
                </a>

                <div class="w-full sm:max-w-md px-6 py-7 bg-surface-container-lowest border border-outline-variant/70 shadow-card rounded-3xl">
                    {{ $slot }}
                </div>

                <p class="mt-6 text-label-md text-secondary">&copy; {{ date('Y') }} Sentinel Guard Systems</p>
            </div>
        </div>
    </body>
</html>

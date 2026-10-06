<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'La Bibliothèque des Mots')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#2a190e] text-[#f3e7cc] font-garamond min-h-screen relative" style="background: repeating-linear-gradient(90deg,#2c1b10 0 3px,#341f11 3px 8px,#2a190e 8px 13px),#2a190e">
    <div id="lamp" aria-hidden="true"></div>

    <nav class="border-b border-[#4a2c17] bg-[#1b1209]/90 backdrop-blur-sm px-4 py-3 text-sm">
        <div class="mx-auto flex max-w-5xl flex-wrap items-center justify-between gap-3">
            <a href="{{ url('/') }}" class="font-cinzel text-lg font-bold tracking-wider text-[#e9c96b] hover:no-underline">
                📖 La Bibliothèque des Mots
            </a>
            
            <div class="flex items-center gap-4">
                @auth
                    <div class="flex items-center gap-2">
                        <span class="font-bold text-[#f3e7cc]">{{ auth()->user()->name }}</span>
                        <span class="rounded px-2 py-0.5 text-xs font-bold 
                            @if(auth()->user()->role === 'admin') bg-purple-900 text-purple-200 
                            @elseif(auth()->user()->role === 'gerant') bg-blue-900 text-blue-200 
                            @elseif(auth()->user()->role === 'auteur') bg-amber-900 text-amber-200 
                            @else bg-gray-800 text-gray-300 @endif">
                            {{ ucfirst(auth()->user()->role) }}
                        </span>
                    </div>

                    @if(auth()->user()->isAuthor())
                        <a href="{{ route('admin.index') }}" class="font-garamond text-sm italic text-[#e9c96b] underline hover:text-white">
                            Bureau Admin →
                        </a>
                    @endif

                    <form action="{{ route('logout') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="text-xs text-[#e9c96b]/80 underline hover:text-[#e9c96b]">Déconnexion</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="plate px-3 py-1 text-sm hover:no-underline inline-block">Connexion</a>
                    <a href="{{ route('register') }}" class="font-garamond text-sm italic text-[#e9c96b] underline hover:text-white">Inscription</a>
                @endauth
            </div>
        </div>
    </nav>

    @yield('content')

    <div id="t" role="status" class="plate fixed left-1/2 z-[60] max-w-[92vw] px-5 py-2 text-center text-lg" style="bottom:calc(1.5rem + env(safe-area-inset-bottom,0px))"></div>

    <script>
        addEventListener('pointermove', e => {
            document.documentElement.style.setProperty('--mx', e.clientX + 'px');
            document.documentElement.style.setProperty('--my', e.clientY + 'px');
        }, { passive: true });
    </script>
    @stack('scripts')
</body>
</html>

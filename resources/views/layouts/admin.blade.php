<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Bureau du Bibliothécaire — Administration')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="px-3 pb-16 pt-6 sm:px-6 min-h-screen text-[#f3e7cc] font-garamond" style="background:#1b1209;background-image:radial-gradient(ellipse 900px 500px at 50% -10%,#2f2011,transparent),linear-gradient(#17100a,#120c06)">
    <header class="mx-auto max-w-5xl">
        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-[#4a2c17] pb-4">
            <div>
                <p class="font-garamond text-sm italic text-[#e9c96b]/70">La Bibliothèque des Mots</p>
                <h1 class="font-cinzel text-2xl font-bold sm:text-3xl text-[#e9c96b]">Bureau du Bibliothécaire</h1>
            </div>
            
            <div class="flex items-center gap-3">
                {{-- Menu déroulant sous le nom de l'utilisateur --}}
                <div class="relative" id="admin-user-menu-wrap">
                    <button id="admin-user-menu-btn" type="button" aria-expanded="false" class="flex items-center gap-2 rounded bg-[#2a1a0e] px-3.5 py-1.5 text-sm font-bold text-[#e9c96b] border border-[#6b4a12] hover:border-[#b98a2e] transition cursor-pointer shadow">
                        <span>👤 {{ auth()->user()->name }}</span>
                        <span class="rounded px-2 py-0.5 text-[10px] uppercase bg-[#8b1e1e] text-white font-mono">
                            {{ auth()->user()->role }}
                        </span>
                        <span class="text-xs text-[#e9c96b]/70">▾</span>
                    </button>
                    
                    <div id="admin-user-menu-dropdown" class="absolute right-0 mt-2 w-56 rounded bg-[#1b1209] border border-[#4a2c17] shadow-2xl py-1 hidden z-50">
                        <a href="{{ url('/') }}" class="block px-4 py-2 text-sm text-[#f3e7cc] hover:bg-[#2a190e] hover:text-[#e9c96b]">
                            🏠 Retour à la boutique
                        </a>

                        <div class="border-t border-[#4a2c17] my-1"></div>

                        <a href="{{ route('admin.books.index') }}" class="block px-4 py-2 text-sm text-[#f3e7cc] hover:bg-[#2a190e] hover:text-[#e9c96b]">
                            📚 Registre des Livres
                        </a>

                        @if (auth()->user()->isGerant())
                            <a href="{{ route('admin.categories.index') }}" class="block px-4 py-2 text-sm text-[#f3e7cc] hover:bg-[#2a190e] hover:text-[#e9c96b]">
                                🏷️ Catégories
                            </a>
                        @endif

                        @if (auth()->user()->isAdmin())
                            <a href="{{ route('admin.users.index') }}" class="block px-4 py-2 text-sm text-[#f3e7cc] hover:bg-[#2a190e] hover:text-[#e9c96b]">
                                👥 Comptes & Rôles
                            </a>
                        @endif

                        <div class="border-t border-[#4a2c17] my-1"></div>

                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-400 hover:bg-[#2a190e] hover:text-red-300 cursor-pointer">
                                🚪 Déconnexion
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <nav class="mt-4 flex flex-wrap gap-2 text-base">
            <a href="{{ route('admin.books.index') }}" class="plate px-4 py-1.5 text-base hover:no-underline {{ request()->routeIs('admin.books.*') || request()->routeIs('admin.index') ? '!brightness-125' : 'opacity-80' }}">
                📚 Registre des Livres
            </a>

            @if (auth()->user()->isGerant())
                <a href="{{ route('admin.categories.index') }}" class="plate px-4 py-1.5 text-base hover:no-underline {{ request()->routeIs('admin.categories.*') ? '!brightness-125' : 'opacity-80' }}">
                    🏷️ Catégories
                </a>
            @endif

            @if (auth()->user()->isAdmin())
                <a href="{{ route('admin.users.index') }}" class="plate px-4 py-1.5 text-base hover:no-underline {{ request()->routeIs('admin.users.*') ? '!brightness-125' : 'opacity-80' }}">
                    👥 Comptes & Rôles
                </a>
            @endif
        </nav>

        @yield('header_stats')
    </header>

    <main class="mx-auto mt-8 max-w-5xl">
        @yield('content')
    </main>

    <div id="t" role="status" class="plate pointer-events-none fixed left-1/2 z-[60] max-w-[92vw] -translate-x-1/2 px-5 py-2 text-center text-lg opacity-0 transition-opacity" style="bottom:calc(1.5rem + env(safe-area-inset-bottom,0px))"></div>

    <script>
        // Admin user menu dropdown toggle logic
        (function() {
            const btn = document.getElementById('admin-user-menu-btn');
            const dropdown = document.getElementById('admin-user-menu-dropdown');
            if (!btn || !dropdown) return;

            btn.addEventListener('click', (e) => {
                e.stopPropagation();
                const isHidden = dropdown.classList.contains('hidden');
                dropdown.classList.toggle('hidden', !isHidden);
                btn.setAttribute('aria-expanded', isHidden ? 'true' : 'false');
            });

            document.addEventListener('click', (e) => {
                if (!dropdown.contains(e.target) && !btn.contains(e.target)) {
                    dropdown.classList.add('hidden');
                    btn.setAttribute('aria-expanded', 'false');
                }
            });

            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') {
                    dropdown.classList.add('hidden');
                    btn.setAttribute('aria-expanded', 'false');
                }
            });
        })();
    </script>
    @stack('scripts')
</body>
</html>

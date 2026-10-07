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
                <x-user-dropdown :is-admin="true" />
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

            @if (request()->routeIs('profile.edit'))
                <a href="{{ route('profile.edit') }}" class="plate px-4 py-1.5 text-base hover:no-underline !brightness-125">
                    ⚙️ Mon Profil
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
        // Dropdown toggle générique
        (function() {
            document.addEventListener('click', (e) => {
                const toggleBtn = e.target.closest('.user-menu-toggle');
                if (toggleBtn) {
                    e.stopPropagation();
                    const wrap = toggleBtn.closest('.relative');
                    const dropdown = wrap ? wrap.querySelector('.user-menu-dropdown') : null;
                    if (dropdown) {
                        const isHidden = dropdown.classList.contains('hidden');
                        document.querySelectorAll('.user-menu-dropdown').forEach(d => d.classList.add('hidden'));
                        dropdown.classList.toggle('hidden', !isHidden);
                    }
                } else if (!e.target.closest('.user-menu-dropdown')) {
                    document.querySelectorAll('.user-menu-dropdown').forEach(d => d.classList.add('hidden'));
                }
            });

            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') {
                    document.querySelectorAll('.user-menu-dropdown').forEach(d => d.classList.add('hidden'));
                }
            });
        })();
    </script>
    @stack('scripts')
</body>
</html>

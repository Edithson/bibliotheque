<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'La Bibliothèque des Mots')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#2a190e] text-[#f3e7cc] font-garamond min-h-screen relative flex flex-col justify-between" style="background: repeating-linear-gradient(90deg,#2c1b10 0 3px,#341f11 3px 8px,#2a190e 8px 13px),#2a190e">
    <div id="lamp" aria-hidden="true"></div>

    <div>
        {{-- Navigation bar intelligente escamotable au scroll --}}
        <nav id="navbar" class="border-b border-[#4a2c17] bg-[#1b1209]/95 backdrop-blur-md px-4 py-2.5 text-sm fixed top-0 left-0 right-0 z-50 transition-transform duration-300 transform">
            <div class="mx-auto flex max-w-5xl flex-wrap items-center justify-between gap-3">
                <a href="{{ url('/') }}" class="font-cinzel text-lg font-bold tracking-wider text-[#e9c96b] hover:no-underline flex items-center gap-2">
                    <span>📖</span> <span>La Bibliothèque des Mots</span>
                </a>
                
                <div class="flex flex-wrap items-center gap-3">
                    @auth
                        <x-user-dropdown :is-admin="false" />
                    @else
                        <a href="{{ route('my-books') }}" class="font-garamond text-sm text-[#e9c96b]/80 hover:text-white underline">
                            📚 Mes Livres
                        </a>

                        <a href="{{ route('login') }}" class="plate px-4 py-1.5 text-sm font-bold hover:no-underline inline-block shadow-md">
                            🔑 Connexion / Inscription
                        </a>
                    @endauth
                </div>
            </div>
        </nav>

        <main class="pt-14">
            @yield('content')
        </main>
    </div>

    {{-- Footer fin et responsive --}}
    <footer class="mt-12 border-t border-[#4a2c17] bg-[#120a04]/90 px-4 py-4 font-garamond text-xs text-[#e9c96b]/70">
        <div class="mx-auto flex max-w-5xl flex-col items-center justify-between gap-3 text-center sm:flex-row sm:text-left">
            <div>
                <a href="{{ route('about') }}" class="italic underline hover:text-[#e9c96b]">
                    📜 À propos du projet
                </a>
            </div>

            <div class="text-[#e9c96b]/50 italic">
                Développé avec passion pour la littérature &bull; La Bibliothèque des Mots
            </div>

            <div>
                <a href="{{ route('contact') }}" class="italic underline hover:text-[#e9c96b]">
                    ✉️ Nous contacter
                </a>
            </div>
        </div>
    </footer>

    <div id="t" role="status" class="plate fixed left-1/2 z-[60] max-w-[92vw] px-5 py-2 text-center text-lg" style="bottom:calc(1.5rem + env(safe-area-inset-bottom,0px))"></div>

    <script>
        addEventListener('pointermove', e => {
            document.documentElement.style.setProperty('--mx', e.clientX + 'px');
            document.documentElement.style.setProperty('--my', e.clientY + 'px');
        }, { passive: true });

        // Scroll auto-hide navbar (Scroll down = masque, Scroll up = réapparaît)
        (function() {
            let lastScrollY = window.scrollY;
            const navbar = document.getElementById('navbar');
            if (!navbar) return;

            window.addEventListener('scroll', () => {
                const currentScrollY = window.scrollY;
                if (currentScrollY < 60) {
                    navbar.classList.remove('-translate-y-full');
                } else if (currentScrollY > lastScrollY && currentScrollY > 90) {
                    navbar.classList.add('-translate-y-full');
                    document.querySelectorAll('.user-menu-dropdown').forEach(d => d.classList.add('hidden'));
                } else if (currentScrollY < lastScrollY) {
                    navbar.classList.remove('-translate-y-full');
                }
                lastScrollY = currentScrollY;
            }, { passive: true });
        })();

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

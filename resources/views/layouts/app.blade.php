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
        {{-- Navigation bar épurée --}}
        <nav class="border-b border-[#4a2c17] bg-[#1b1209]/95 backdrop-blur-md px-4 py-2.5 text-sm sticky top-0 z-50">
            <div class="mx-auto flex max-w-5xl flex-wrap items-center justify-between gap-3">
                <a href="{{ url('/') }}" class="font-cinzel text-lg font-bold tracking-wider text-[#e9c96b] hover:no-underline flex items-center gap-2">
                    <span>📖</span> <span>La Bibliothèque des Mots</span>
                </a>
                
                <div class="flex flex-wrap items-center gap-3">
                    <a href="{{ route('contact', ['subject' => 'author_request']) }}" class="font-garamond text-sm italic text-[#e9c96b]/90 hover:text-white underline hidden sm:inline-block">
                        ✍️ Devenir Auteur
                    </a>

                    @auth
                        <a href="{{ route('my-books') }}" class="font-garamond text-sm font-semibold text-[#e9c96b] hover:underline">
                            📚 Mes Livres
                        </a>

                        @if(auth()->user()->isAuthor())
                            <a href="{{ route('admin.index') }}" class="plate px-3 py-1 text-xs hover:no-underline inline-block">
                                🏛️ Bureau Admin
                            </a>
                        @endif

                        <div class="flex items-center gap-2 border-l border-[#4a2c17] pl-3">
                            <span class="font-bold text-[#f3e7cc] text-xs sm:text-sm">{{ auth()->user()->name }}</span>
                            <form action="{{ route('logout') }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="text-xs text-[#e9c96b]/80 underline hover:text-[#e9c96b]">Déconnexion</button>
                            </form>
                        </div>
                    @else
                        <a href="{{ route('my-books') }}" class="font-garamond text-sm text-[#e9c96b]/80 hover:text-white underline">
                            📚 Mes Livres
                        </a>

                        {{-- Bouton d'authentification unique (Connexion / Inscription) --}}
                        <a href="{{ route('login') }}" class="plate px-4 py-1.5 text-sm font-bold hover:no-underline inline-block shadow-md">
                            🔑 Connexion / Inscription
                        </a>
                    @endauth
                </div>
            </div>
        </nav>

        <main>
            @yield('content')
        </main>
    </div>

    {{-- Footer très fin, épuré et responsive (Cascade sur mobile) --}}
    <footer class="mt-12 border-t border-[#4a2c17] bg-[#120a04]/90 px-4 py-4 font-garamond text-xs text-[#e9c96b]/70">
        <div class="mx-auto flex max-w-5xl flex-col items-center justify-between gap-3 text-center sm:flex-row sm:text-left">
            {{-- Extrême gauche : À propos --}}
            <div>
                <a href="{{ route('about') }}" class="italic underline hover:text-[#e9c96b]">
                    📜 À propos du projet
                </a>
            </div>

            {{-- Centre : Crédit Dev --}}
            <div class="text-[#e9c96b]/50 italic">
                Développé avec passion pour la littérature &bull; La Bibliothèque des Mots
            </div>

            {{-- Extrême droite : Contact --}}
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
    </script>
    @stack('scripts')
</body>
</html>

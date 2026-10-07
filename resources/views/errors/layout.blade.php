<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title>@yield('title', 'Erreur — La Bibliothèque des Mots')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="px-4 py-12 min-h-screen text-[#f3e7cc] font-garamond flex items-center justify-center" style="background:#1b1209;background-image:radial-gradient(ellipse 900px 500px at 50% -10%,#2f2011,transparent),linear-gradient(#17100a,#120c06)">
    <div class="w-full max-w-xl text-center space-y-6">
        {{-- En-tête de Marque --}}
        <div class="space-y-1">
            <p class="font-garamond text-base italic text-[#e9c96b]/80">La Bibliothèque des Mots</p>
            <h1 class="font-cinzel text-3xl font-bold text-[#e9c96b] tracking-wider">Sanctuaire Littéraire</h1>
        </div>

        {{-- Carte d'Erreur --}}
        <div class="card2 p-8 sm:p-10 space-y-6 shadow-2xl relative overflow-hidden">
            <div class="inline-flex items-center justify-center rounded-full bg-[#3d1e10] px-4 py-1 text-sm font-bold font-mono text-[#e9c96b] border border-[#6b3e21]">
                CODE ERREUR @yield('code')
            </div>

            <div class="space-y-3">
                <h2 class="font-cinzel text-2xl font-bold sm:text-3xl text-amber-100">
                    @yield('message_title', 'Une anomalie s\'est produite')
                </h2>

                <p class="text-base text-gray-300 leading-relaxed font-garamond">
                    @yield('message')
                </p>
            </div>

            {{-- Actions de Navigation --}}
            <div class="pt-4 border-t border-[#4a2c17] flex flex-wrap items-center justify-center gap-3">
                @yield('actions')
                
                <a href="{{ url('/') }}" class="plate px-5 py-2.5 text-base font-bold hover:no-underline inline-block shadow">
                    🏠 Retourner aux Rayons
                </a>
            </div>
        </div>

        {{-- Pied de page --}}
        <div class="text-xs italic text-[#e9c96b]/60">
            « Un livre est une fenêtre par laquelle on s'évade. » — La Bibliothèque des Mots
        </div>
    </div>
</body>
</html>

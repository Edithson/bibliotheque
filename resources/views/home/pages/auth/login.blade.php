@extends('layouts.app')

@section('title', 'Connexion — La Bibliothèque des Mots')

@section('content')
<main class="mx-auto max-w-md px-4 py-12">
    <div class="card2 p-6 sm:p-8">
        <div class="text-center">
            <h1 class="font-cinzel text-2xl font-bold text-[#e9c96b]">Connexion au Compte</h1>
            <p class="mt-1 font-garamond text-base italic text-[#e9c96b]/70">Accédez à votre espace lecteur et à la bibliothèque</p>
        </div>

        {{-- Google OAuth Button (Featured Prominently) --}}
        <div class="mt-6">
            <a href="{{ route('auth.google') }}" class="flex w-full items-center justify-center gap-3 rounded bg-white px-4 py-2.5 font-sans text-sm font-semibold text-gray-800 shadow hover:bg-gray-100 transition">
                <svg class="h-5 w-5" viewBox="0 0 24 24">
                    <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                    <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                    <path fill="#FBBC05" d="M5.84 14.1c-.22-.66-.35-1.36-.35-2.1s.13-1.44.35-2.1V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.62z"/>
                    <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z"/>
                </svg>
                <span>Se connecter avec Google</span>
            </a>
        </div>

        <div class="relative my-6 flex items-center justify-center">
            <div class="absolute inset-0 flex items-center"><div class="w-full border-t border-[#4a2c17]"></div></div>
            <span class="relative bg-[#271a0d] px-3 font-garamond text-sm italic text-[#e9c96b]/60">ou par identifiants</span>
        </div>

        @if (session('status'))
            <div class="mb-4 text-sm font-semibold text-[#68d391]">
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-4 rounded border border-[#e05a3f] bg-[#e05a3f]/10 p-3 text-sm text-red-200">
                <ul class="list-disc pl-4 space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}" class="space-y-4">
            @csrf

            <div>
                <label for="email" class="block font-garamond text-sm font-semibold text-[#e9c96b]">Adresse E-mail</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" class="admin-field-input mt-1">
            </div>

            <div>
                <label for="password" class="block font-garamond text-sm font-semibold text-[#e9c96b]">Mot de passe</label>
                <input id="password" type="password" name="password" required autocomplete="current-password" class="admin-field-input mt-1">
            </div>

            <div class="flex items-center justify-between">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="remember" class="rounded accent-[#b98a2e]">
                    <span class="font-garamond text-sm text-[#e9c96b]/80">Se souvenir de moi</span>
                </label>
            </div>

            <div class="pt-2">
                <button type="submit" class="plate w-full py-2 text-xl">Se connecter</button>
            </div>
        </form>

        <div class="mt-6 text-center font-garamond text-sm">
            <span class="text-[#f3e7cc]/70">Pas encore de compte ?</span>
            <a href="{{ route('register') }}" class="ml-1 text-[#e9c96b] underline hover:text-white">Créer un compte</a>
        </div>
    </div>
</main>
@endsection

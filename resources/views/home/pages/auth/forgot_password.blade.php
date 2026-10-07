@extends('layouts.app')

@section('title', 'Mot de Passe Oublié — La Bibliothèque des Mots')

@section('content')
<main class="mx-auto max-w-md px-4 py-16 font-garamond">
    <div class="card2 p-6 sm:p-8 space-y-6">
        <div class="border-b border-[#4a2c17] pb-4 text-center">
            <p class="font-garamond text-sm italic text-[#e9c96b]/70">Espace Sécurité</p>
            <h1 class="font-cinzel text-2xl font-bold text-[#e9c96b]">Mot de Passe Oublié ?</h1>
        </div>

        <p class="text-sm text-gray-300 leading-relaxed">
            Saisissez votre adresse e-mail ci-dessous. Nous vous transmettrons un lien de réinitialisation sécurisé pour configurer un nouveau mot de passe.
        </p>

        @if (session('status'))
            <div class="rounded border border-[#2f855a] bg-[#276749]/30 p-3 text-sm text-[#68d391]">
                ✓ {{ session('status') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="rounded border border-[#e05a3f] bg-[#e05a3f]/10 p-3 text-xs text-red-200">
                <ul class="list-disc pl-4 space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
            @csrf

            <div>
                <label for="email" class="block text-sm font-semibold text-[#e9c96b] mb-1">Votre Adresse E-mail *</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus class="admin-field-input" placeholder="exemple@domaine.com">
            </div>

            <div class="pt-2">
                <button type="submit" class="plate w-full py-2 text-lg font-bold">
                    📨 Envoyer le lien de réinitialisation
                </button>
            </div>
        </form>

        <div class="pt-4 border-t border-[#4a2c17] text-center text-sm">
            <a href="{{ route('login') }}" class="text-[#e9c96b]/80 hover:text-[#e9c96b] underline italic">
                ← Se souvenir de mon mot de passe (Se connecter)
            </a>
        </div>
    </div>
</main>
@endsection

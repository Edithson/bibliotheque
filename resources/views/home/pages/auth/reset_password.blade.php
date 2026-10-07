@extends('layouts.app')

@section('title', 'Nouveau Mot de Passe — La Bibliothèque des Mots')

@section('content')
<main class="mx-auto max-w-md px-4 py-16 font-garamond">
    <div class="card2 p-6 sm:p-8 space-y-6">
        <div class="border-b border-[#4a2c17] pb-4 text-center">
            <p class="font-garamond text-sm italic text-[#e9c96b]/70">Espace Sécurité</p>
            <h1 class="font-cinzel text-2xl font-bold text-[#e9c96b]">Nouveau Mot de Passe</h1>
        </div>

        @if ($errors->any())
            <div class="rounded border border-[#e05a3f] bg-[#e05a3f]/10 p-3 text-xs text-red-200">
                <ul class="list-disc pl-4 space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('password.update') }}" class="space-y-4">
            @csrf

            <input type="hidden" name="token" value="{{ $token }}">

            <div>
                <label for="email" class="block text-sm font-semibold text-[#e9c96b] mb-1">Adresse E-mail *</label>
                <input id="email" type="email" name="email" value="{{ old('email', $request->email) }}" required class="admin-field-input">
            </div>

            <div>
                <label for="password" class="block text-sm font-semibold text-[#e9c96b] mb-1">Nouveau mot de passe *</label>
                <input id="password" type="password" name="password" required class="admin-field-input" placeholder="Minimum 8 caractères">
            </div>

            <div>
                <label for="password_confirmation" class="block text-sm font-semibold text-[#e9c96b] mb-1">Confirmer le nouveau mot de passe *</label>
                <input id="password_confirmation" type="password" name="password_confirmation" required class="admin-field-input" placeholder="Répétez le nouveau mot de passe">
            </div>

            <div class="pt-2">
                <button type="submit" class="plate w-full py-2 text-lg font-bold">
                    🗝️ Mettre à jour mon mot de passe
                </button>
            </div>
        </form>
    </div>
</main>
@endsection

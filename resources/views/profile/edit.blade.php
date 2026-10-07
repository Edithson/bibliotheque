@extends('layouts.app')

@section('title', 'Mon Profil — La Bibliothèque des Mots')

@section('content')
<main class="mx-auto max-w-4xl px-4 pb-16 pt-24 sm:px-6 font-garamond">
    <div class="mb-6 flex flex-wrap items-center justify-between gap-4 border-b border-[#4a2c17] pb-4">
        <div>
            <p class="font-garamond text-sm italic text-[#e9c96b]/70">Espace personnel</p>
            <h1 class="font-cinzel text-2xl font-bold sm:text-3xl text-[#e9c96b]">Gestion de Mon Compte</h1>
        </div>

        @if(auth()->user()->isAuthor())
            <a href="{{ route('admin.index') }}" class="plate px-4 py-1.5 text-sm font-semibold hover:no-underline">
                🏛️ Accéder au Bureau Admin
            </a>
        @endif
    </div>

    @if (session('success'))
        <div class="mb-6 rounded border border-[#2f855a] bg-[#276749]/30 p-3 text-lg text-[#68d391]">
            ✓ {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="mb-6 rounded border border-[#e05a3f] bg-[#e05a3f]/20 p-3 text-lg text-red-200">
            ⚠️ {{ session('error') }}
        </div>
    @endif

    <div class="grid gap-8 md:grid-cols-2">
        {{-- Card 1: Informations Personnelles --}}
        <div class="card2 p-6 h-fit space-y-4">
            <div class="flex items-center justify-between border-b border-[#4a2c17] pb-3">
                <h2 class="font-cinzel text-xl font-bold text-[#e9c96b]">Informations Personnelles</h2>
                <span class="rounded px-2.5 py-1 text-xs font-bold font-mono uppercase bg-[#8b1e1e] text-white">
                    {{ $user->role }}
                </span>
            </div>

            <form action="{{ route('profile.update') }}" method="POST" class="space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label for="name" class="block text-sm font-semibold text-[#e9c96b] mb-1">Nom d'utilisateur *</label>
                    <input id="name" type="text" name="name" value="{{ old('name', $user->name) }}" required class="admin-field-input">
                    @error('name')
                        <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="email" class="block text-sm font-semibold text-[#e9c96b] mb-1">Adresse E-mail *</label>
                    <input id="email" type="email" name="email" value="{{ old('email', $user->email) }}" required class="admin-field-input">
                    @error('email')
                        <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div class="pt-2">
                    <button type="submit" class="plate w-full py-2 text-lg font-bold">Enregistrer les modifications</button>
                </div>
            </form>
        </div>

        {{-- Card 2: Modifier le Mot de Passe --}}
        <div class="card2 p-6 h-fit space-y-4">
            <h2 class="font-cinzel text-xl font-bold text-[#e9c96b] border-b border-[#4a2c17] pb-3">Changer mon Mot de Passe</h2>

            <form action="{{ route('profile.password.update') }}" method="POST" class="space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label for="current_password" class="block text-sm font-semibold text-[#e9c96b] mb-1">Mot de passe actuel *</label>
                    <input id="current_password" type="password" name="current_password" required class="admin-field-input" placeholder="Saisissez votre mot de passe actuel">
                    @error('current_password')
                        <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="block text-sm font-semibold text-[#e9c96b] mb-1">Nouveau mot de passe *</label>
                    <input id="password" type="password" name="password" required class="admin-field-input" placeholder="Minimum 8 caractères">
                    @error('password')
                        <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-semibold text-[#e9c96b] mb-1">Confirmer le nouveau mot de passe *</label>
                    <input id="password_confirmation" type="password" name="password_confirmation" required class="admin-field-input" placeholder="Répétez le nouveau mot de passe">
                </div>

                <div class="pt-2">
                    <button type="submit" class="plate w-full py-2 text-lg font-bold text-amber-950" style="background: linear-gradient(135deg,#fbd38d,#ed8936 50%,#c05621)">
                        Modifier mon mot de passe
                    </button>
                </div>
            </form>
        </div>
    </div>
</main>
@endsection

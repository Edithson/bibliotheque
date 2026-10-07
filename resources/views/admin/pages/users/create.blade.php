@extends('layouts.admin')

@section('title', 'Créer un Utilisateur — Bureau du Bibliothécaire')

@section('content')
<div class="mb-6 flex items-center justify-between">
    <div>
        <p class="font-garamond text-sm italic text-[#e9c96b]/70">
            <a href="{{ route('admin.users.index') }}" class="underline hover:text-white">Gestion des Comptes</a> &rsaquo; Nouveau Compte
        </p>
        <h1 class="font-cinzel text-2xl font-bold sm:text-3xl text-[#e9c96b]">Créer un Nouvel Utilisateur</h1>
    </div>

    <a href="{{ route('admin.users.index') }}" class="plate px-4 py-1.5 text-sm font-semibold hover:no-underline">
        ↩️ Retour à la liste
    </a>
</div>

<div class="card2 max-w-2xl mx-auto p-6 font-garamond">
    <form action="{{ route('admin.users.store') }}" method="POST" class="space-y-4">
        @csrf

        <div>
            <label for="name" class="block text-sm font-semibold text-[#e9c96b] mb-1">Nom complet *</label>
            <input id="name" type="text" name="name" value="{{ old('name') }}" required placeholder="ex: Jean Dupont" class="admin-field-input">
            @error('name')
                <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="email" class="block text-sm font-semibold text-[#e9c96b] mb-1">Adresse E-mail *</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required placeholder="ex: jean.dupont@example.com" class="admin-field-input">
            @error('email')
                <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="type_id" class="block text-sm font-semibold text-[#e9c96b] mb-1">Rôle & Niveau d'accès *</label>
            <select id="type_id" name="type_id" required class="admin-field-select">
                @foreach ($types as $t)
                    <option value="{{ $t->id }}" {{ old('type_id', 1) == $t->id ? 'selected' : '' }}>
                        {{ ucfirst($t->name) }} 
                        @if($t->id == 1) (Lecteur / Visiteur) @elseif($t->id == 2) (Proposer des livres) @elseif($t->id == 3) (Valider livres & catégories) @elseif($t->id == 4) (Accès complet administrateur) @endif
                    </option>
                @endforeach
            </select>
            @error('type_id')
                <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label for="password" class="block text-sm font-semibold text-[#e9c96b] mb-1">Mot de passe *</label>
                <input id="password" type="password" name="password" required placeholder="Minimum 8 caractères" class="admin-field-input">
                @error('password')
                    <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password_confirmation" class="block text-sm font-semibold text-[#e9c96b] mb-1">Confirmer le mot de passe *</label>
                <input id="password_confirmation" type="password" name="password_confirmation" required placeholder="Répéter le mot de passe" class="admin-field-input">
            </div>
        </div>

        <div class="pt-4 flex items-center justify-end gap-3 border-t border-[#4a2c17]">
            <a href="{{ route('admin.users.index') }}" class="text-sm text-gray-400 hover:text-white underline">Annuler</a>
            <button type="submit" class="plate px-6 py-2 text-lg font-bold">+ Créer le compte</button>
        </div>
    </form>
</div>
@endsection

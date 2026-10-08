@extends('layouts.admin')

@section('title', "Modifier {$user->name} — Bureau du Bibliothécaire")

@section('content')
<div class="mb-6 flex items-center justify-between">
    <div>
        <p class="font-garamond text-sm italic text-[#e9c96b]/70">
            <a href="{{ route('admin.users.index') }}" class="underline hover:text-white">Gestion des Comptes</a> &rsaquo; Édition #{{ $user->id }}
        </p>
        <h1 class="font-cinzel text-2xl font-bold sm:text-3xl text-[#e9c96b]">Modifier l'Utilisateur : {{ $user->name }}</h1>
    </div>

    <a href="{{ route('admin.users.index') }}" class="plate px-4 py-1.5 text-sm font-semibold hover:no-underline">
        ↩️ Retour à la liste
    </a>
</div>

<div class="card2 max-w-2xl mx-auto p-6 font-garamond">
    <form action="{{ route('admin.users.update', $user->id) }}" method="POST" class="space-y-4">
        @csrf
        @method('PUT')

        <div>
            <label for="name" class="block text-sm font-semibold text-[#e9c96b] mb-1">Nom complet *</label>
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

        <div>
            <label for="type_id" class="block text-sm font-semibold text-[#e9c96b] mb-1">Rôle & Niveau d'accès *</label>
            @if ($user->id === auth()->id())
                <input type="hidden" name="type_id" value="4">
                <div class="admin-field-input !bg-[#2a1a0e] text-[#e9c96b] font-mono text-sm flex items-center justify-between">
                    <span>👑 Administrateur (Votre compte principal)</span>
                    <span class="text-xs text-gray-400 italic">🔒 Rôle non modifiable sur soi-même</span>
                </div>
            @else
                <select id="type_id" name="type_id" required class="admin-field-select">
                    @foreach ($types as $t)
                        <option value="{{ $t->id }}" {{ old('type_id', $user->type_id) == $t->id ? 'selected' : '' }}>
                            {{ ucfirst($t->name) }} 
                            @if($t->id == 1) (Lecteur / Visiteur) @elseif($t->id == 2) (Proposer des livres) @elseif($t->id == 3) (Valider livres & catégories) @elseif($t->id == 4) (Accès complet administrateur) @endif
                        </option>
                    @endforeach
                </select>
            @endif
            @error('type_id')
                <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <div class="border-t border-[#4a2c17] pt-4 mt-2">
            <p class="text-sm font-bold text-[#e9c96b] mb-2">Changer le mot de passe (optionnel)</p>
            <p class="text-xs text-gray-400 mb-3">Laissez ces champs vides si vous ne souhaitez pas modifier le mot de passe actuel.</p>

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label for="password" class="block text-xs font-semibold text-gray-300 mb-1">Nouveau mot de passe</label>
                    <input id="password" type="password" name="password" placeholder="Minimum 8 caractères" class="admin-field-input">
                    @error('password')
                        <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password_confirmation" class="block text-xs font-semibold text-gray-300 mb-1">Confirmer le nouveau mot de passe</label>
                    <input id="password_confirmation" type="password" name="password_confirmation" placeholder="Répéter le mot de passe" class="admin-field-input">
                </div>
            </div>
        </div>

        <div class="pt-4 flex items-center justify-end gap-3 border-t border-[#4a2c17]">
            <a href="{{ route('admin.users.index') }}" class="text-sm text-gray-400 hover:text-white underline">Annuler</a>
            <button type="submit" class="plate px-6 py-2 text-lg font-bold">Enregistrer les modifications</button>
        </div>
    </form>
</div>

{{-- Modale de confirmation pour l'attribution du rôle Administrateur --}}
<div id="admin-role-modal" class="fixed inset-0 z-50 hidden flex items-center justify-center bg-black/80 p-4 backdrop-blur-sm" role="dialog" aria-modal="true">
    <div class="card2 max-w-md w-full p-6 text-[#f3e7cc] space-y-4 shadow-2xl border border-purple-800">
        <div class="flex items-center gap-3 border-b border-[#4a2c17] pb-3">
            <span class="text-3xl">🛡️</span>
            <div>
                <h3 class="font-cinzel text-xl font-bold text-purple-300">Privilège Administrateur</h3>
                <p class="text-xs text-gray-400">Confirmation de droits d'accès de Niveau 4</p>
            </div>
        </div>
        <p class="font-garamond text-base leading-snug">
            Vous vous préparez à accorder le rôle <b class="text-purple-300">Administrateur</b> à <b class="text-[#e9c96b]">{{ $user->name }}</b>.
        </p>
        <div class="rounded border border-purple-900/60 bg-purple-950/40 p-3 text-xs text-purple-200 space-y-1.5 font-garamond">
            <p class="font-bold uppercase tracking-wider text-purple-400">⚠️ Avertissement de sécurité :</p>
            <ul class="list-disc pl-4 space-y-1">
                <li>Cet utilisateur obtiendra un <b>accès total</b> à l'ensemble du système.</li>
                <li>Il aura le pouvoir de modifier ou <b>supprimer d'autres administrateurs</b>.</li>
                <li>Il aura accès aux données confidentielles.</li>
            </ul>
        </div>
        <div class="flex justify-end gap-3 pt-2">
            <button type="button" id="cancel-admin-modal-btn" class="px-4 py-1.5 text-sm underline text-gray-400 hover:text-white">Annuler</button>
            <button type="button" id="confirm-admin-modal-btn" class="plate px-5 py-1.5 text-sm font-bold bg-purple-800 text-purple-100 border-purple-900">Confirmer la nomination</button>
        </div>
    </div>
</div>

@push('scripts')
<script>
(function() {
    const form = document.querySelector('form[action*="admin/users"]');
    const select = document.getElementById('type_id');
    const modal = document.getElementById('admin-role-modal');
    const cancelBtn = document.getElementById('cancel-admin-modal-btn');
    const confirmBtn = document.getElementById('confirm-admin-modal-btn');
    
    if (!form || !select || !modal) return;
    
    const initialType = select.value;
    let confirmed = false;

    form.addEventListener('submit', function(e) {
        if (select.value == '4' && initialType != '4' && !confirmed) {
            e.preventDefault();
            modal.classList.remove('hidden');
        }
    });

    cancelBtn.addEventListener('click', function() {
        modal.classList.add('hidden');
    });

    confirmBtn.addEventListener('click', function() {
        confirmed = true;
        modal.classList.add('hidden');
        form.submit();
    });
})();
</script>
@endpush
@endsection

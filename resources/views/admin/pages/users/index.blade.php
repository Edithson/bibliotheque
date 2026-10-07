@extends('layouts.admin')

@section('title', 'Gestion des Comptes & Rôles — Bureau du Bibliothécaire')

@section('content')
<div class="mb-6 flex flex-wrap items-center justify-between gap-4">
    <div>
        <p class="font-garamond text-sm italic text-[#e9c96b]/70">Bureau du Bibliothécaire — Espace Administrateur</p>
        <h1 class="font-cinzel text-2xl font-bold sm:text-3xl text-[#e9c96b]">Gestion des Comptes Utilisateurs & Rôles</h1>
    </div>

    <a href="{{ route('admin.users.create') }}" class="plate whitespace-nowrap px-5 py-2 text-lg hover:no-underline inline-block">
        + Nouvel utilisateur
    </a>
</div>

@if (session('success'))
    <div class="mb-4 rounded border border-[#2f855a] bg-[#276749]/30 p-3 text-lg text-[#68d391]">
        ✓ {{ session('success') }}
    </div>
@endif

@if (session('error'))
    <div class="mb-4 rounded border border-[#e05a3f] bg-[#e05a3f]/20 p-3 text-lg text-red-200">
        ⚠️ {{ session('error') }}
    </div>
@endif

{{-- Barre de recherche et de filtres --}}
<form method="GET" action="{{ route('admin.users.index') }}" class="card2 mb-6 p-4 space-y-3 font-garamond text-sm">
    <div class="grid gap-3 sm:grid-cols-2 md:grid-cols-3">
        <div class="md:col-span-2">
            <label for="search" class="block text-xs font-bold text-[#e9c96b] mb-1">Recherche (Nom, Email...)</label>
            <input id="search" type="search" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Chercher un utilisateur..." class="admin-field-input !py-1 !text-sm">
        </div>

        <div>
            <label for="type_id" class="block text-xs font-bold text-[#e9c96b] mb-1">Rôle d'accès</label>
            <select id="type_id" name="type_id" class="admin-field-select !py-1 !text-sm">
                <option value="">-- Tous les rôles --</option>
                <option value="1" {{ ($filters['type_id'] ?? '') == 1 ? 'selected' : '' }}>Guest (Visiteur)</option>
                <option value="2" {{ ($filters['type_id'] ?? '') == 2 ? 'selected' : '' }}>Auteur</option>
                <option value="3" {{ ($filters['type_id'] ?? '') == 3 ? 'selected' : '' }}>Gérant</option>
                <option value="4" {{ ($filters['type_id'] ?? '') == 4 ? 'selected' : '' }}>Administrateur</option>
            </select>
        </div>
    </div>

    <div class="flex items-center justify-end gap-3 pt-1 border-t border-[#4a2c17]/60">
        @if (array_filter($filters ?? []))
            <a href="{{ route('admin.users.index') }}" class="text-xs text-[#e9c96b]/80 underline hover:text-white">Réinitialiser les filtres</a>
        @endif
        <button type="submit" class="plate px-4 py-1 text-sm font-bold">🔍 Filtrer les comptes</button>
    </div>
</form>

<div class="card2 overflow-x-auto">
    <table class="ledger w-full min-w-[750px] text-left font-garamond text-base">
        <thead>
            <tr>
                <th>Nom d'utilisateur</th>
                <th>Adresse E-mail</th>
                <th>Rôle Actuel</th>
                <th>Modifier le Rôle</th>
                <th>Inscrit le</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($users as $u)
                <tr>
                    <td class="font-semibold">{{ $u->name }}</td>
                    <td class="font-mono text-sm">{{ $u->email }}</td>
                    <td>
                        <span class="rounded px-2.5 py-1 text-xs font-bold font-mono uppercase
                            @if($u->type_id == 4) bg-purple-900 text-purple-200 
                            @elseif($u->type_id == 3) bg-blue-900 text-blue-200 
                            @elseif($u->type_id == 2) bg-amber-900 text-amber-200 
                            @else bg-gray-800 text-gray-300 @endif">
                            {{ $u->role }}
                        </span>
                    </td>
                    <td>
                        <form action="{{ route('admin.users.update-role', $u->id) }}" method="POST" class="flex items-center gap-2">
                            @csrf
                            @method('PATCH')
                            <select name="type_id" class="admin-field-select !py-1 !text-sm">
                                <option value="1" {{ $u->type_id == 1 ? 'selected' : '' }}>Guest (Visiteur)</option>
                                <option value="2" {{ $u->type_id == 2 ? 'selected' : '' }}>Auteur</option>
                                <option value="3" {{ $u->type_id == 3 ? 'selected' : '' }}>Gérant</option>
                                <option value="4" {{ $u->type_id == 4 ? 'selected' : '' }}>Administrateur</option>
                            </select>
                            <button type="submit" class="plate px-3 py-1 text-xs whitespace-nowrap">Mettre à jour</button>
                        </form>
                    </td>
                    <td class="font-mono text-xs text-gray-600">{{ $u->created_at->format('d/m/Y H:i') }}</td>
                    <td class="whitespace-nowrap">
                        <div class="flex items-center gap-1.5">
                            {{-- Icone Modification --}}
                            <a href="{{ route('admin.users.edit', $u->id) }}" class="inline-flex items-center justify-center p-1.5 text-blue-300 hover:text-white hover:bg-[#3b2514] rounded transition" title="Modifier l'utilisateur">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </a>

                            {{-- Icone Suppression --}}
                            @if ($u->id !== auth()->id())
                                <form action="{{ route('admin.users.destroy', $u->id) }}" method="POST" class="inline" onsubmit="return confirm('Supprimer définitivement le compte de {{ $u->name }} ?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex items-center justify-center p-1.5 text-[#e05a3f] hover:text-red-400 hover:bg-[#3b2514] rounded transition border-0 bg-transparent cursor-pointer" title="Supprimer l'utilisateur">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </form>
                            @else
                                <span class="text-xs italic text-gray-500 pl-1">(Votre compte)</span>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="py-8 text-center italic">Aucun utilisateur enregistré.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-6 flex justify-center">
    {{ $users->links() }}
</div>
@endsection

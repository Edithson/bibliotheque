@extends('layouts.admin')

@section('title', 'Bureau du Bibliothécaire — Administration')

@section('header_stats')
<div id="stats" class="mt-6 grid grid-cols-2 gap-3 sm:grid-cols-4">
    <div class="card2 p-4">
        <p class="text-sm text-[#e9c96b]/70">Livres au catalogue</p>
        <p class="font-cinzel text-3xl mt-1">{{ $totalBooks }}</p>
    </div>
    <div class="card2 p-4">
        <p class="text-sm text-[#e9c96b]/70">Livres gratuits</p>
        <p class="font-cinzel text-3xl mt-1 text-[#48bb78]">{{ $freeBooks }}</p>
    </div>
    <div class="card2 p-4">
        <p class="text-sm text-[#e9c96b]/70">Livres payants</p>
        <p class="font-cinzel text-3xl mt-1">{{ $paidBooks }}</p>
    </div>
    <div class="card2 p-4">
        <p class="text-sm text-[#e9c96b]/70">Publiés en ligne</p>
        <p class="font-cinzel text-3xl mt-1">{{ $publishedBooks }}</p>
    </div>
</div>
@endsection

@section('content')
@if (session('success'))
    <div class="mb-4 rounded border border-[#2f855a] bg-[#276749]/30 p-3 text-lg text-[#68d391]">
        ✓ {{ session('success') }}
    </div>
@endif

<div class="flex flex-wrap items-center justify-between gap-3">
    <p class="font-garamond text-lg italic text-[#e9c96b]/80">Registre général des ouvrages numériques</p>
    <a href="{{ route('admin.books.create') }}" class="plate whitespace-nowrap px-5 py-2 text-lg hover:no-underline inline-block">
        + Nouvel ouvrage numérique
    </a>
</div>

{{-- Zone de recherche et de filtres --}}
<form method="GET" action="{{ route('admin.books.index') }}" class="card2 mt-4 p-4 space-y-3 font-garamond text-sm">
    <div class="grid gap-3 sm:grid-cols-2 md:grid-cols-4">
        <div>
            <label for="search" class="block text-xs font-bold text-[#e9c96b] mb-1">Recherche (Titre, Auteur, Extrait...)</label>
            <input id="search" type="search" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Mots-clés..." class="admin-field-input !py-1 !text-sm">
        </div>

        <div>
            <label for="category_id" class="block text-xs font-bold text-[#e9c96b] mb-1">Catégorie</label>
            <select id="category_id" name="category_id" class="admin-field-select !py-1 !text-sm">
                <option value="">-- Toutes les catégories --</option>
                @foreach ($categories as $cat)
                    <option value="{{ $cat->id }}" {{ ($filters['category_id'] ?? '') == $cat->id ? 'selected' : '' }}>
                        {{ $cat->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label for="status" class="block text-xs font-bold text-[#e9c96b] mb-1">Statut de publication</label>
            <select id="status" name="status" class="admin-field-select !py-1 !text-sm">
                <option value="">-- Tous les statuts --</option>
                <option value="published" {{ ($filters['status'] ?? '') === 'published' ? 'selected' : '' }}>✓ Publiés uniquement</option>
                <option value="hidden" {{ ($filters['status'] ?? '') === 'hidden' ? 'selected' : '' }}>⏳ Masqués / À valider</option>
            </select>
        </div>

        @if (auth()->user()->isGerant())
            <div>
                <label for="user_id" class="block text-xs font-bold text-[#e9c96b] mb-1">Créateur / Auteur</label>
                <select id="user_id" name="user_id" class="admin-field-select !py-1 !text-sm">
                    <option value="">-- Tous les créateurs --</option>
                    @foreach ($creators as $creator)
                        <option value="{{ $creator->id }}" {{ ($filters['user_id'] ?? '') == $creator->id ? 'selected' : '' }}>
                            {{ $creator->name }} ({{ $creator->role }})
                        </option>
                    @endforeach
                </select>
            </div>
        @endif
    </div>

    <div class="flex items-center justify-end gap-3 pt-1 border-t border-[#4a2c17]/60">
        @if (array_filter($filters ?? []))
            <a href="{{ route('admin.books.index') }}" class="text-xs text-[#e9c96b]/80 underline hover:text-white">Réinitialiser les filtres</a>
        @endif
        <button type="submit" class="plate px-4 py-1 text-sm font-bold">🔍 Filtrer le registre</button>
    </div>
</form>

<div class="card2 mt-4 overflow-x-auto">
    <table class="ledger w-full min-w-[750px] text-left font-garamond text-base">
        <thead>
            <tr>
                <th>Titre & Slug</th>
                <th>Auteur</th>
                <th>Catégorie</th>
                <th>Tarif</th>
                <th>Pages</th>
                <th>Visibilité</th>
                <th>Fichier PDF / E-Book</th>
                <th>Créé par</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($books as $b)
                <tr>
                    <td>
                        <span class="font-semibold text-base block">{{ $b->title }}</span>
                        <span class="text-xs text-[#8b1e1e] font-mono block">slug: {{ $b->slug }}</span>
                    </td>
                    <td>{{ $b->author ?? 'Inconnu' }}</td>
                    <td>{{ $b->category ? $b->category->name : 'Général' }}</td>
                    <td>
                        @if ($b->price === 0)
                            <span class="px-2 py-0.5 rounded text-xs font-bold bg-[#276749] text-white">GRATUIT</span>
                        @else
                            <b>{{ number_format($b->price, 0, ',', ' ') }}</b> FCFA
                        @endif
                    </td>
                    <td>
                        <span class="font-mono text-xs">{{ $b->nbr_pages }} p.</span>
                    </td>
                    <td>
                        @if (auth()->user()->isGerant())
                            <form action="{{ route('admin.books.toggle-publish', $b->id) }}" method="POST" class="inline">
                                @csrf
                                @method('PATCH')
                                @if ($b->is_published)
                                    <button type="submit" class="px-2 py-0.5 rounded text-xs bg-[#2f855a] text-white cursor-pointer hover:bg-green-700" title="Cliquer pour masquer">
                                        ✓ Publié
                                    </button>
                                @else
                                    <button type="submit" class="px-2 py-0.5 rounded text-xs bg-[#9b2c2c] text-white cursor-pointer hover:bg-red-700" title="Cliquer pour valider & publier">
                                        ⏳ Masqué / À valider
                                    </button>
                                @endif
                            </form>
                        @else
                            @if ($b->is_published)
                                <span class="px-2 py-0.5 rounded text-xs bg-[#2f855a] text-white">Publié</span>
                            @else
                                <span class="px-2 py-0.5 rounded text-xs bg-[#9b2c2c] text-white">Masqué (Validation en attente)</span>
                            @endif
                        @endif
                    </td>
                    <td class="font-mono text-xs">
                        @if ($b->file_path)
                            <span class="text-[#2b6cb0]" title="{{ $b->file_path }}">📄 {{ basename($b->file_path) }}</span>
                        @else
                            <span class="italic text-gray-400">Aucun</span>
                        @endif
                    </td>
                    <td class="font-mono text-xs">
                        @if ($b->creator)
                            <span class="font-semibold text-[#2a190e]">{{ $b->creator->name }}</span>
                            <span class="block text-[10px] text-gray-600">({{ $b->creator->role }})</span>
                        @else
                            <span class="italic text-gray-500">Système</span>
                        @endif
                    </td>
                    <td class="whitespace-nowrap">
                        <div class="flex items-center gap-1.5">
                            {{-- Icone Consultation & Historique --}}
                            <a href="{{ route('admin.books.show', $b->id) }}" class="inline-flex items-center justify-center p-1.5 text-[#e9c96b] hover:text-white hover:bg-[#3b2514] rounded transition" title="Consulter la fiche & l'historique d'achats/téléchargements">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </a>
                            @if (auth()->user()->isGerant() || ! $b->is_published)
                                {{-- Icone Modification --}}
                                <a href="{{ route('admin.books.edit', $b->id) }}" class="inline-flex items-center justify-center p-1.5 text-blue-300 hover:text-white hover:bg-[#3b2514] rounded transition" title="Modifier l'ouvrage">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </a>
                                {{-- Icone Suppression --}}
                                <form action="{{ route('admin.books.destroy', $b->id) }}" method="POST" class="inline" onsubmit="return confirm('Retirer « {{ $b->title }} » du catalogue ?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex items-center justify-center p-1.5 text-[#e05a3f] hover:text-red-400 hover:bg-[#3b2514] rounded transition border-0 bg-transparent cursor-pointer" title="Supprimer l'ouvrage">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </form>
                            @else
                                <span class="p-1.5 text-gray-500 cursor-not-allowed" title="Les ouvrages publiés ne peuvent plus être modifiés ou supprimés par l'auteur.">🔒</span>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="py-10 text-center italic">Aucun ouvrage numérique ne correspond aux critères de recherche.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-6 flex justify-center">
    {{ $books->links() }}
</div>
@endsection

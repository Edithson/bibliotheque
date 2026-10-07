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

{{-- Barre de recherche et de filtres avancés --}}
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
    </div>

    <div class="flex items-center justify-end gap-3 pt-1 border-t border-[#4a2c17]/60">
        @if (array_filter($filters ?? []))
            <a href="{{ route('admin.books.index') }}" class="text-xs text-[#e9c96b]/80 underline hover:text-white">Réinitialiser les filtres</a>
        @endif
        <button type="submit" class="plate px-4 py-1 text-sm font-bold">🔍 Filtrer le registre</button>
    </div>
</form>

<div class="card2 mt-4 overflow-x-auto">
    <table class="ledger w-full min-w-[850px] text-left font-garamond text-base">
        <thead>
            <tr>
                <th>Titre & Slug</th>
                <th>Auteur</th>
                <th>Catégorie</th>
                <th>Tarif</th>
                <th>Visibilité</th>
                <th>Créé par</th>
                <th>Dernière modif.</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($books as $b)
                <tr>
                    <td>
                        <a href="{{ route('admin.books.show', $b->id) }}" class="font-semibold text-base block hover:underline text-[#e9c96b] hover:text-white" title="Cliquer pour voir la fiche détaillée & l'historique d'achats/téléchargements">
                            {{ $b->title }}
                        </a>
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
                        @if ($b->creator)
                            <span class="font-semibold text-[#2a190e]">{{ $b->creator->name }}</span>
                            <span class="block text-[10px] text-gray-600">({{ $b->creator->role }})</span>
                        @else
                            <span class="italic text-gray-500">Système</span>
                        @endif
                    </td>
                    <td class="font-mono text-xs">
                        @if ($b->updater)
                            <span class="text-[#2a190e]">{{ $b->updater->name }}</span>
                        @else
                            <span class="italic text-gray-500">-</span>
                        @endif
                        <span class="block text-[10px] text-gray-500">{{ $b->updated_at->format('d/m/Y') }}</span>
                    </td>
                    <td class="whitespace-nowrap space-x-1">
                        <a href="{{ route('admin.books.show', $b->id) }}" class="underline font-semibold text-amber-300 hover:text-white" title="Consulter la fiche & l'historique">🔍 Détails & Historique</a> · 
                        <a href="{{ route('admin.books.edit', $b->id) }}" class="underline font-semibold text-gray-200 hover:text-white">Modifier</a> · 
                        <form action="{{ route('admin.books.destroy', $b->id) }}" method="POST" class="inline" onsubmit="return confirm('Retirer « {{ $b->title }} » du catalogue ?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-[#e05a3f] underline bg-transparent border-0 p-0 cursor-pointer hover:text-red-400">Supprimer</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="py-10 text-center italic">Aucun ouvrage numérique ne correspond aux critères de recherche.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-6 flex justify-center">
    {{ $books->links() }}
</div>
@endsection

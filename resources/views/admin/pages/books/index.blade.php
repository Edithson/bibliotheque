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
                    <td class="whitespace-nowrap">
                        <a href="{{ route('admin.books.edit', $b->id) }}" class="underline font-semibold text-[#2a190e]">Modifier</a> · 
                        <form action="{{ route('admin.books.destroy', $b->id) }}" method="POST" class="inline" onsubmit="return confirm('Retirer « {{ $b->title }} » du catalogue ?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-[#8b1e1e] underline bg-transparent border-0 p-0 cursor-pointer">Supprimer</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="py-10 text-center italic">Aucun ouvrage numérique disponible dans le catalogue.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-6 flex justify-center">
    {{ $books->links() }}
</div>
@endsection

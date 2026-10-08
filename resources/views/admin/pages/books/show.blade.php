@extends('layouts.admin')

@section('title', "Fiche Technique — {$book->title} — Bureau du Bibliothécaire")

@section('content')
<div class="mb-6 flex flex-wrap items-center justify-between gap-4">
    <div>
        <p class="font-garamond text-sm italic text-[#e9c96b]/70">
            <a href="{{ route('admin.books.index') }}" class="underline hover:text-white">Registre</a> &rsaquo; Fiche de l'ouvrage #{{ $book->id }}
        </p>
        <h1 class="font-cinzel text-2xl font-bold sm:text-3xl text-[#e9c96b]">{{ $book->title }}</h1>
        <p class="font-garamond text-base italic text-gray-400">Par {{ $book->author ?? 'Auteur inconnu' }}</p>
    </div>

    <div class="flex flex-wrap items-center gap-3">
        @if (auth()->user()->isGerant() || ! $book->is_published)
            <a href="{{ route('admin.books.edit', $book->id) }}" class="plate px-4 py-1.5 text-sm font-semibold hover:no-underline">
                ✍️ Modifier l'ouvrage
            </a>
        @endif
        <a href="{{ route('books.download', $book->slug) }}" class="plate px-4 py-1.5 text-sm font-semibold text-green-300 hover:no-underline" target="_blank">
            📥 Télécharger PDF
        </a>
        <a href="{{ route('admin.books.index') }}" class="plate px-4 py-1.5 text-sm font-semibold text-gray-300 hover:no-underline">
            ↩️ Retour au registre
        </a>
    </div>
</div>

@if (session('success'))
    <div class="mb-6 rounded border border-[#2f855a] bg-[#276749]/30 p-3 text-lg text-[#68d391]">
        ✓ {{ session('success') }}
    </div>
@endif

{{-- Grille des Statistiques de l'ouvrage --}}
<div class="grid grid-cols-2 gap-4 sm:grid-cols-4 mb-6">
    <div class="card2 p-4">
        <p class="text-xs text-[#e9c96b]/70 uppercase font-mono">Total Téléchargements</p>
        <p class="font-cinzel text-3xl mt-1 text-[#e9c96b]">{{ $totalDownloads }}</p>
        <p class="text-[11px] text-gray-400 mt-1">Acquisitions globales</p>
    </div>
    <div class="card2 p-4">
        <p class="text-xs text-[#e9c96b]/70 uppercase font-mono">Membres vs Invités</p>
        <p class="font-cinzel text-2xl mt-1 text-blue-300">{{ $userDownloads }} <small class="text-xs text-gray-400">membres</small> / {{ $guestDownloads }} <small class="text-xs text-gray-400">invités</small></p>
        <p class="text-[11px] text-gray-400 mt-1">Répartition de l'audience</p>
    </div>
    <div class="card2 p-4">
        <p class="text-xs text-[#e9c96b]/70 uppercase font-mono">Tarif & Estimation</p>
        @if ($book->price === 0)
            <p class="font-cinzel text-2xl mt-1 text-[#48bb78]">GRATUIT</p>
            <p class="text-[11px] text-gray-400 mt-1">Accès libre</p>
        @else
            <p class="font-cinzel text-2xl mt-1 text-amber-300">{{ number_format($estimatedRevenue, 0, ',', ' ') }} <small class="text-xs">FCFA</small></p>
            <p class="text-[11px] text-gray-400 mt-1">{{ number_format($book->price, 0, ',', ' ') }} FCFA / unité</p>
        @endif
    </div>
    <div class="card2 p-4">
        <p class="text-xs text-[#e9c96b]/70 uppercase font-mono">Visibilité Catalogue</p>
        @if ($book->is_published)
            <p class="font-cinzel text-xl mt-2 font-bold text-green-400">✓ En Ligne (Publié)</p>
        @else
            <p class="font-cinzel text-xl mt-2 font-bold text-amber-400">⏳ Masqué / Attente</p>
        @endif
        <p class="text-[11px] text-gray-400 mt-1">{{ $book->nbr_pages }} page(s) &bull; {{ $book->publish_year ?? '2026' }}</p>
    </div>
</div>

<div class="grid gap-6 md:grid-cols-3 mb-8">
    {{-- Colonne de gauche : Fiche Technique & Métadonnées --}}
    <div class="card2 p-5 space-y-4 md:col-span-1 font-garamond">
        <h2 class="font-cinzel text-xl font-bold text-[#e9c96b] border-b border-[#4a2c17] pb-2">Fiche Technique</h2>
        
        <div class="space-y-2 text-sm">
            <div>
                <span class="text-xs font-mono uppercase text-[#e9c96b]/60 block">Identifiant & Slug</span>
                <span class="font-mono text-sm font-semibold text-gray-200">#{{ $book->id }} ({{ $book->slug }})</span>
            </div>
            <div>
                <span class="text-xs font-mono uppercase text-[#e9c96b]/60 block">Catégorie</span>
                <span class="text-base text-gray-100 font-semibold">{{ $book->category ? $book->category->name : 'Général' }}</span>
            </div>
            <div>
                <span class="text-xs font-mono uppercase text-[#e9c96b]/60 block">Prix de l'ouvrage</span>
                <span class="text-base font-semibold {{ $book->price === 0 ? 'text-green-400' : 'text-amber-300' }}">
                    {{ $book->price === 0 ? 'Exemplaire Gratuit' : number_format($book->price, 0, ',', ' ').' FCFA' }}
                </span>
            </div>
            <div>
                <span class="text-xs font-mono uppercase text-[#e9c96b]/60 block">Format & Édition</span>
                <span class="text-sm text-gray-300">{{ $book->nbr_pages }} pages &bull; Parution : {{ $book->publish_year ?? 2026 }}</span>
            </div>
            <div>
                <span class="text-xs font-mono uppercase text-[#e9c96b]/60 block">Fichier PDF lié</span>
                @if ($book->file_path)
                    <span class="font-mono text-xs text-green-300 break-all">✓ {{ $book->file_path }}</span>
                @else
                    <span class="italic text-xs text-gray-400">Aucun fichier PDF téléversé (PDF généré à la volée)</span>
                @endif
            </div>
        </div>

        <h3 class="font-cinzel text-lg font-bold text-[#e9c96b] border-b border-[#4a2c17] pt-2 pb-1">Traçabilité & Historique</h3>
        <div class="space-y-2 text-xs font-mono text-gray-300">
            <div>
                <span class="text-gray-400 block">Créé par :</span>
                @if ($book->creator)
                    <span class="font-semibold text-gray-100">{{ $book->creator->name }}</span> ({{ $book->creator->role }})
                @else
                    <span class="italic text-gray-400">Système / Seeder</span>
                @endif
                <span class="block text-[11px] text-gray-400">Le {{ $book->created_at->format('d/m/Y à H:i') }}</span>
            </div>
            <div>
                <span class="text-gray-400 block">Dernière modification par :</span>
                @if ($book->updater)
                    <span class="font-semibold text-gray-100">{{ $book->updater->name }}</span> ({{ $book->updater->role }})
                @else
                    <span class="italic text-gray-400">-</span>
                @endif
                <span class="block text-[11px] text-gray-400">Le {{ $book->updated_at->format('d/m/Y à H:i') }}</span>
            </div>
        </div>
    </div>

    {{-- Colonne de droite : Synopsis & Prévisualisation de l'extrait --}}
    <div class="card2 p-5 space-y-4 md:col-span-2 font-garamond">
        <h2 class="font-cinzel text-xl font-bold text-[#e9c96b] border-b border-[#4a2c17] pb-2">Synopsis & Consultation</h2>
        
        <div>
            <h3 class="font-cinzel text-sm font-bold text-[#e9c96b]/80 uppercase mb-1">Résumé de l'ouvrage</h3>
            <p class="text-base leading-relaxed text-gray-200 bg-[#1e130b]/60 p-3.5 rounded border border-[#3e2617]/50 italic">
                {{ $book->description ?? 'Aucun synopsis disponible.' }}
            </p>
        </div>

        <div>
            <h3 class="font-cinzel text-sm font-bold text-[#e9c96b]/80 uppercase mb-1">Extrait Numérisé</h3>
            @if ($book->excerpt)
                <div class="paper relative p-4 rounded text-[#2a190e]">
                    <p class="font-garamond text-lg leading-relaxed first-letter:float-left first-letter:mr-2 first-letter:font-cinzel first-letter:text-5xl first-letter:leading-none first-letter:text-[#8b1e1e]">
                        {!! nl2br(e(str_replace('|', "\n\n", $book->excerpt))) !!}
                    </p>
                </div>
            @else
                <p class="italic text-sm text-gray-400">Aucun extrait n'a été fourni pour cet ouvrage.</p>
            @endif
        </div>
    </div>
</div>

{{-- Historique des Téléchargements et Acquisitions --}}
<div class="card2 p-5 space-y-4">
    <div class="flex flex-wrap items-center justify-between gap-2 border-b border-[#4a2c17] pb-3">
        <div>
            <h2 class="font-cinzel text-xl font-bold text-[#e9c96b]">Historique des Achats & Téléchargements</h2>
            <p class="font-garamond text-sm italic text-gray-400">Journal chronologique des consultations et accès de cet ouvrage numérique.</p>
        </div>
        <span class="plate px-3 py-1 text-xs font-mono">
            Total : {{ $downloads->total() }} entrée(s)
        </span>
    </div>

    <div class="overflow-x-auto">
        <table class="ledger w-full min-w-[700px] text-left font-garamond text-base">
            <thead>
                <tr>
                    <th>Date & Heure</th>
                    <th>Utilisateur / Lecteur</th>
                    <th>Adresse IP</th>
                    <th>Appareil / User-Agent</th>
                    <th>Statut d'Accès</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($downloads as $d)
                    <tr>
                        <td class="font-mono text-xs text-gray-300">
                            {{ $d->created_at->format('d/m/Y à H:i:s') }}
                            <span class="block text-[10px] text-gray-500">{{ $d->created_at->diffForHumans() }}</span>
                        </td>
                        <td>
                            @if ($d->user)
                                <span class="font-semibold text-[#2a190e] block">{{ $d->user->name }}</span>
                                <span class="font-mono text-xs text-gray-600 block">{{ $d->user->email }} ({{ $d->user->role }})</span>
                            @else
                                <span class="font-mono text-xs italic text-gray-600">👤 Visiteur Anonyme (Invité)</span>
                            @endif
                        </td>
                        <td class="font-mono text-xs text-gray-600">
                            {{ $d->ip_address ?? 'Inconnue' }}
                        </td>
                        <td class="font-mono text-[11px] text-gray-500 max-w-[250px] truncate" title="{{ $d->user_agent }}">
                            {{ $d->user_agent ? Str::limit($d->user_agent, 45) : 'Non renseigné' }}
                        </td>
                        <td>
                            @if ($book->price === 0)
                                <span class="px-2 py-0.5 rounded text-xs font-bold bg-[#276749] text-white">
                                    📥 Gratuit
                                </span>
                            @else
                                <span class="px-2 py-0.5 rounded text-xs font-bold bg-amber-800 text-amber-100">
                                    💳 Achat & Téléchargement ({{ number_format($book->price, 0, ',', ' ') }} FCFA)
                                </span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="py-8 text-center italic text-gray-400">
                            Aucun téléchargement ou achat enregistré pour cet ouvrage jusqu me présent.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4 flex justify-center">
        {{ $downloads->links() }}
    </div>
</div>
@endsection

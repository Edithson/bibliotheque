@extends('layouts.admin')

@section('title', 'Registre des Téléchargements — Administration')

@section('header_stats')
<div class="mt-6 grid grid-cols-2 gap-3 sm:grid-cols-4 font-garamond">
    <div class="card2 p-4">
        <p class="text-xs text-[#e9c96b]/70 uppercase tracking-wider">Total Téléchargements</p>
        <p class="font-cinzel text-3xl mt-1 text-[#e9c96b] font-bold">{{ number_format($totalDownloads, 0, ',', ' ') }}</p>
    </div>
    <div class="card2 p-4">
        <p class="text-xs text-[#e9c96b]/70 uppercase tracking-wider">Ce Mois-ci</p>
        <p class="font-cinzel text-3xl mt-1 text-[#68d391] font-bold">{{ number_format($downloadsThisMonth, 0, ',', ' ') }}</p>
    </div>
    <div class="card2 p-4">
        <p class="text-xs text-[#e9c96b]/70 uppercase tracking-wider">Gratuits / Payants</p>
        <p class="font-cinzel text-2xl mt-1 font-bold">
            <span class="text-green-400">{{ $freeDownloads }}</span> <span class="text-xs text-gray-400 font-sans">/</span> <span class="text-amber-300">{{ $paidDownloads }}</span>
        </p>
    </div>
    <div class="card2 p-4">
        <p class="text-xs text-[#e9c96b]/70 uppercase tracking-wider">Livre le plus lu</p>
        @if ($topBook)
            <p class="font-cinzel text-sm mt-1 text-amber-200 truncate font-bold" title="{{ $topBook->title }}">
                🏆 {{ $topBook->title }}
            </p>
            <p class="text-xs text-gray-400">({{ $topBook->downloads_count }} téléchargements)</p>
        @else
            <p class="font-cinzel text-sm mt-1 text-gray-400">Aucun</p>
        @endif
    </div>
</div>
@endsection

@section('content')
<main class="mx-auto max-w-6xl space-y-6 font-garamond">
    <div class="flex flex-wrap items-center justify-between gap-4 border-b border-[#4a2c17] pb-4">
        <div>
            <p class="font-garamond text-sm italic text-[#e9c96b]/70">Espace Statistiques & Audit</p>
            <h1 class="font-cinzel text-2xl font-bold sm:text-3xl text-[#e9c96b]">Registre des Téléchargements</h1>
        </div>

        <a href="{{ route('admin.downloads.export', request()->query()) }}" class="plate px-5 py-2 text-base font-bold flex items-center gap-2 hover:no-underline shadow">
            📥 Exporter en CSV
        </a>
    </div>

    {{-- Filtres de recherche --}}
    <form method="GET" action="{{ route('admin.downloads.index') }}" class="card2 p-4 space-y-3 font-garamond text-sm">
        <div class="grid gap-3 sm:grid-cols-2 md:grid-cols-5">
            <div class="md:col-span-2">
                <label for="search" class="block text-xs font-bold text-[#e9c96b] mb-1">Recherche (Livre, Lecteur, IP)</label>
                <input id="search" type="search" name="search" value="{{ request('search') }}" placeholder="Titre, auteur, nom ou email..." class="admin-field-input !py-1 !text-sm">
            </div>

            <div>
                <label for="category_id" class="block text-xs font-bold text-[#e9c96b] mb-1">Catégorie</label>
                <select id="category_id" name="category_id" class="admin-field-select !py-1 !text-sm" onchange="this.form.submit()">
                    <option value="">-- Toutes --</option>
                    @foreach ($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="price_type" class="block text-xs font-bold text-[#e9c96b] mb-1">Tarification</label>
                <select id="price_type" name="price_type" class="admin-field-select !py-1 !text-sm" onchange="this.form.submit()">
                    <option value="">-- Tous les tarifs --</option>
                    <option value="free" {{ request('price_type') === 'free' ? 'selected' : '' }}>✓ Gratuits uniquement</option>
                    <option value="paid" {{ request('price_type') === 'paid' ? 'selected' : '' }}>💰 Payants uniquement</option>
                </select>
            </div>

            <div>
                <label for="date_from" class="block text-xs font-bold text-[#e9c96b] mb-1">Du (Date)</label>
                <input id="date_from" type="date" name="date_from" value="{{ request('date_from') }}" class="admin-field-input !py-1 !text-sm">
            </div>
        </div>

        <div class="flex items-center justify-between gap-3 pt-2 border-t border-[#4a2c17]/60">
            <div class="text-xs italic text-[#e9c96b]/80">
                Total trouvé : {{ $downloads->total() }} log(s)
            </div>

            <div class="flex items-center gap-3">
                @if (request()->hasAny(['search', 'category_id', 'price_type', 'date_from', 'date_to']))
                    <a href="{{ route('admin.downloads.index') }}" class="text-xs text-[#e9c96b]/80 underline hover:text-white">Réinitialiser</a>
                @endif
                <button type="submit" class="plate px-4 py-1 text-sm font-bold">🔍 Filtrer</button>
            </div>
        </div>
    </form>

    {{-- Tableau des téléchargements --}}
    <div class="card2 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-[#1c0d06] font-cinzel text-[#e9c96b] border-b border-[#4a2c17]">
                    <tr>
                        <th class="px-4 py-3">Date & Heure</th>
                        <th class="px-4 py-3">Ouvrage Numérique</th>
                        <th class="px-4 py-3">Catégorie</th>
                        <th class="px-4 py-3">Tarif</th>
                        <th class="px-4 py-3">Lecteur / Compte</th>
                        <th class="px-4 py-3 text-right">Adresse IP</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#4a2c17]">
                    @forelse ($downloads as $download)
                        <tr class="hover:bg-[#3d1e10]/40 transition">
                            <td class="px-4 py-3 whitespace-nowrap text-xs text-amber-200/90 font-mono">
                                {{ $download->created_at?->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-4 py-3">
                                @if ($download->book)
                                    <a href="{{ route('admin.books.show', $download->book) }}" class="font-bold text-[#e9c96b] hover:underline">
                                        {{ $download->book->title }}
                                    </a>
                                    <div class="text-xs text-gray-400">par {{ $download->book->author }}</div>
                                @else
                                    <span class="italic text-gray-500">Ouvrage supprimé</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-xs text-amber-100">
                                {{ $download->book?->category?->name ?? 'Général' }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                @if ($download->book?->price === 0)
                                    <span class="inline-flex items-center rounded px-2 py-0.5 text-xs font-bold bg-[#276749] text-white">
                                        GRATUIT
                                    </span>
                                @elseif ($download->book)
                                    <span class="font-bold text-amber-200">
                                        {{ number_format($download->book->price, 0, ',', ' ') }} FCFA
                                    </span>
                                @else
                                    <span class="text-xs text-gray-500">N/A</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                @if ($download->user)
                                    <div class="font-semibold text-white">{{ $download->user->name }}</div>
                                    <div class="text-xs text-gray-400 font-mono">{{ $download->user->email }}</div>
                                @else
                                    <span class="inline-flex items-center rounded px-2 py-0.5 text-xs bg-[#4a2c17] text-gray-300 italic">
                                        👤 Visiteur Invité
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right font-mono text-xs text-gray-400 whitespace-nowrap">
                                {{ $download->ip_address ?? '—' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-gray-400 italic">
                                Aucun historique de téléchargement enregistré ne correspond aux critères.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if ($downloads->hasPages())
            <div class="p-4 border-t border-[#4a2c17] bg-[#1c0d06] text-white">
                {{ $downloads->links() }}
            </div>
        @endif
    </div>
</main>
@endsection

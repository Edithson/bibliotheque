@extends('layouts.admin')

@section('title', 'Gestion des Catégories — Bureau du Bibliothécaire')

@section('content')
<div class="mb-6 flex items-center justify-between">
    <div>
        <p class="font-garamond text-sm italic text-[#e9c96b]/70">Bureau du Bibliothécaire</p>
        <h1 class="font-cinzel text-2xl font-bold sm:text-3xl text-[#e9c96b]">Gestion des Catégories de Livres</h1>
    </div>
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

<div class="grid gap-8 md:grid-cols-3">
    {{-- Formulaire de création / édition --}}
    <div class="card2 p-6">
        <h2 class="font-cinzel text-xl font-bold text-[#e9c96b] mb-4">Ajouter une Catégorie</h2>
        <form action="{{ route('admin.categories.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label for="name" class="block font-garamond text-sm font-semibold text-[#e9c96b]">Nom de la catégorie *</label>
                <input id="name" type="text" name="name" required placeholder="ex: Poésie Moderne" class="admin-field-input mt-1">
                @error('name')
                    <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit" class="plate w-full py-2 text-lg">+ Ajouter la catégorie</button>
        </form>
    </div>

    {{-- Liste des catégories --}}
    <div class="md:col-span-2">
        <div class="card2 overflow-x-auto">
            <table class="ledger w-full text-left font-garamond text-base">
                <thead>
                    <tr>
                        <th>Nom de la catégorie</th>
                        <th>Slug</th>
                        <th>Nombre de livres</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($categories as $cat)
                        <tr>
                            <td>
                                <form action="{{ route('admin.categories.update', $cat->id) }}" method="POST" class="flex items-center gap-2">
                                    @csrf
                                    @method('PUT')
                                    <input type="text" name="name" value="{{ $cat->name }}" class="admin-field-input !py-1 !text-sm" required>
                                    <button type="submit" class="text-xs plate px-2 py-1">Enregistrer</button>
                                </form>
                            </td>
                            <td class="font-mono text-xs text-[#8b1e1e]">{{ $cat->slug }}</td>
                            <td class="font-mono text-sm font-bold">{{ $cat->books_count }} livre(s)</td>
                            <td>
                                <form action="{{ route('admin.categories.destroy', $cat->id) }}" method="POST" class="inline" onsubmit="return confirm('Supprimer la catégorie « {{ $cat->name }} » ?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-[#8b1e1e] underline bg-transparent border-0 p-0 cursor-pointer text-sm">Supprimer</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-8 text-center italic">Aucune catégorie enregistrée.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

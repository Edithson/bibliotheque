@extends('layouts.admin')

@section('title', 'Nouveau Livre Numérique — Bureau du Bibliothécaire')

@section('content')
<div class="mb-6 flex items-center justify-between">
    <div>
        <p class="font-garamond text-sm italic text-[#e9c96b]/70">Bureau du Bibliothécaire</p>
        <h1 class="font-cinzel text-2xl font-bold sm:text-3xl text-[#e9c96b]">Ajouter un nouvel ouvrage numérique</h1>
    </div>
    <a href="{{ route('admin.books.index') }}" class="font-garamond text-base italic text-[#e9c96b]/80 underline hover:text-[#e9c96b]">← Annuler et revenir au catalogue</a>
</div>

@if (!auth()->user()->isGerant())
    <div class="mb-6 rounded border border-amber-500/50 bg-amber-950/40 p-4 text-[#e9c96b]">
        ℹ️ <b>Information Auteur :</b> En tant qu'auteur, les ouvrages ajoutés restent masqués (non publiés) par défaut jusqu'à validation par un gérant ou un administrateur.
    </div>
@endif

@if ($errors->any())
    <div class="mb-6 rounded border border-[#e05a3f] bg-[#e05a3f]/10 p-4 text-white">
        <p class="font-bold text-lg mb-1">Veuillez corriger les erreurs ci-dessous :</p>
        <ul class="list-disc pl-5 text-sm space-y-1">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form action="{{ route('admin.books.store') }}" method="POST" enctype="multipart/form-data" class="card2 p-6 sm:p-10 space-y-6">
    @csrf

    <div class="grid gap-6 sm:grid-cols-2">
        <label class="sm:col-span-2">
            <span class="block text-sm font-semibold text-[#e9c96b] mb-1">Titre de l'ouvrage *</span>
            <input name="title" value="{{ old('title') }}" class="admin-field-input" placeholder="ex: Le Cartographe des Brumes" required>
        </label>

        <label>
            <span class="block text-sm font-semibold text-[#e9c96b] mb-1">Auteur *</span>
            <input name="author" value="{{ old('author', auth()->user()->name) }}" class="admin-field-input" placeholder="ex: Élise Marchand" required>
        </label>

        <label>
            <span class="block text-sm font-semibold text-[#e9c96b] mb-1">Catégorie *</span>
            <select name="category_id" class="admin-field-select" required>
                <option value="">-- Sélectionner une catégorie --</option>
                @foreach ($categories as $cat)
                    <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                        {{ $cat->name }}
                    </option>
                @endforeach
            </select>
        </label>

        <label>
            <span class="block text-sm font-semibold text-[#e9c96b] mb-1">Prix en FCFA (Saisir 0 pour un livre GRATUIT) *</span>
            <input type="number" min="0" name="price" value="{{ old('price', 0) }}" class="admin-field-input" required>
        </label>

        <label>
            <span class="block text-sm font-semibold text-[#e9c96b] mb-1">Couleur de couverture (sur le rayon)</span>
            <input type="color" name="cover_color" value="{{ old('cover_color', '#5b1a1f') }}" class="admin-field-input h-11 p-1">
        </label>

        <label>
            <span class="block text-sm font-semibold text-[#e9c96b] mb-1">Année de publication</span>
            <input type="number" name="publish_year" value="{{ old('publish_year', 2026) }}" class="admin-field-input">
        </label>

        @if (auth()->user()->isGerant())
            <div class="flex items-center pt-6">
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" name="is_published" value="1" {{ old('is_published', '1') ? 'checked' : '' }} class="h-5 w-5 rounded accent-[#b98a2e]">
                    <span class="text-base text-[#e9c96b]">Publier immédiatement cet ouvrage sur la boutique</span>
                </label>
            </div>
        @endif

        <label class="sm:col-span-2 rounded border border-[#c7a96f]/40 bg-[#fffaf0]/5 p-4">
            <span class="block text-base font-bold text-[#e9c96b] mb-1">Fichier du Livre Numérique (PDF, EPUB, MOBI, TXT, DOCX - max 30 Mo)</span>
            <span class="block text-xs italic text-[#e9c96b]/70 mb-2">Le nombre de pages sera automatiquement détecté et le fichier sera stocké dans l'espace privé sécurisé.</span>
            <input type="file" name="book_file" accept=".pdf,.epub,.mobi,.txt,.docx" class="admin-field-input text-base bg-white p-2">
        </label>

        <label class="sm:col-span-2">
            <span class="block text-sm font-semibold text-[#e9c96b] mb-1">Description / Résumé</span>
            <textarea name="description" rows="4" class="admin-field-textarea" placeholder="Présentation succincte de l'ouvrage...">{{ old('description') }}</textarea>
        </label>

        <label class="sm:col-span-2">
            <span class="block text-sm font-semibold text-[#e9c96b] mb-1">Extrait pour feuilletage (Séparer les pages de l'extrait par le symbole |)</span>
            <textarea name="excerpt" rows="4" class="admin-field-textarea" placeholder="Page 1 de l'extrait...|Page 2 de l'extrait...">{{ old('excerpt') }}</textarea>
        </label>
    </div>

    <div class="flex justify-end gap-4 pt-4 border-t border-[#4a2c17]">
        <a href="{{ route('admin.books.index') }}" class="px-6 py-2.5 font-garamond text-lg italic underline text-[#e9c96b]/80 hover:text-[#e9c96b]">Annuler</a>
        <button type="submit" class="plate px-8 py-2.5 text-xl">Enregistrer et ajouter l'ouvrage</button>
    </div>
</form>
@endsection

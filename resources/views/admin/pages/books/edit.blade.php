@extends('layouts.admin')

@section('title', "Modifier {$book->title} — Bureau du Bibliothécaire")

@section('content')
<div class="mb-6 flex items-center justify-between">
    <div>
        <p class="font-garamond text-sm italic text-[#e9c96b]/70">Bureau du Bibliothécaire</p>
        <h1 class="font-cinzel text-2xl font-bold sm:text-3xl text-[#e9c96b]">Modifier l'ouvrage : {{ $book->title }}</h1>
    </div>
    <a href="{{ route('admin.books.index') }}" class="font-garamond text-base italic text-[#e9c96b]/80 underline hover:text-[#e9c96b]">← Revenir au catalogue</a>
</div>

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

<form action="{{ route('admin.books.update', $book->id) }}" method="POST" enctype="multipart/form-data" class="card2 p-6 sm:p-10 space-y-6">
    @csrf
    @method('PUT')

    <div class="grid gap-6 sm:grid-cols-2">
        <label class="sm:col-span-2">
            <span class="block text-sm font-semibold text-[#e9c96b] mb-1">Titre de l'ouvrage *</span>
            <input name="title" value="{{ old('title', $book->title) }}" class="admin-field-input" required>
        </label>

        <label>
            <span class="block text-sm font-semibold text-[#e9c96b] mb-1">Auteur *</span>
            <input name="author" value="{{ old('author', $book->author) }}" class="admin-field-input" required>
        </label>

        <label>
            <span class="block text-sm font-semibold text-[#e9c96b] mb-1">Catégorie *</span>
            <select name="category_id" class="admin-field-select" required>
                @foreach ($categories as $cat)
                    <option value="{{ $cat->id }}" {{ old('category_id', $book->category_id) == $cat->id ? 'selected' : '' }}>
                        {{ $cat->name }}
                    </option>
                @endforeach
            </select>
        </label>

        <label>
            <span class="block text-sm font-semibold text-[#e9c96b] mb-1">Prix en FCFA (0 = GRATUIT) *</span>
            <input type="number" min="0" name="price" value="{{ old('price', $book->price) }}" class="admin-field-input" required>
        </label>

        <label>
            <span class="block text-sm font-semibold text-[#e9c96b] mb-1">Couleur de couverture</span>
            <input type="color" name="cover_color" value="{{ old('cover_color', $book->cover_color ?? '#5b1a1f') }}" class="admin-field-input h-11 p-1">
        </label>

        <label>
            <span class="block text-sm font-semibold text-[#e9c96b] mb-1">Année de publication</span>
            <input type="number" name="publish_year" value="{{ old('publish_year', $book->publish_year) }}" class="admin-field-input">
        </label>

        @if (auth()->user()->isGerant())
            <div class="flex items-center pt-6">
                <input type="hidden" name="is_published" value="0">
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" name="is_published" value="1" {{ old('is_published', $book->is_published) ? 'checked' : '' }} class="h-5 w-5 rounded accent-[#b98a2e]">
                    <span class="text-base text-[#e9c96b]">Publier cet ouvrage sur le site web</span>
                </label>
            </div>
        @endif

        <div class="sm:col-span-2 rounded border border-[#c7a96f]/40 bg-[#fffaf0]/5 p-4 space-y-2">
            <span class="block text-base font-bold text-[#e9c96b]">Remplacer le fichier du Livre Numérique (PDF, EPUB, MOBI, TXT, DOCX - max 30 Mo)</span>
            <p class="text-xs italic text-[#e9c96b]/80">
                Fichier actuel : 
                @if ($book->file_path)
                    <span class="font-mono text-[#68d391]">📄 {{ basename($book->file_path) }} ({{ $book->nbr_pages }} pages détectées)</span>
                @else
                    <span class="italic text-gray-400">Aucun fichier enregistré</span>
                @endif
            </p>
            <input type="file" name="book_file" accept=".pdf,.epub,.mobi,.txt,.docx" class="admin-field-input text-base bg-white p-2">
        </div>

        <label class="sm:col-span-2">
            <span class="block text-sm font-semibold text-[#e9c96b] mb-1">Description / Résumé</span>
            <textarea name="description" rows="4" class="admin-field-textarea">{{ old('description', $book->description) }}</textarea>
        </label>

        <label class="sm:col-span-2">
            <span class="block text-sm font-semibold text-[#e9c96b] mb-1">Extrait pour feuilletage (Séparer les pages par |)</span>
            <textarea name="excerpt" rows="4" class="admin-field-textarea">{{ old('excerpt', $book->excerpt) }}</textarea>
        </label>
    </div>

    <div class="flex justify-end gap-4 pt-4 border-t border-[#4a2c17]">
        <a href="{{ route('admin.books.index') }}" class="px-6 py-2.5 font-garamond text-lg italic underline text-[#e9c96b]/80 hover:text-[#e9c96b]">Annuler</a>
        <button type="submit" class="plate px-8 py-2.5 text-xl">Enregistrer les modifications</button>
    </div>
</form>
@endsection

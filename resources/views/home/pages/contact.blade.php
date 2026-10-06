@extends('layouts.app')

@section('title', 'Nous Contacter — La Bibliothèque des Mots')

@section('content')
<main class="mx-auto max-w-xl px-4 py-12">
    <div class="card2 p-6 sm:p-10 space-y-6">
        <div class="border-b border-[#4a2c17] pb-4 text-center">
            <p class="font-garamond text-sm italic text-[#e9c96b]/70">Correspondance avec le Bibliothécaire</p>
            <h1 class="font-cinzel text-2xl font-bold text-[#e9c96b] sm:text-3xl">Nous Contacter</h1>
        </div>

        @if (session('success'))
            <div class="rounded border border-[#2f855a] bg-[#276749]/30 p-4 text-base text-[#68d391]">
                ✓ {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="rounded border border-[#e05a3f] bg-[#e05a3f]/10 p-3 text-sm text-red-200">
                <ul class="list-disc pl-4 space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('contact.send') }}" class="space-y-5 font-garamond text-base">
            @csrf

            <div>
                <label for="name" class="block font-semibold text-[#e9c96b] mb-1">Votre Nom & Prénom *</label>
                <input id="name" type="text" name="name" value="{{ old('name', auth()->user()?->name) }}" required class="admin-field-input">
            </div>

            <div>
                <label for="email" class="block font-semibold text-[#e9c96b] mb-1">Adresse E-mail *</label>
                <input id="email" type="email" name="email" value="{{ old('email', auth()->user()?->email) }}" required class="admin-field-input">
            </div>

            <div>
                <label for="subject" class="block font-semibold text-[#e9c96b] mb-1">Sujet du message *</label>
                <select id="subject" name="subject" class="admin-field-select" required>
                    <option value="general" {{ old('subject', $selectedSubject) === 'general' ? 'selected' : '' }}>Question Générale</option>
                    <option value="author_request" {{ old('subject', $selectedSubject) === 'author_request' ? 'selected' : '' }}>✍️ Demande pour devenir Auteur (Proposer un livre)</option>
                    <option value="support" {{ old('subject', $selectedSubject) === 'support' ? 'selected' : '' }}>Support Technique / Téléchargement</option>
                </select>
            </div>

            <div>
                <label for="message" class="block font-semibold text-[#e9c96b] mb-1">Votre Message *</label>
                <textarea id="message" name="message" rows="6" required class="admin-field-textarea" placeholder="Rédigez votre message ici...">{{ old('message', $prefilledMessage) }}</textarea>
            </div>

            <div class="pt-2">
                <button type="submit" class="plate w-full py-2.5 text-xl">Envoyer la correspondance</button>
            </div>
        </form>

        <div class="pt-4 text-center">
            <a href="{{ url('/') }}" class="font-garamond text-sm italic text-[#e9c96b]/80 underline hover:text-[#e9c96b]">← Retourner au rayon principal</a>
        </div>
    </div>
</main>
@endsection

@extends('layouts.admin')

@section('title', 'Détail du Message — Administration')

@section('content')
@php
    $replySubject = "Re: " . $contact->subject_label . " — La Bibliothèque des Mots";
    $replyBody = "Bonjour " . $contact->name . ",\n\nMerci pour votre message concernant : " . $contact->subject_label . ".\n\n\n-----------------------------------\nCitation de votre correspondance du " . $contact->created_at->format('d/m/Y à H:i') . " :\n\"" . $contact->message . "\"\n-----------------------------------\nCordialement,\nLe Bibliothécaire — La Bibliothèque des Mots";
    $mailtoUrl = "mailto:" . rawurlencode($contact->email) . "?subject=" . rawurlencode($replySubject) . "&body=" . rawurlencode($replyBody);
@endphp

<main class="mx-auto max-w-4xl space-y-6 font-garamond">
    <div class="flex flex-wrap items-center justify-between gap-4 border-b border-[#4a2c17] pb-4">
        <div>
            <p class="font-garamond text-sm italic text-[#e9c96b]/70">Correspondance avec les lecteurs</p>
            <h1 class="font-cinzel text-2xl font-bold sm:text-3xl text-[#e9c96b]">Lecture du Message</h1>
        </div>

        <a href="{{ route('admin.contacts.index') }}" class="plate px-4 py-1.5 text-sm font-semibold hover:no-underline">
            ← Retour aux messages
        </a>
    </div>

    @if (session('success'))
        <div class="rounded border border-[#2f855a] bg-[#276749]/30 p-3 text-lg text-[#68d391]">
            ✓ {{ session('success') }}
        </div>
    @endif

    <div class="card2 p-6 sm:p-8 space-y-6">
        {{-- En-tête de la correspondance --}}
        <div class="flex flex-wrap items-start justify-between gap-4 border-b border-[#4a2c17] pb-6">
            <div class="space-y-1">
                <span class="inline-block rounded px-2.5 py-0.5 text-xs font-bold bg-[#4a2c17] text-[#e9c96b] mb-2">
                    {{ $contact->subject_label }}
                </span>
                <h2 class="font-cinzel text-2xl font-bold text-amber-100">{{ $contact->name }}</h2>
                <p class="text-sm font-mono text-gray-300">
                    ✉️ <a href="mailto:{{ $contact->email }}" class="underline hover:text-[#e9c96b]">{{ $contact->email }}</a>
                </p>
            </div>

            <div class="text-right space-y-1">
                <div class="text-xs text-amber-200/80">Reçu le {{ $contact->created_at->format('d/m/Y à H:i') }}</div>
                <div class="text-xs italic text-gray-400">({{ $contact->created_at->diffForHumans() }})</div>
                <div class="pt-1">
                    <span class="inline-flex items-center rounded-full bg-[#276749]/50 px-2.5 py-0.5 text-xs text-green-300 border border-green-800">
                        ✔ Message lu
                    </span>
                </div>
            </div>
        </div>

        {{-- Corps du message reçu --}}
        <div class="space-y-2">
            <h3 class="font-cinzel text-sm font-semibold text-[#e9c96b] uppercase tracking-wider">Message du lecteur :</h3>
            <div class="rounded bg-[#1c0d06] p-5 text-base text-gray-200 leading-relaxed border border-[#4a2c17] whitespace-pre-line shadow-inner">
                {{ $contact->message }}
            </div>
        </div>

        {{-- Zone d'action & réponse --}}
        <div class="space-y-4 pt-4 border-t border-[#4a2c17]">
            <h3 class="font-cinzel text-sm font-semibold text-[#e9c96b] uppercase tracking-wider">Réponse au correspondant :</h3>

            <div class="flex flex-wrap items-center gap-3">
                {{-- Bouton Mailto Application par défaut --}}
                <a href="{{ $mailtoUrl }}" class="plate px-5 py-2.5 text-base font-bold flex items-center gap-2 hover:no-underline shadow">
                    ✉️ Répondre via l'application de mail par défaut
                </a>

                {{-- Copie rapide si webmail --}}
                <button type="button" onclick="copyReplyText()" class="px-4 py-2.5 rounded font-semibold bg-[#3d1e10] hover:bg-[#4a2c17] text-amber-200 text-sm transition border border-[#6b3e21] shadow flex items-center gap-2 cursor-pointer">
                    📋 Copier le texte de réponse prérempli
                </button>
            </div>

            {{-- Aperçu du texte de réponse prérempli --}}
            <details class="rounded bg-[#1a0f0a] border border-[#4a2c17] p-4 text-sm text-gray-300">
                <summary class="cursor-pointer font-semibold text-[#e9c96b] hover:underline">
                    📄 Afficher l'aperçu du texte prérempli
                </summary>
                <div class="mt-3 space-y-2 font-mono text-xs text-amber-100/90 whitespace-pre-line bg-[#0f0905] p-3 rounded border border-[#2b170c]">
                    <span class="text-gray-400">Objet :</span> {{ $replySubject }}

<span class="text-gray-400">Corps :</span>
{{ $replyBody }}
                </div>
            </details>

            <div class="flex justify-end pt-2">
                <form action="{{ route('admin.contacts.destroy', $contact) }}" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer définitivement ce message des registres ?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-4 py-2 rounded font-semibold bg-[#8b1e1e] hover:bg-red-700 text-white text-sm transition cursor-pointer border border-red-900 shadow">
                        🗑️ Supprimer ce message
                    </button>
                </form>
            </div>
        </div>
    </div>
</main>

<script>
    function copyReplyText() {
        const textToCopy = @json($replyBody);
        navigator.clipboard.writeText(textToCopy).then(() => {
            if (typeof window.showToast === 'function') {
                window.showToast('Texte de réponse copié dans le presse-papier !');
            } else {
                alert('Texte de réponse prérempli copié dans le presse-papier !');
            }
        }).catch(err => {
            console.error('Erreur lors de la copie: ', err);
        });
    }
</script>
@endsection

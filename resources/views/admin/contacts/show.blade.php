@extends('layouts.admin')

@section('title', 'Détail du Message — Administration')

@section('content')
<main class="mx-auto max-w-4xl space-y-6 font-garamond">
    <div class="flex flex-wrap items-center justify-between gap-4 border-b border-[#4a2c17] pb-4">
        <div>
            <p class="font-garamond text-sm italic text-[#e9c96b]/70">Correspondance avec les lecteurs</p>
            <h1 class="font-cinzel text-2xl font-bold sm:text-3xl text-[#e9c96b]">Lecture du Message</h1>
        </div>

        <a href="{{ route('admin.contacts.index') }}" class="plate px-4 py-1.5 text-sm font-semibold hover:no-underline">
            ← Retour à la liste
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
                <h2 class="font-cinzel text-xl font-bold text-amber-100">{{ $contact->name }}</h2>
                <p class="text-sm font-mono text-gray-300">
                    ✉️ <a href="mailto:{{ $contact->email }}" class="underline hover:text-[#e9c96b]">{{ $contact->email }}</a>
                </p>
            </div>

            <div class="text-right space-y-1">
                <div class="text-xs text-amber-200/80">Reçu le {{ $contact->created_at->format('d/m/Y à H:i') }}</div>
                <div class="text-xs italic text-gray-400">({{ $contact->created_at->diffForHumans() }})</div>
            </div>
        </div>

        {{-- Corps du message --}}
        <div class="space-y-2">
            <h3 class="font-cinzel text-sm font-semibold text-[#e9c96b] uppercase tracking-wider">Message :</h3>
            <div class="rounded bg-[#1c0d06] p-5 text-base text-gray-200 leading-relaxed border border-[#4a2c17] whitespace-pre-line">
                {{ $contact->message }}
            </div>
        </div>

        {{-- Actions --}}
        <div class="pt-4 border-t border-[#4a2c17] flex flex-wrap items-center justify-between gap-4">
            <a href="mailto:{{ $contact->email }}?subject=Re: {{ urlencode($contact->subject_label) }}" class="plate px-5 py-2 text-base font-bold hover:no-underline">
                ✉️ Répondre par E-mail
            </a>

            <form action="{{ route('admin.contacts.destroy', $contact) }}" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer définitivement ce message ?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-4 py-2 rounded font-semibold bg-[#8b1e1e] hover:bg-red-700 text-white text-sm transition cursor-pointer border border-red-900 shadow">
                    🗑️ Supprimer ce message
                </button>
            </form>
        </div>
    </div>
</main>
@endsection

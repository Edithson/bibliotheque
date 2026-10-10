@extends('layouts.app')

@section('title', 'Paiement Mobile Money — ' . $book->title)

@section('content')
<main class="mx-auto max-w-2xl px-4 pb-16 pt-24 sm:px-6">
    <div class="mb-4">
        <a href="{{ route('shop.books.show', $book->slug) }}" class="font-garamond text-lg italic text-[#e9c96b] underline hover:text-white transition">
            &larr; Revenir à la fiche du livre
        </a>
    </div>

    <div class="card2 p-6 sm:p-10 space-y-6">
        <div class="border-b border-[#4a2c17] pb-4 text-center">
            <p class="font-garamond text-sm italic text-[#e9c96b]/70">Paiement Sécurisé Mobile Money</p>
            <h1 class="font-cinzel text-2xl font-bold text-[#e9c96b] sm:text-3xl">Acquisition de l'ouvrage</h1>
        </div>

        <div class="rounded border border-[#6b4a12] bg-[#1c1208]/80 p-5 font-garamond space-y-3">
            <div class="flex flex-wrap items-center justify-between gap-2 border-b border-[#4a2c17]/60 pb-3">
                <div>
                    <span class="text-xs uppercase font-mono text-[#e9c96b]/60 block">Ouvrage Numérique</span>
                    <h2 class="font-cinzel text-xl font-bold text-gray-100">{{ $book->title }}</h2>
                    <p class="text-sm italic text-gray-400">Par {{ $book->author ?? 'Auteur inconnu' }}</p>
                </div>
                <div class="text-right">
                    <span class="text-xs uppercase font-mono text-[#e9c96b]/60 block">Montant Total</span>
                    <span class="font-cinzel text-2xl font-bold text-amber-300">{{ number_format($book->price, 0, ',', ' ') }} <small class="text-sm">FCFA</small></span>
                </div>
            </div>

            <div class="space-y-1 text-xs font-mono text-gray-400">
                <p>Référence Transaction : <span class="text-[#e9c96b] font-semibold">{{ $payment->payment_ref }}</span></p>
                <p>Compte Client : <span class="text-gray-200">{{ auth()->user()->name }} ({{ auth()->user()->email }})</span></p>
            </div>
        </div>

        {{-- Intégration officielle du Widget & Bouton Monetbil v2 --}}
        <div class="space-y-4 text-center pt-2">
            <p class="font-garamond text-base italic text-[#e9c96b]/90">
                Sélectionnez votre opérateur (Orange Money, MTN Mobile Money, Moov...) et validez le paiement sur votre téléphone portable.
            </p>

            <div class="flex justify-center py-2">
                <form action="{{ $returnUrl }}" method="get" data-monetbil="form"
                      data-service-key="{{ $serviceKey }}"
                      data-amount="{{ $book->price }}"
                      data-currency="XAF"
                      data-item-ref="{{ $payment->payment_ref }}"
                      data-description="{{ $book->title }}"
                      data-notify-url="{{ $notifyUrl }}"
                      data-user-id="{{ auth()->id() }}"
                      data-email="{{ auth()->user()->email }}"
                      data-country="CM">
                    <button class="plate px-8 py-3.5 text-xl font-bold border-amber-800 text-amber-950 shadow-2xl cursor-pointer hover:brightness-110 transition" style="background: linear-gradient(135deg,#fbd38d,#ed8936 50%,#c05621)" type="submit">
                        💳 Pay by Mobile Money ({{ number_format($book->price, 0, ',', ' ') }} FCFA)
                    </button>
                </form>
            </div>

            <div class="flex flex-wrap justify-center items-center gap-3 pt-2 text-xs text-[#e9c96b]/60 font-mono">
                <span>🍊 Orange Money</span> &bull; 
                <span>🟡 MTN Mobile Money</span> &bull; 
                <span>🔵 Express Union</span> &bull; 
                <span>🔒 Transaction Cryptée SSL</span>
            </div>
        </div>
    </div>
</main>
@endsection

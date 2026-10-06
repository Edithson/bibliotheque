@extends('layouts.app')

@section('title', 'Mes Livres & Téléchargements — La Bibliothèque des Mots')

@section('content')
<main class="mx-auto max-w-4xl px-4 py-12">
    <div class="card2 p-6 sm:p-10 space-y-6">
        <div class="border-b border-[#4a2c17] pb-4 flex flex-wrap items-center justify-between gap-3">
            <div>
                <p class="font-garamond text-sm italic text-[#e9c96b]/70">Bibliothèque Personnelle de {{ auth()->user()->name }}</p>
                <h1 class="font-cinzel text-2xl font-bold text-[#e9c96b] sm:text-3xl">Mes Livres & Acquis</h1>
            </div>
            <a href="{{ url('/') }}" class="font-garamond text-sm italic text-[#e9c96b]/80 underline hover:text-[#e9c96b]">← Parcourir d'autres rayons</a>
        </div>

        @if ($books->isEmpty())
            <div class="py-12 text-center font-garamond text-xl italic text-[#f3e7cc]/70">
                <p>Vous n'avez pas encore téléchargé d'ouvrages numériques.</p>
                <p class="mt-2 text-sm">Parcourez nos rayons et découvrez nos livres gratuits ou disponibles au catalogue !</p>
                <div class="mt-6">
                    <a href="{{ url('/') }}" class="plate inline-block px-6 py-2 text-lg hover:no-underline">Découvrir le catalogue</a>
                </div>
            </div>
        @else
            <div class="grid gap-4 sm:grid-cols-2">
                @foreach ($books as $b)
                    <div class="rounded border border-[#4a2c17] bg-[#1b1209]/80 p-4 space-y-3 shadow flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between">
                                <span class="font-garamond text-xs font-bold text-[#8b1e1e] uppercase">{{ $b->category ? $b->category->name : 'Général' }}</span>
                                @if ($b->price === 0)
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-[#276749] text-white">GRATUIT</span>
                                @else
                                    <span class="font-mono text-xs font-bold text-[#e9c96b]">{{ number_format($b->price, 0, ',', ' ') }} FCFA</span>
                                @endif
                            </div>
                            <h2 class="font-cinzel text-lg font-bold text-[#e9c96b] mt-1">{{ $b->title }}</h2>
                            <p class="font-garamond text-sm italic text-[#f3e7cc]/80">de {{ $b->author }}</p>
                            @if ($b->description)
                                <p class="font-garamond text-sm mt-2 line-clamp-2 text-[#f3e7cc]/70">{{ $b->description }}</p>
                            @endif
                        </div>

                        <div class="pt-3 border-t border-[#4a2c17]/50 flex items-center justify-between">
                            <span class="font-mono text-xs text-[#e9c96b]/60">{{ $b->nbr_pages }} pages</span>
                            <a href="{{ route('books.download', $b->slug) }}" class="plate px-4 py-1 text-sm border-green-800 text-green-950 font-bold hover:no-underline inline-block" style="background: linear-gradient(135deg,#68d391,#38a169 50%,#276749)">
                                📄 Télécharger (PDF)
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</main>
@endsection

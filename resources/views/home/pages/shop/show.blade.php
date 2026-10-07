@extends('layouts.app')

@section('title', $book->title . ' — La Bibliothèque des Mots')

@section('content')
<main class="mx-auto max-w-4xl px-4 pb-16 pt-24 sm:px-6">
    <div class="mb-4">
        <a href="{{ route('shop.index') }}" class="font-garamond text-lg italic text-[#e9c96b] underline hover:text-white transition">
            &larr; Retour aux rayons de la bibliothèque
        </a>
    </div>

    <div class="relative w-full max-w-4xl rounded-md p-3.5 shadow-2xl" style="background:linear-gradient(90deg,#0006,transparent 6%,transparent 94%,#0006),{{ $book->cover_color ?? '#5b1a1f' }}">
        <div class="paper relative grid overflow-hidden rounded-sm md:grid-cols-2">
            <section class="relative flex flex-col p-6 text-[#3b2a1a] sm:p-9">
                <p class="font-garamond text-lg font-bold italic text-[#8b1e1e]">{{ $book->category ? $book->category->name : 'Général' }}</p>
                <h1 class="mt-1 font-cinzel text-2xl font-bold leading-tight text-[#2a190e] sm:text-3xl">{{ $book->title }}</h1>
                <p class="mt-1 font-garamond text-xl italic text-[#5a4028]">de {{ $book->author ?? 'Auteur inconnu' }}</p>
                <p class="my-4 text-center text-2xl text-[#b98a2e]" aria-hidden="true">❦</p>
                
                <p class="font-garamond text-xl leading-snug">{{ $book->description ?? 'Aucun résumé fourni.' }}</p>

                <div class="mt-auto pt-6">
                    <p class="font-cinzel text-3xl text-[#2a190e]">
                        @if ($book->price === 0)
                            GRATUIT
                        @else
                            {{ number_format($book->price, 0, ',', ' ') }} <small class="text-sm">FCFA</small>
                        @endif
                    </p>
                    <div class="mt-4 flex flex-wrap items-center gap-4">
                        @if ($book->price === 0 || auth()->check())
                            <a href="{{ route('books.download', $book->slug) }}" class="plate px-6 py-2.5 text-xl font-bold border-green-800 text-green-950" style="background: linear-gradient(135deg,#68d391,#38a169 50%,#276749)">
                                📥 Télécharger (PDF)
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="plate px-6 py-2.5 text-xl font-bold border-amber-800 text-amber-950" style="background: linear-gradient(135deg,#fbd38d,#ed8936 50%,#c05621)">
                                🔐 Se connecter pour télécharger
                            </a>
                        @endif
                    </div>
                </div>
            </section>

            <section class="flex flex-col border-t border-[#0002] p-6 text-[#2f2114] sm:p-9 md:border-l md:border-t-0">
                <p class="font-garamond text-lg font-bold italic text-[#8b1e1e]">Extrait de l'ouvrage</p>
                @if ($book->excerpt)
                    <p class="mt-3 font-garamond text-xl leading-relaxed first-letter:float-left first-letter:mr-2 first-letter:font-cinzel first-letter:text-6xl first-letter:leading-[.85] first-letter:text-[#8b1e1e] sm:text-2xl">
                        {!! nl2br(e(str_replace('|', "\n\n", $book->excerpt))) !!}
                    </p>
                @else
                    <p class="mt-3 font-garamond text-xl italic text-gray-600">Feuilletez cet ouvrage en le téléchargeant directement.</p>
                @endif
                <div class="mt-auto pt-6 text-right font-garamond text-sm text-[#5a4028]">
                    Parution : {{ $book->publish_year ?? 2026 }} &bull; {{ $book->nbr_pages }} pages
                </div>
            </section>
        </div>
    </div>
</main>
@endsection

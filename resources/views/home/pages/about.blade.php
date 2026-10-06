@extends('layouts.app')

@section('title', 'À propos — La Bibliothèque des Mots')

@section('content')
<main class="mx-auto max-w-3xl px-4 py-12">
    <div class="card2 p-6 sm:p-10 space-y-6">
        <div class="border-b border-[#4a2c17] pb-4 text-center">
            <p class="font-garamond text-sm italic text-[#e9c96b]/70">Philosophie & Histoire</p>
            <h1 class="font-cinzel text-3xl font-bold text-[#e9c96b]">À propos de la Bibliothèque</h1>
        </div>

        <div class="space-y-4 font-garamond text-xl leading-relaxed text-[#f3e7cc]">
            <p class="first-letter:float-left first-letter:mr-3 first-letter:font-cinzel first-letter:text-6xl first-letter:leading-[.85] first-letter:text-[#e9c96b]">
                La Bibliothèque des Mots est née d'un désir simple : offrir un refuge numérique où les livres ont une présence physique, une couleur de tranche, une épaisseur, et un souffle.
            </p>

            <p>
                Contrairement aux bibliothèques virtuelles froides et impersonnelles, nous avons conçu cet endroit pour rappeler le crépitement du parquet, l'odeur du papier jauni et la satisfaction de poser un livre sur le comptoir avant de l'emporter.
            </p>

            <h2 class="font-cinzel text-xl font-bold text-[#e9c96b] pt-4 border-t border-[#4a2c17]/60">Notre Engagements</h2>
            <ul class="list-disc pl-6 space-y-2 text-lg">
                <li><b>Accès libre aux savoirs</b> : Tous les ouvrages signalés comme "Gratuits" sont immédiatement téléchargeables sans contrainte.</li>
                <li><b>Valorisation des auteurs</b> : Une plateforme de publication équitable permettant aux auteurs d'exposer leurs œuvres.</li>
                <li><b>Expérience de lecture immersive</b> : Feuilletage des extraits directement depuis les étagères de la bibliothèque.</li>
            </ul>
        </div>

        <div class="pt-6 border-t border-[#4a2c17] flex justify-between items-center text-sm font-garamond italic">
            <a href="{{ url('/') }}" class="text-[#e9c96b] underline hover:text-white">← Entrer dans la boutique</a>
            <a href="{{ route('contact') }}" class="text-[#e9c96b] underline hover:text-white">Poser une question au bibliothécaire →</a>
        </div>
    </div>
</main>
@endsection

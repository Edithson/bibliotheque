@extends('errors.layout')

@section('title', 'Maintenance en cours — La Bibliothèque des Mots')
@section('code', '503')
@section('message_title', '🛠️ Rangement des Rayons & Maintenance')

@section('message')
La Bibliothèque des Mots fait actuellement l'objet d'un rangement et d'un inventaire de ses ouvrages numériques. Nous serons de nouveau accessibles très prochainement.
@endsection

@section('actions')
<button onclick="window.location.reload()" class="px-4 py-2.5 rounded font-semibold bg-[#3d1e10] hover:bg-[#4a2c17] text-amber-200 text-sm transition border border-[#6b3e21] cursor-pointer">
    🔄 Vérifier la Disponibilité
</button>
@endsection

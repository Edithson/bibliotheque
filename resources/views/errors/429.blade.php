@extends('errors.layout')

@section('title', 'Trop de Requêtes — La Bibliothèque des Mots')
@section('code', '429')
@section('message_title', '⏱️ Seuil de Consultations Atteint')

@section('message')
Vous avez effectué un nombre élevé de sollicitations en peu de temps. Afin de préserver la sérénité de nos serveurs, veuillez patienter un court instant avant de consulter d'autres documents.
@endsection

@section('actions')
<button onclick="window.location.reload()" class="px-4 py-2.5 rounded font-semibold bg-[#3d1e10] hover:bg-[#4a2c17] text-amber-200 text-sm transition border border-[#6b3e21] cursor-pointer">
    🔄 Réessayer
</button>
@endsection

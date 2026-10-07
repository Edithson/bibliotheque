@extends('errors.layout')

@section('title', 'Accès Refusé — La Bibliothèque des Mots')
@section('code', '403')
@section('message_title', '🔒 Accès Refusé / Zone Réservée')

@section('message')
Vous tentez d'accéder à un rayon réservé du bureau d'administration. Vos privilèges actuels ne vous permettent pas de consulter ou de modifier cette section.
@endsection

@section('actions')
@guest
    <a href="{{ route('login') }}" class="px-4 py-2.5 rounded font-semibold bg-[#3d1e10] hover:bg-[#4a2c17] text-amber-200 text-sm transition border border-[#6b3e21]">
        🔑 Se Connecter
    </a>
@endguest
@endsection

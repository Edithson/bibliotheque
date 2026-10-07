@extends('errors.layout')

@section('title', 'Session Expirée — La Bibliothèque des Mots')
@section('code', '419')
@section('message_title', '⏳ Session Expirée')

@section('message')
Votre temps de consultation a expiré en raison d'une inactivité prolongée. Veuillez rafraîchir la page pour renouveler votre jeton de sécurité.
@endsection

@section('actions')
<button onclick="window.location.reload()" class="px-4 py-2.5 rounded font-semibold bg-[#3d1e10] hover:bg-[#4a2c17] text-amber-200 text-sm transition border border-[#6b3e21] cursor-pointer">
    🔄 Rafraîchir la Page
</button>
@endsection

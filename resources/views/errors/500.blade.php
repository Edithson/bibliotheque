@extends('errors.layout')

@section('title', 'Erreur Serveur — La Bibliothèque des Mots')
@section('code', '500')
@section('message_title', '⚙️ Incident Technique dans les Archives')

@section('message')
Une anomalie imprévue est survenue au niveau des serveurs de la bibliothèque. L'équipe d'administration a été informée afin de rétablir le service dans les plus brefs délais.
@endsection

@section('actions')
<button onclick="window.location.reload()" class="px-4 py-2.5 rounded font-semibold bg-[#3d1e10] hover:bg-[#4a2c17] text-amber-200 text-sm transition border border-[#6b3e21] cursor-pointer">
    🔄 Réessayer
</button>
@endsection

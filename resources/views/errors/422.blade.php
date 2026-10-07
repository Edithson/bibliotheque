@extends('errors.layout')

@section('title', 'Données Invalides — La Bibliothèque des Mots')
@section('code', '422')
@section('message_title', '⚠️ Données Non Conformes')

@section('message')
Les informations transmises ne respectent pas les critères d'enregistrement de nos registres. Veuillez vérifier les champs renseignés avant de soumettre à nouveau.
@endsection

@section('actions')
<button onclick="window.history.back()" class="px-4 py-2.5 rounded font-semibold bg-[#3d1e10] hover:bg-[#4a2c17] text-amber-200 text-sm transition border border-[#6b3e21] cursor-pointer">
    ↩️ Corriger mes Saisies
</button>
@endsection

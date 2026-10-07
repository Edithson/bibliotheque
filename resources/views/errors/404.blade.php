@extends('errors.layout')

@section('title', 'Ouvrage Introuvable — La Bibliothèque des Mots')
@section('code', '404')
@section('message_title', '📜 Ouvrage ou Page Introuvable')

@section('message')
Le livre, le document ou la page que vous recherchez semble s'être égaré hors des archives de notre bibliothèque ou a été déplacé.
@endsection

@section('actions')
<a href="{{ route('shop.index') }}" class="px-4 py-2.5 rounded font-semibold bg-[#3d1e10] hover:bg-[#4a2c17] text-amber-200 text-sm transition border border-[#6b3e21]">
    📚 Parcourir le Catalogue
</a>
@endsection

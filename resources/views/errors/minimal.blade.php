@extends('errors.layout')

@section('title', 'Erreur — La Bibliothèque des Mots')
@section('code')@yield('code', 'ERR')@endsection
@section('message_title')@yield('title', 'Anomalie Détectée')@endsection

@section('message')
@yield('message', 'Une anomalie est survenue lors de la consultation des registres de la bibliothèque.')
@endsection

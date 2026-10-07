@extends('layouts.admin')

@section('title', 'Gestion des Comptes & Rôles — Bureau du Bibliothécaire')

@section('content')
<div class="mb-6 flex items-center justify-between">
    <div>
        <p class="font-garamond text-sm italic text-[#e9c96b]/70">Bureau du Bibliothécaire — Espace Administrateur</p>
        <h1 class="font-cinzel text-2xl font-bold sm:text-3xl text-[#e9c96b]">Gestion des Comptes Utilisateurs & Rôles</h1>
    </div>
</div>

@if (session('success'))
    <div class="mb-4 rounded border border-[#2f855a] bg-[#276749]/30 p-3 text-lg text-[#68d391]">
        ✓ {{ session('success') }}
    </div>
@endif

@if (session('error'))
    <div class="mb-4 rounded border border-[#e05a3f] bg-[#e05a3f]/20 p-3 text-lg text-red-200">
        ⚠️ {{ session('error') }}
    </div>
@endif

<div class="card2 overflow-x-auto">
    <table class="ledger w-full min-w-[650px] text-left font-garamond text-base">
        <thead>
            <tr>
                <th>Nom d'utilisateur</th>
                <th>Adresse E-mail</th>
                <th>Rôle Actuel</th>
                <th>Modifier le Rôle</th>
                <th>Inscrit le</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($users as $u)
                <tr>
                    <td class="font-semibold">{{ $u->name }}</td>
                    <td class="font-mono text-sm">{{ $u->email }}</td>
                    <td>
                        <span class="rounded px-2.5 py-1 text-xs font-bold font-mono uppercase
                            @if($u->role === 'admin') bg-purple-900 text-purple-200 
                            @elseif($u->role === 'gerant') bg-blue-900 text-blue-200 
                            @elseif($u->role === 'auteur') bg-amber-900 text-amber-200 
                            @else bg-gray-800 text-gray-300 @endif">
                            {{ $u->role }}
                        </span>
                    </td>
                    <td>
                        <form action="{{ route('admin.users.update-role', $u->id) }}" method="POST" class="flex items-center gap-2">
                            @csrf
                            @method('PATCH')
                            <select name="type_id" class="admin-field-select !py-1 !text-sm">
                                <option value="1" {{ $u->type_id == 1 ? 'selected' : '' }}>Guest (Visiteur)</option>
                                <option value="2" {{ $u->type_id == 2 ? 'selected' : '' }}>Auteur</option>
                                <option value="3" {{ $u->type_id == 3 ? 'selected' : '' }}>Gérant</option>
                                <option value="4" {{ $u->type_id == 4 ? 'selected' : '' }}>Administrateur</option>
                            </select>
                            <button type="submit" class="plate px-3 py-1 text-xs whitespace-nowrap">Mettre à jour</button>
                        </form>
                    </td>
                    <td class="font-mono text-xs text-gray-600">{{ $u->created_at->format('d/m/Y H:i') }}</td>
                    <td>
                        @if ($u->id !== auth()->id())
                            <form action="{{ route('admin.users.destroy', $u->id) }}" method="POST" class="inline" onsubmit="return confirm('Supprimer définitivement le compte de {{ $u->name }} ?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-[#8b1e1e] underline bg-transparent border-0 p-0 cursor-pointer text-sm">Supprimer</button>
                            </form>
                        @else
                            <span class="text-xs italic text-gray-500">(Votre compte)</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="py-8 text-center italic">Aucun utilisateur enregistré.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-6 flex justify-center">
    {{ $users->links() }}
</div>
@endsection

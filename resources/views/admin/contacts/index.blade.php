@extends('layouts.admin')

@section('title', 'Gestion des Messages — Administration')

@section('content')
<main class="mx-auto max-w-6xl space-y-6 font-garamond">
    <div class="flex flex-wrap items-center justify-between gap-4 border-b border-[#4a2c17] pb-4">
        <div>
            <p class="font-garamond text-sm italic text-[#e9c96b]/70">Espace Correspondance & Support</p>
            <h1 class="font-cinzel text-2xl font-bold sm:text-3xl text-[#e9c96b]">Messages de Contact</h1>
        </div>
        
        @if ($unreadCount > 0)
            <span class="rounded-full bg-[#8b1e1e] px-3 py-1 text-xs font-bold text-white shadow border border-red-700">
                📬 {{ $unreadCount }} message(s) non lu(s)
            </span>
        @endif
    </div>

    @if (session('success'))
        <div class="rounded border border-[#2f855a] bg-[#276749]/30 p-3 text-lg text-[#68d391]">
            ✓ {{ session('success') }}
        </div>
    @endif

    {{-- Filtres et barre de recherche --}}
    <div class="card2 p-4 flex flex-wrap items-center justify-between gap-4">
        <form method="GET" action="{{ route('admin.contacts.index') }}" class="flex flex-wrap items-center gap-3 w-full md:w-auto">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Rechercher nom, e-mail, sujet..." class="admin-field-input !w-64">
            
            <select name="status" class="admin-field-select !w-44" onchange="this.form.submit()">
                <option value="">Tous les messages</option>
                <option value="unread" {{ request('status') === 'unread' ? 'selected' : '' }}>✉️ Non lus seulement</option>
                <option value="read" {{ request('status') === 'read' ? 'selected' : '' }}>✔ Lus seulement</option>
            </select>

            <button type="submit" class="plate px-4 py-1.5 text-sm font-semibold">🔍 Filtrer</button>
            
            @if(request('search') || request('status'))
                <a href="{{ route('admin.contacts.index') }}" class="text-xs italic text-[#e9c96b]/70 underline hover:text-[#e9c96b]">Réinitialiser</a>
            @endif
        </form>

        <div class="text-sm italic text-[#e9c96b]/80">
            Total : {{ $contacts->total() }} correspondance(s)
        </div>
    </div>

    {{-- Tableau des messages --}}
    <div class="card2 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-[#1c0d06] font-cinzel text-[#e9c96b] border-b border-[#4a2c17]">
                    <tr>
                        <th class="px-4 py-3">Statut</th>
                        <th class="px-4 py-3">Date</th>
                        <th class="px-4 py-3">Expéditeur</th>
                        <th class="px-4 py-3">Sujet</th>
                        <th class="px-4 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#4a2c17]">
                    @forelse ($contacts as $contact)
                        <tr class="hover:bg-[#3d1e10]/40 transition {{ $contact->is_read ? 'opacity-80' : 'font-semibold bg-[#3d1e10]/20' }}">
                            <td class="px-4 py-3 whitespace-nowrap">
                                @if (!$contact->is_read)
                                    <span class="inline-flex items-center rounded-full bg-[#8b1e1e] px-2.5 py-0.5 text-xs text-white">
                                        ✉️ Non lu
                                    </span>
                                @else
                                    <span class="inline-flex items-center rounded-full bg-[#276749]/50 px-2.5 py-0.5 text-xs text-gray-300">
                                        ✔ Lu
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-xs text-amber-200/80">
                                {{ $contact->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-4 py-3">
                                <div class="font-bold text-[#e9c96b]">{{ $contact->name }}</div>
                                <div class="text-xs text-gray-400 font-mono">{{ $contact->email }}</div>
                            </td>
                            <td class="px-4 py-3 text-amber-100 max-w-xs truncate">
                                {{ $contact->subject_label }}
                            </td>
                            <td class="px-4 py-3 text-right whitespace-nowrap space-x-2">
                                <a href="{{ route('admin.contacts.show', $contact) }}" class="inline-block p-1.5 rounded text-amber-300 hover:text-amber-100 hover:bg-[#4a2c17] transition" title="Consulter le message">
                                    👁️
                                </a>

                                <form action="{{ route('admin.contacts.destroy', $contact) }}" method="POST" class="inline-block" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce message de contact ?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 rounded text-red-400 hover:text-red-200 hover:bg-red-950/60 transition" title="Supprimer le message">
                                        🗑️
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-gray-400 italic">
                                Aucune correspondance trouvée dans les archives.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if ($contacts->hasPages())
            <div class="p-4 border-t border-[#4a2c17] bg-[#1c0d06] text-white">
                {{ $contacts->links() }}
            </div>
        @endif
    </div>
</main>
@endsection

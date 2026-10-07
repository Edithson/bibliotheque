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
                        @php
                            $quickSubject = "Re: " . $contact->subject_label . " — La Bibliothèque des Mots";
                            $quickBody = "Bonjour " . $contact->name . ",\n\nMerci pour votre message concernant : " . $contact->subject_label . ".\n\n-----------------------------------\nCitation :\n\"" . $contact->message . "\"\n-----------------------------------\nCordialement,\nLe Bibliothécaire — La Bibliothèque des Mots";
                            $quickMailto = "mailto:" . rawurlencode($contact->email) . "?subject=" . rawurlencode($quickSubject) . "&body=" . rawurlencode($quickBody);
                        @endphp
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
                            <td class="px-4 py-3 text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-1.5">
                                    {{-- Icone Consultation (Œil) --}}
                                    <a href="{{ route('admin.contacts.show', $contact) }}" class="inline-flex items-center justify-center p-1.5 text-[#e9c96b] hover:text-white hover:bg-[#3b2514] rounded transition" title="Consulter le message">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </a>

                                    {{-- Icone Répondre (Enveloppe Mail) --}}
                                    <a href="{{ $quickMailto }}" class="inline-flex items-center justify-center p-1.5 text-blue-300 hover:text-white hover:bg-[#3b2514] rounded transition" title="Répondre par e-mail via l'application par défaut">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 002-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                        </svg>
                                    </a>

                                    {{-- Icone Suppression (Corbeille) --}}
                                    <form action="{{ route('admin.contacts.destroy', $contact) }}" method="POST" class="inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce message de contact ?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex items-center justify-center p-1.5 text-[#e05a3f] hover:text-red-400 hover:bg-[#3b2514] rounded transition border-0 bg-transparent cursor-pointer" title="Supprimer le message">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
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

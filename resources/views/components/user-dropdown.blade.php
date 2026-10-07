@props(['isAdmin' => false])

<div class="relative" id="user-menu-wrap-{{ $isAdmin ? 'admin' : 'client' }}">
    <button id="user-menu-btn-{{ $isAdmin ? 'admin' : 'client' }}" type="button" aria-expanded="false" class="user-menu-toggle flex items-center gap-2 rounded bg-[#2a1a0e] px-3.5 py-1.5 text-sm font-bold text-[#e9c96b] border border-[#6b4a12] hover:border-[#b98a2e] transition cursor-pointer shadow">
        <span>👤 {{ auth()->user()->name }}</span>
        <span class="rounded px-2 py-0.5 text-[10px] uppercase bg-[#8b1e1e] text-white font-mono">
            {{ auth()->user()->role }}
        </span>
        <span class="text-xs text-[#e9c96b]/70">▾</span>
    </button>
    
    <div id="user-menu-dropdown-{{ $isAdmin ? 'admin' : 'client' }}" class="user-menu-dropdown absolute right-0 mt-2 w-52 rounded bg-[#1b1209] border border-[#4a2c17] shadow-2xl py-1 hidden z-50">
        @if($isAdmin)
            <a href="{{ url('/') }}" class="block px-4 py-2 text-sm text-[#f3e7cc] hover:bg-[#2a190e] hover:text-[#e9c96b]">
                🏠 Retour à la boutique
            </a>
            <div class="border-t border-[#4a2c17] my-1"></div>
            <a href="{{ route('admin.books.index') }}" class="block px-4 py-2 text-sm text-[#f3e7cc] hover:bg-[#2a190e] hover:text-[#e9c96b]">
                📚 Registre des Livres
            </a>
            @if (auth()->user()->isGerant())
                <a href="{{ route('admin.categories.index') }}" class="block px-4 py-2 text-sm text-[#f3e7cc] hover:bg-[#2a190e] hover:text-[#e9c96b]">
                    🏷️ Catégories
                </a>
                <a href="{{ route('admin.contacts.index') }}" class="block px-4 py-2 text-sm text-[#f3e7cc] hover:bg-[#2a190e] hover:text-[#e9c96b]">
                    📬 Messages de Contact
                </a>
                <a href="{{ route('admin.downloads.index') }}" class="block px-4 py-2 text-sm text-[#f3e7cc] hover:bg-[#2a190e] hover:text-[#e9c96b]">
                    📊 Téléchargements
                </a>
            @endif
            @if (auth()->user()->isAdmin())
                <a href="{{ route('admin.users.index') }}" class="block px-4 py-2 text-sm text-[#f3e7cc] hover:bg-[#2a190e] hover:text-[#e9c96b]">
                    👥 Comptes & Rôles
                </a>
            @endif
        @else
            <a href="{{ route('my-books') }}" class="block px-4 py-2 text-sm text-[#f3e7cc] hover:bg-[#2a190e] hover:text-[#e9c96b]">
                📚 Mes Livres
            </a>
            @if(auth()->user()->isAuthor())
                <a href="{{ route('admin.index') }}" class="block px-4 py-2 text-sm text-[#f3e7cc] hover:bg-[#2a190e] hover:text-[#e9c96b]">
                    🏛️ Bureau Admin
                </a>
            @endif
        @endif

        <div class="border-t border-[#4a2c17] my-1"></div>

        <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-[#f3e7cc] hover:bg-[#2a190e] hover:text-[#e9c96b]">
            ⚙️ Mon Profil
        </a>

        <div class="border-t border-[#4a2c17] my-1"></div>

        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-400 hover:bg-[#2a190e] hover:text-red-300 cursor-pointer">
                🚪 Déconnexion
            </button>
        </form>
    </div>
</div>

@extends('layouts.admin')

@section('title', 'Bureau du Bibliothécaire — Administration')

@section('header_stats')
<div id="stats" class="mt-6 grid grid-cols-2 gap-3 sm:grid-cols-4"></div>
@endsection

@section('content')
<div class="flex flex-wrap items-center gap-3">
    <input id="q" type="search" placeholder="Rechercher un titre, un auteur…" class="admin-field-input min-w-[200px] flex-1">
    <select id="catf" class="admin-field-select w-auto"></select>
    <button id="new" class="plate whitespace-nowrap px-4 py-2 text-lg">+ Nouvel ouvrage</button>
</div>
<p id="cnt" class="mt-2 font-garamond text-sm italic text-[#e9c96b]/70"></p>

<div class="card2 mt-3 overflow-x-auto">
    <table class="ledger w-full min-w-[680px] text-left font-garamond text-base">
        <thead>
            <tr>
                <th>Titre</th>
                <th>Auteur</th>
                <th>Catégorie</th>
                <th>Prix</th>
                <th>Stock</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody id="tbody"></tbody>
    </table>
</div>

{{-- Modal Ajout/Modification d'ouvrage --}}
<div id="m" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/70 p-3 backdrop-blur-sm" role="dialog" aria-modal="true" aria-labelledby="mt">
    <form id="frm" class="card2 w-full max-w-lg p-6 sm:p-8">
        <h2 id="mt" class="font-cinzel text-xl text-[#e9c96b]">Nouvel ouvrage</h2>
        <div class="mt-4 grid gap-3 sm:grid-cols-2">
            <label class="sm:col-span-2">
                <span class="block text-xs text-[#e9c96b] mb-1">Titre</span>
                <input id="f_t" class="admin-field-input" required>
            </label>
            <label>
                <span class="block text-xs text-[#e9c96b] mb-1">Auteur</span>
                <input id="f_a" class="admin-field-input" required>
            </label>
            <label>
                <span class="block text-xs text-[#e9c96b] mb-1">Catégorie</span>
                <select id="f_g" class="admin-field-select"></select>
            </label>
            <label>
                <span class="block text-xs text-[#e9c96b] mb-1">Prix (FCFA)</span>
                <input id="f_p" type="number" min="0" class="admin-field-input" required>
            </label>
            <label>
                <span class="block text-xs text-[#e9c96b] mb-1">Stock</span>
                <input id="f_s" type="number" min="0" class="admin-field-input" required>
            </label>
            <label class="sm:col-span-2">
                <span class="block text-xs text-[#e9c96b] mb-1">Description</span>
                <textarea id="f_d" rows="2" class="admin-field-textarea"></textarea>
            </label>
        </div>
        <div class="mt-5 flex justify-end gap-4">
            <button type="button" id="cancel" class="font-garamond text-lg italic underline">Annuler</button>
            <button type="submit" class="plate px-5 py-2 text-lg">Enregistrer</button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
const $ = s => document.querySelector(s), fmt = n => n.toLocaleString('fr-FR');

const CATS = @json($categories->pluck('name'));
let BOOKS = @json($books->map(function($b) {
    return [
        'id' => $b->id,
        't' => $b->title,
        'a' => $b->author ?? 'Inconnu',
        'g' => $b->category ? $b->category->name : 'Général',
        'p' => $b->price,
        's' => $b->stock,
        'd' => $b->description ?? ''
    ];
}));

let nextId = Math.max(0, ...BOOKS.map(b => b.id)) + 1, editId = null, tm;

function stats() {
    const val = BOOKS.reduce((s, b) => s + b.p * b.s, 0), ex = BOOKS.reduce((s, b) => s + b.s, 0), rupture = BOOKS.filter(b => b.s == 0).length;
    $('#stats').innerHTML = `<div class="card2 p-4"><p class="text-sm text-[#e9c96b]/70">Ouvrages au catalogue</p><p class="font-cinzel text-3xl mt-1">${BOOKS.length}</p></div><div class="card2 p-4"><p class="text-sm text-[#e9c96b]/70">Valeur du stock</p><p class="font-cinzel text-3xl mt-1">${fmt(val)} <small class="text-sm">FCFA</small></p></div><div class="card2 p-4"><p class="text-sm text-[#e9c96b]/70">Exemplaires en stock</p><p class="font-cinzel text-3xl mt-1">${ex}</p></div><div class="card2 p-4"><p class="text-sm text-[#e9c96b]/70">En rupture</p><p class="font-cinzel text-3xl mt-1 ${rupture ? 'text-[#e05a3f]' : ''}">${rupture}</p></div>`;
}

function rows() {
    const q = $('#q').value.trim().toLowerCase(), cat = $('#catf').value, list = BOOKS.filter(b => (cat == 'Tout' || b.g == cat) && (!q || (b.t + ' ' + b.a).toLowerCase().includes(q)));
    $('#tbody').innerHTML = list.length ? list.map(b => `<tr><td class="font-semibold">${b.t}</td><td>${b.a}</td><td>${b.g}</td><td>${fmt(b.p)} FCFA</td><td class="${b.s == 0 ? 'font-bold text-[#8b1e1e]' : ''}">${b.s}</td><td class="whitespace-nowrap"><button class="underline" onclick="edit(${b.id})">Modifier</button> · <button class="text-[#8b1e1e] underline" onclick="del(${b.id})">Supprimer</button></td></tr>`).join('') : '<tr><td colspan="6" class="py-10 text-center italic">Aucun ouvrage ne correspond à cette recherche.</td></tr>';
    $('#cnt').textContent = `${list.length} ouvrage${list.length > 1 ? 's' : ''} affiché${list.length > 1 ? 's' : ''} sur ${BOOKS.length}`;
}

function refresh() { stats(); rows(); }
function openForm() { $('#m').classList.replace('hidden', 'flex'); }
function closeForm() { $('#m').classList.replace('flex', 'hidden'); }
function openNew() { editId = null; $('#mt').textContent = 'Nouvel ouvrage'; $('#frm').reset(); openForm(); }

function edit(id) {
    const b = BOOKS.find(x => x.id == id);
    if (!b) return;
    editId = id;
    $('#mt').textContent = "Modifier l'ouvrage";
    $('#f_t').value = b.t;
    $('#f_a').value = b.a;
    $('#f_g').value = b.g;
    $('#f_p').value = b.p;
    $('#f_s').value = b.s;
    $('#f_d').value = b.d;
    openForm();
}

function del(id) {
    const b = BOOKS.find(x => x.id == id);
    if (!b || !confirm(`Retirer « ${b.t} » du catalogue ?`)) return;
    BOOKS = BOOKS.filter(x => x.id != id);
    refresh();
    toast('Ouvrage retiré du catalogue');
}

function toast(s) {
    const e = $('#t');
    e.textContent = s;
    e.classList.remove('opacity-0');
    clearTimeout(tm);
    tm = setTimeout(() => e.classList.add('opacity-0'), 2200);
}

$('#f_g').innerHTML = CATS.map(c => `<option>${c}</option>`).join('');
$('#catf').innerHTML = '<option>Tout</option>' + CATS.map(c => `<option>${c}</option>`).join('');
$('#q').oninput = rows;
$('#catf').onchange = rows;
$('#new').onclick = openNew;
$('#cancel').onclick = closeForm;
$('#m').onclick = e => { if (e.target.id == 'm') closeForm(); };
addEventListener('keydown', e => { if (e.key == 'Escape' && !$('#m').classList.contains('hidden')) closeForm(); });

$('#frm').onsubmit = e => {
    e.preventDefault();
    const o = {
        t: $('#f_t').value.trim(),
        a: $('#f_a').value.trim(),
        g: $('#f_g').value,
        p: +$('#f_p').value,
        s: +$('#f_s').value,
        d: $('#f_d').value.trim()
    };
    if (editId) {
        Object.assign(BOOKS.find(x => x.id == editId), o);
    } else {
        BOOKS.push({ id: nextId++, ...o });
    }
    closeForm();
    refresh();
    toast(editId ? 'Modifications enregistrées' : 'Ouvrage ajouté au catalogue');
};

refresh();
</script>
@endpush

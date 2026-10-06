@extends('layouts.app')

@section('title', 'La Bibliothèque des Mots — Accueil')

@section('content')
<button id="cart" class="plate fixed right-3 z-40 px-3 py-1.5 text-lg" style="top:calc(.75rem + env(safe-area-inset-top,0px))">
    Comptoir : <b id="n">0</b>
</button>

<header class="px-4 pb-2 pt-16 text-center sm:pt-10">
    <div class="plate mx-auto inline-block px-6 py-4 !cursor-default sm:px-12">
        <h1 class="font-cinzel text-2xl font-bold tracking-widest sm:text-4xl">La Bibliothèque des Mots</h1>
    </div>
    <div class="mt-2">
        <a href="{{ url('/admin') }}" class="font-garamond text-sm italic text-[#e9c96b]/80 underline hover:text-[#e9c96b]">Accéder au Bureau du Bibliothécaire (Admin)</a>
    </div>
    <p class="mt-4 font-garamond text-xl italic text-[#e9c96b]/90 sm:text-2xl">Poussez la porte, parcourez les rayons, feuilletez avant d'emporter.</p>
</header>

<main class="mx-auto max-w-4xl px-4 pb-16 sm:px-6">
    <label class="card mt-6 block rounded-sm py-4 pl-[60px] pr-4">
        <span class="font-cinzel text-xs text-[#8b1e1e]">Fiche de recherche</span>
        <input id="q" type="search" placeholder="Un titre, un auteur, un thème…" autocomplete="off" class="mt-1 block w-full bg-transparent font-mono text-lg outline-none placeholder:text-[#3b2a1a]/50">
    </label>
    
    <div id="cats" class="mt-5 flex flex-wrap justify-center gap-2"></div>
    <p id="ct" class="mt-4 text-center font-garamond text-lg italic text-[#e9c96b]/80"></p>
    <p class="text-center font-garamond text-sm italic text-[#e9c96b]/60 sm:hidden">Faites glisser un rayon pour le parcourir.</p>
    
    <div id="rows" class="mt-4 space-y-10"></div>
</main>

{{-- Modal d'extrait de livre --}}
<div id="m" class="fixed inset-0 z-50 hidden overflow-y-auto bg-black/80 p-3 backdrop-blur-sm sm:p-6" role="dialog" aria-modal="true" aria-labelledby="bt">
    <div id="bk" class="relative m-auto w-full max-w-4xl rounded-md p-2.5 shadow-2xl sm:p-3.5" style="background:linear-gradient(90deg,#0006,transparent 6%,transparent 94%,#0006),var(--c)">
        <div class="paper relative grid overflow-hidden rounded-sm md:grid-cols-2">
            <section class="relative flex flex-col p-6 text-[#3b2a1a] sm:p-9">
                <p id="bg" class="font-garamond text-lg font-bold italic text-[#8b1e1e]"></p>
                <h2 id="bt" class="mt-1 font-cinzel text-2xl font-bold leading-tight text-[#2a190e] sm:text-3xl"></h2>
                <p id="ba" class="mt-1 font-garamond text-xl italic text-[#5a4028]"></p>
                <p class="my-4 text-center text-2xl text-[#b98a2e]" aria-hidden="true">❦</p>
                <p id="bd" class="font-garamond text-xl leading-snug"></p>
                <div class="mt-auto pt-6">
                    <p class="font-cinzel text-3xl text-[#2a190e]"><span id="bp"></span> <small class="text-sm">FCFA</small></p>
                    <div class="mt-3 flex flex-wrap items-center gap-4">
                        <button id="add" class="plate px-5 py-2 text-xl">Poser sur le comptoir</button>
                        <button id="x" class="font-garamond text-lg italic underline">Reposer le livre</button>
                    </div>
                </div>
                <div aria-hidden="true" class="pointer-events-none absolute bottom-3 right-3 hidden h-16 w-16 -rotate-12 rounded-full border-2 border-[#8b1e1e]/50 text-center font-cinzel text-[8px] font-bold leading-tight text-[#8b1e1e]/60 sm:flex sm:items-center sm:justify-center">
                    EX-LIBRIS<br>BIBLIOTHÈQUE<br>DES MOTS
                </div>
            </section>
            
            <section id="rp" class="flex flex-col border-t border-[#0002] p-6 text-[#2f2114] sm:p-9 md:border-l md:border-t-0">
                <p class="font-garamond text-lg font-bold italic text-[#8b1e1e]">Extrait</p>
                <p id="tx" class="mt-3 font-garamond text-xl leading-relaxed first-letter:float-left first-letter:mr-2 first-letter:font-cinzel first-letter:text-6xl first-letter:leading-[.85] first-letter:text-[#8b1e1e] sm:text-2xl"></p>
                <p id="en" hidden class="mt-4 font-garamond text-lg italic text-[#5a4028]">La suite vous attend entre les pages du livre.</p>
                <div class="mt-auto flex items-center justify-between gap-2 pt-6 font-garamond text-lg">
                    <button id="pv" class="disabled:opacity-30">‹ Précédente</button>
                    <span id="pn" class="text-base"></span>
                    <button id="nx" class="disabled:opacity-30">Suivante ›</button>
                </div>
            </section>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
/* Données dynamiques depuis la base de données SQLite */
const B = @json($books);
const DB_CATS = @json($categories->pluck('name'));

const $ = s => document.querySelector(s);
const nz = s => s.toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, '');
const pal = ["#5b1a1f","#1f3d2e","#1d2b4a","#3e2a1c","#1c4a4a","#4a2340","#4b4a1e","#1a1a1a","#7a3418"];
const rm = matchMedia('(prefers-reduced-motion: reduce)').matches;

let cur = null, pg = 0, cat = 'Tout', last = null, cart = [], tm;

const sp = i => {
    const b = B[i];
    return `<button class="spine" data-i="${i}" style="--c:${b.c};--w:${b.w}px;--h:${b.h}px" aria-label="${b.t}, ${b.a}"><span>${b.t}</span></button>`;
};
const fill = k => {
    const r = (k * 7919) % 97;
    return `<i class="spine" aria-hidden="true" style="--c:${pal[r % 9]};--w:${26 + r % 22}px;--h:${150 + (r * 3) % 70}px;pointer-events:none"></i>`;
};
const ladderSvg = `<svg viewBox="0 0 60 260" preserveAspectRatio="none" aria-hidden="true"><defs><linearGradient id="lw" x1="0" x2="1"><stop offset="0" stop-color="#6b4423"/><stop offset=".5" stop-color="#9a6a35"/><stop offset="1" stop-color="#5a3a1c"/></linearGradient></defs><circle cx="30" cy="9" r="8" fill="#241608"/><rect x="4" y="12" width="9" height="246" rx="3" fill="url(#lw)"/><rect x="47" y="12" width="9" height="246" rx="3" fill="url(#lw)"/><g stroke="#7a4a22" stroke-width="7" stroke-linecap="round"><line x1="9" y1="40" x2="51" y2="40"/><line x1="9" y1="78" x2="51" y2="78"/><line x1="9" y1="116" x2="51" y2="116"/><line x1="9" y1="154" x2="51" y2="154"/><line x1="9" y1="192" x2="51" y2="192"/><line x1="9" y1="230" x2="51" y2="230"/></g></svg>`;

// Division dynamique en étagères
const chunkSize = 4;
const shelfCount = Math.max(1, Math.ceil(B.length / chunkSize));
let rowsHtml = '';
for (let s = 0; s < shelfCount; s++) {
    const startIdx = s * chunkSize;
    const endIdx = Math.min(startIdx + chunkSize, B.length);
    let shelfItemsHtml = '';
    let itemSlot = 0;
    
    for (let j = 0; j < 20; j++) {
        if ([1, 5, 9, 13].includes(j) && (startIdx + itemSlot) < endIdx) {
            shelfItemsHtml += sp(startIdx + itemSlot);
            itemSlot++;
        } else {
            shelfItemsHtml += fill(s * 20 + j);
        }
    }
    rowsHtml += `<div class="relative">${s === 0 ? `<div class="rail" aria-hidden="true"></div><button id="ladder" type="button" aria-label="Faire glisser l'échelle le long du rayon" class="ladder-wrap" style="left:14%">${ladderSvg}</button>` : ''}<div class="shelf">${shelfItemsHtml}</div><div class="plank"></div></div>`;
}

$('#rows').innerHTML = rowsHtml;

(function() {
    const lw = $('#ladder');
    if (!lw) return;
    const wrap = lw.parentElement;
    let sx = 0, sl = 0, drag = false;
    const cl = v => Math.min(90, Math.max(4, v));
    lw.onpointerdown = e => {
        drag = true;
        lw.setPointerCapture(e.pointerId);
        sx = e.clientX;
        sl = parseFloat(lw.style.left);
        lw.style.transition = 'none';
    };
    lw.onpointermove = e => {
        if (!drag) return;
        const r = wrap.getBoundingClientRect(), dx = e.clientX - sx;
        lw.style.left = cl(sl + dx / r.width * 100) + '%';
        if (!rm) lw.style.transform = `rotate(${Math.max(-5, Math.min(5, dx / 9))}deg)`;
    };
    const end = () => {
        if (!drag) return;
        drag = false;
        lw.style.transition = '';
        lw.style.transform = '';
    };
    lw.onpointerup = end;
    lw.onpointercancel = end;
    lw.onkeydown = e => {
        const v = parseFloat(lw.style.left);
        if (e.key == 'ArrowLeft') lw.style.left = cl(v - 6) + '%';
        if (e.key == 'ArrowRight') lw.style.left = cl(v + 6) + '%';
    };
})();

const availableCats = ['Tout', ...new Set([...(DB_CATS.length ? DB_CATS : []), ...B.map(b => b.g)])];
$('#cats').innerHTML = availableCats.map(c => `<button class="plate px-4 py-1 text-lg" aria-pressed="${c == 'Tout'}">${c}</button>`).join('');

const filt = () => {
    const q = nz($('#q').value.trim());
    let k = 0;
    document.querySelectorAll('button.spine').forEach(e => {
        const b = B[e.dataset.i], ok = (cat == 'Tout' || b.g == cat) && (!q || nz(b.t + b.a + b.g + b.d).includes(q));
        e.classList.toggle('dim', !ok);
        if (ok) k++;
    });
    $('#ct').textContent = k ? `${k} ouvrage${k > 1 ? 's' : ''} sur les rayons` : 'Aucun ouvrage ne correspond : essayez un autre mot.';
};

$('#q').oninput = filt;
$('#cats').onclick = e => {
    const c = e.target.closest('button');
    if (!c) return;
    cat = c.textContent;
    document.querySelectorAll('#cats button').forEach(x => x.setAttribute('aria-pressed', x == c));
    filt();
};
$('#rows').onclick = e => {
    const s = e.target.closest('button.spine');
    if (s) openBook(+s.dataset.i, s);
};

function show() {
    const P = B[cur].x.split('|');
    $('#tx').textContent = P[pg];
    $('#pn').textContent = `${pg + 1} / ${P.length}`;
    $('#pv').disabled = !pg;
    $('#nx').disabled = pg == P.length - 1;
    $('#en').hidden = pg < P.length - 1;
}

function openBook(i, el) {
    const r0 = el.getBoundingClientRect();
    cur = i;
    pg = 0;
    last = el;
    const b = B[i], bk = $('#bk');
    bk.style.setProperty('--c', b.c);
    $('#bg').textContent = b.g;
    $('#bt').textContent = b.t;
    $('#ba').textContent = 'de ' + b.a;
    $('#bd').textContent = b.d;
    $('#bp').textContent = b.p.toLocaleString('fr-FR');
    $('#m').classList.replace('hidden', 'flex');
    document.body.style.overflow = 'hidden';
    if (!rm) {
        const r1 = bk.getBoundingClientRect(),
            dx = (r0.left + r0.width / 2) - (r1.left + r1.width / 2),
            dy = (r0.top + r0.height / 2) - (r1.top + r1.height / 2),
            sx = Math.max(r0.width / r1.width, .06),
            sy = Math.max(r0.height / r1.height, .06);
        bk.style.transition = 'none';
        bk.style.transform = `perspective(1400px) translate(${dx}px,${dy}px) scale(${sx},${sy}) rotateY(-65deg)`;
        bk.style.opacity = '.35';
        requestAnimationFrame(() => {
            bk.style.transition = 'transform .55s cubic-bezier(.22,.8,.2,1),opacity .4s';
            bk.style.transform = '';
            bk.style.opacity = '1';
        });
    }
    show();
    $('#x').focus();
}

const cls = () => {
    $('#m').classList.replace('flex', 'hidden');
    document.body.style.overflow = '';
    cur = null;
    if (last) last.focus();
};

function go(d) {
    const L = B[cur].x.split('|').length;
    if (pg + d < 0 || pg + d >= L) return;
    const r = $('#rp');
    r.classList.remove('flip');
    void r.offsetWidth;
    r.classList.add('flip');
    setTimeout(() => {
        pg += d;
        show();
    }, 220);
}

$('#x').onclick = cls;
$('#m').onclick = e => { if (e.target.id == 'm') cls(); };
$('#pv').onclick = () => go(-1);
$('#nx').onclick = () => go(1);
addEventListener('keydown', e => {
    if (cur === null) return;
    if (e.key == 'Escape') cls();
    if (e.key == 'ArrowRight') go(1);
    if (e.key == 'ArrowLeft') go(-1);
});

const toast = s => {
    const t = $('#t');
    t.textContent = s;
    t.classList.add('on');
    clearTimeout(tm);
    tm = setTimeout(() => t.classList.remove('on'), 2400);
};

$('#add').onclick = () => {
    cart.push(B[cur]);
    $('#n').textContent = cart.length;
    const c = $('#cart');
    c.classList.remove('stamp');
    void c.offsetWidth;
    c.classList.add('stamp');
    toast(`« ${B[cur].t} » est sur le comptoir`);
};

$('#cart').onclick = () => toast(cart.length ? `${cart.length} livre${cart.length > 1 ? 's' : ''} : ${cart.reduce((s, b) => s + b.p, 0).toLocaleString('fr-FR')} FCFA` : 'Le comptoir est vide pour l\u2019instant.');

filt();
</script>
@endpush

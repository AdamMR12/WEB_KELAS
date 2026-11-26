import 'lenis/dist/lenis.css';
import Lenis from 'lenis';
import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';
import { SplitText } from 'gsap/SplitText';
import { animate, hover, press, stagger } from 'motion';

gsap.registerPlugin(ScrollTrigger, SplitText);
window.__animReady = true;

const reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
const finePointer = window.matchMedia('(pointer: fine)').matches;
const $ = (sel, root = document) => root.querySelector(sel);
const $$ = (sel, root = document) => [...root.querySelectorAll(sel)];

/* ---------- Lenis smooth scroll (dikendalikan oleh ticker GSAP) ---------- */
let lenis = null;
if (!reduced) {
    lenis = new Lenis({ autoRaf: false, duration: 1.1, smoothWheel: true });
    lenis.on('scroll', ScrollTrigger.update);
    gsap.ticker.add((time) => lenis.raf(time * 1000));
    gsap.ticker.lagSmoothing(0);
}

/* ---------- GSAP: intro halaman ---------- */
function intro() {
    if (reduced) {
        gsap.set('[data-anim], [data-reveal]', { autoAlpha: 1 });
        return;
    }

    const tl = gsap.timeline({ defaults: { ease: 'power3.out' } });

    if ($('[data-anim="sidebar"]')) {
        tl.from('[data-anim="sidebar"]', { x: -40, autoAlpha: 0, duration: 0.7, clearProps: 'transform' })
            .from('[data-anim="sidebar"] a', { x: -12, autoAlpha: 0, stagger: 0.05, duration: 0.4, clearProps: 'transform' }, '-=0.4');
    }
    if ($('[data-anim="header"]')) {
        tl.from('[data-anim="header"]', { y: -16, autoAlpha: 0, duration: 0.5, clearProps: 'transform' }, '-=0.5');
    }

    const title = $('[data-anim="title"]');
    if (title) {
        gsap.set(title, { autoAlpha: 1 });
        const split = SplitText.create(title, { type: 'words,chars' });
        tl.from(split.chars, { yPercent: 110, autoAlpha: 0, stagger: 0.018, duration: 0.55, clearProps: 'transform' }, '-=0.3');
    }

    tl.from('[data-anim="hero"]', { y: 14, autoAlpha: 0, stagger: 0.08, duration: 0.5, clearProps: 'transform' }, '-=0.35');

    // Count-up angka statistik
    $$('[data-count]').forEach((el) => {
        const target = Number(el.dataset.count) || 0;
        const obj = { v: 0 };
        tl.to(obj, {
            v: target,
            duration: 1,
            ease: 'power2.out',
            onUpdate: () => (el.textContent = Math.round(obj.v)),
        }, '<');
    });

    // Kartu & blok konten muncul bertahap saat masuk viewport
    gsap.set('[data-reveal]', { y: 28 });
    ScrollTrigger.batch('[data-reveal]', {
        start: 'top 95%',
        once: true,
        onEnter: (batch) =>
            gsap.to(batch, { autoAlpha: 1, y: 0, stagger: 0.07, duration: 0.7, ease: 'power3.out', delay: 0.15, clearProps: 'transform' }),
    });

    // Field form muncul berurutan
    gsap.from('[data-stagger] > :not([type="hidden"])', {
        y: 16, autoAlpha: 0, stagger: 0.05, duration: 0.5, ease: 'power3.out', delay: 0.35, clearProps: 'transform',
    });
}

/* ---------- GSAP: tilt 3D kartu mengikuti kursor ---------- */
function tiltCards(root = document) {
    if (reduced || !finePointer || !root) return;
    $$('[data-tilt]', root).forEach((el) => {
        gsap.set(el, { transformPerspective: 900 });
        const rx = gsap.quickTo(el, 'rotationX', { duration: 0.45, ease: 'power3' });
        const ry = gsap.quickTo(el, 'rotationY', { duration: 0.45, ease: 'power3' });
        el.addEventListener('pointermove', (e) => {
            const r = el.getBoundingClientRect();
            ry(((e.clientX - r.left) / r.width - 0.5) * 6);
            rx(-((e.clientY - r.top) / r.height - 0.5) * 6);
        });
        el.addEventListener('pointerleave', () => { rx(0); ry(0); });
    });
}

/* ---------- GSAP: tombol magnetik ---------- */
function magnetic() {
    if (reduced || !finePointer) return;
    $$('[data-magnetic]').forEach((el) => {
        const x = gsap.quickTo(el, 'x', { duration: 0.4, ease: 'power3' });
        const y = gsap.quickTo(el, 'y', { duration: 0.4, ease: 'power3' });
        el.addEventListener('pointermove', (e) => {
            const r = el.getBoundingClientRect();
            x((e.clientX - r.left - r.width / 2) * 0.25);
            y((e.clientY - r.top - r.height / 2) * 0.35);
        });
        el.addEventListener('pointerleave', () => { x(0); y(0); });
    });
}

const springy = { type: 'spring', stiffness: 420, damping: 22 };

function bindPress(root = document) {
    if (reduced || !root) return;
    const targets = $$('[data-press]', root);
    if (!targets.length) return;
    press(targets, (el) => {
        animate(el, { scale: 0.94 }, springy);
        return () => animate(el, { scale: 1 }, springy);
    });
}

function bindHoverLift(root = document) {
    if (reduced || !root) return;
    const targets = $$('[data-hover-lift]', root);
    if (!targets.length) return;
    hover(targets, (el) => {
        animate(el, { y: -3 }, springy);
        return () => animate(el, { y: 0 }, springy);
    });
}

/* ---------- Motion: micro-interaction hover & tekan ---------- */
function microInteractions() {
    if (reduced) return;

    bindPress(document);
    bindHoverLift(document);

    // Kartu pilihan radio (warna kartu & kategori) memantul saat dipilih
    document.addEventListener('change', (e) => {
        if (e.target.matches('input[type="radio"]')) {
            const label = e.target.closest('label');
            if (label) animate(label, { scale: [1, 0.95, 1.03, 1] }, { duration: 0.45 });
        }
    });

    // Lonceng bergoyang jika ada pengingat
    const bellIcon = $('#reminderToggleBtn svg');
    if (bellIcon && $('#reminderBadge')) {
        animate(bellIcon, { rotate: [0, -18, 14, -10, 6, 0] }, { duration: 0.9, delay: 1.2 });
        animate('#reminderBadge', { scale: [0, 1.25, 1] }, { duration: 0.5, delay: 1.1 });
    }
}

/* ---------- Motion: popover pengingat ---------- */
function reminderPopover() {
    const btn = $('#reminderToggleBtn');
    const panel = $('#reminderDropdown');
    const wrap = $('#reminderContainer');
    if (!btn || !panel) return;

    let open = false;
    const show = () => {
        open = true;
        btn.setAttribute('aria-expanded', 'true');
        panel.classList.remove('hidden');
        if (reduced) return;
        animate(panel, { opacity: [0, 1], y: [-10, 0], scale: [0.96, 1] }, { type: 'spring', stiffness: 380, damping: 28 });
        animate($$('[data-reminder-item]', panel), { opacity: [0, 1], x: [12, 0] }, { delay: stagger(0.04, { startDelay: 0.08 }), duration: 0.3 });
    };
    const hide = async () => {
        if (!open) return;
        open = false;
        btn.setAttribute('aria-expanded', 'false');
        if (!reduced) await animate(panel, { opacity: 0, y: -8, scale: 0.97 }, { duration: 0.15 });
        panel.classList.add('hidden');
    };

    btn.addEventListener('click', (e) => { e.stopPropagation(); open ? hide() : show(); });
    document.addEventListener('click', (e) => { if (!wrap.contains(e.target)) hide(); });
    document.addEventListener('keydown', (e) => { if (e.key === 'Escape') hide(); });
}

/* ---------- Motion: modal kelola kategori & status ---------- */
function manageModal() {
    const modal = $('#manageModal');
    if (!modal) return;
    const box = $('[data-modal-box]', modal);

    const open = () => {
        modal.classList.remove('hidden');
        lenis?.stop();
        if (reduced) return;
        animate(modal, { opacity: [0, 1] }, { duration: 0.2 });
        animate(box, { opacity: [0, 1], y: [30, 0], scale: [0.95, 1] }, { type: 'spring', stiffness: 320, damping: 26 });
    };
    const close = async () => {
        if (!reduced) {
            await Promise.all([
                animate(box, { opacity: 0, y: 20, scale: 0.97 }, { duration: 0.18 }),
                animate(modal, { opacity: 0 }, { duration: 0.2 }),
            ]);
        }
        modal.classList.add('hidden');
        lenis?.start();
    };

    $('#openManageModalBtn')?.addEventListener('click', open);
    $$('[data-modal-close]', modal).forEach((b) => b.addEventListener('click', close));
    modal.addEventListener('click', (e) => { if (e.target === modal) close(); });
    document.addEventListener('keydown', (e) => { if (e.key === 'Escape' && !modal.classList.contains('hidden')) close(); });

    // Tab dengan indikator geser
    const tabs = $$('[data-tab]', modal);
    const indicator = $('[data-tab-indicator]', modal);
    const moveIndicator = (tab) => {
        if (!indicator) return;
        const opts = reduced ? { duration: 0 } : { type: 'spring', stiffness: 400, damping: 32 };
        animate(indicator, { x: tab.offsetLeft, width: tab.offsetWidth }, opts);
    };
    tabs.forEach((tab) =>
        tab.addEventListener('click', () => {
            tabs.forEach((t) => {
                const active = t === tab;
                t.classList.toggle('text-[#0c4a6e]', active);
                t.classList.toggle('text-slate-400', !active);
                t.setAttribute('aria-selected', String(active));
                $(`#${t.dataset.tab}`).classList.toggle('hidden', !active);
            });
            moveIndicator(tab);
            const panel = $(`#${tab.dataset.tab}`);
            if (!reduced) animate(panel.children, { opacity: [0, 1], y: [10, 0] }, { delay: stagger(0.05), duration: 0.3 });
        }),
    );
    // Posisi awal indikator (setelah modal tampil pertama kali)
    $('#openManageModalBtn')?.addEventListener('click', () => {
        const active = tabs.find((t) => t.getAttribute('aria-selected') === 'true') ?? tabs[0];
        if (active) requestAnimationFrame(() => moveIndicator(active));
    });
}

/* ---------- Flash message: masuk lalu hilang otomatis ---------- */
function flashMessages() {
    $$('[data-flash]').forEach((el) => {
        if (!reduced) animate(el, { opacity: [0, 1], y: [-12, 0] }, { type: 'spring', stiffness: 300, damping: 24 });
        const dismiss = async () => {
            if (!el.isConnected) return;
            if (!reduced) await animate(el, { opacity: 0, height: 0, marginBottom: 0, paddingTop: 0, paddingBottom: 0 }, { duration: 0.3 });
            el.remove();
        };
        $('button', el)?.addEventListener('click', dismiss);
        setTimeout(dismiss, 5000);
    });
}

/* ---------- Navigasi parsial Jadwal: pindah tanggal & filter pengganti hanya menganimasikan jadwal ---------- */
function jadwalNavigation() {
    const viewer = $('#scheduleViewer');
    if (!viewer) return;

    let ctrl = null;

    const loadJadwal = async (href, push = true) => {
        const nextUrl = new URL(href, location.origin);
        const prevDate = viewer.dataset.selectedDate || '';
        const prevPengganti = viewer.dataset.showPengganti ?? '1';
        const prevHideNon = viewer.dataset.hideNonAkademik ?? '0';

        const nextDate = nextUrl.searchParams.get('tanggal') || viewer.dataset.todayDate || prevDate;
        const nextPengganti = nextUrl.searchParams.get('show_pengganti') ?? '1';
        const nextHideNon = nextUrl.searchParams.get('hide_non_akademik') ?? '0';

        const dateChanged = nextDate !== prevDate;
        const penggantiChanged = nextPengganti !== prevPengganti;
        const hideNonChanged = nextHideNon !== prevHideNon;

        if (push && !dateChanged && !penggantiChanged && !hideNonChanged) return;

        const animateSchedule = dateChanged || penggantiChanged;
        const dir = dateChanged && prevDate && nextDate < prevDate ? -1 : 1;

        ctrl?.abort();
        ctrl = new AbortController();

        const currentBody = $('[data-schedule-body]', viewer);
        const currentDateEl = $('[data-schedule-date]', viewer);
        const outgoing = animateSchedule
            ? (dateChanged ? [currentDateEl, currentBody] : [currentBody]).filter(Boolean)
            : [];
        if (outgoing.length) gsap.killTweensOf(outgoing);

        try {
            const fadeOut = reduced || !outgoing.length
                ? Promise.resolve()
                : gsap.to(outgoing, {
                    autoAlpha: 0,
                    x: dateChanged ? -16 * dir : 0,
                    y: dateChanged ? 0 : -10,
                    duration: 0.15,
                    ease: 'power2.in',
                });

            const [res] = await Promise.all([
                fetch(nextUrl.href, { signal: ctrl.signal, headers: { 'X-Requested-With': 'XMLHttpRequest' } }),
                fadeOut,
            ]);
            if (!res.ok) throw new Error(res.statusText);

            const doc = new DOMParser().parseFromString(await res.text(), 'text/html');
            const nextViewer = $('#scheduleViewer', doc);
            if (!nextViewer) {
                location.href = nextUrl.href;
                return;
            }

            // Sinkronkan kalender, filter, statistik, & tautan tambah tanpa animasi ulang
            for (const sel of ['#miniCalendar', '#filterCard', '#statBebanSks', '#statTotalSesi']) {
                const cur = $(sel);
                const incoming = $(sel, doc);
                if (cur && incoming) cur.innerHTML = incoming.innerHTML;
            }
            const curBtn = $('#createScheduleBtn');
            const incomingBtn = $('#createScheduleBtn', doc);
            if (curBtn && incomingBtn) curBtn.href = incomingBtn.href;

            if (hideNonChanged) {
                const curNon = $('#nonAkademikSlot');
                const incomingNon = $('#nonAkademikSlot', doc);
                if (curNon && incomingNon) {
                    curNon.innerHTML = incomingNon.innerHTML;
                    gsap.set($$('[data-reveal]', curNon), { autoAlpha: 1 });
                    bindHoverLift(curNon);
                }
            }

            // Perbarui state & isi penampil jadwal
            viewer.dataset.selectedDate = nextViewer.dataset.selectedDate || nextDate;
            viewer.dataset.showPengganti = nextViewer.dataset.showPengganti ?? nextPengganti;
            viewer.dataset.hideNonAkademik = nextViewer.dataset.hideNonAkademik ?? nextHideNon;
            viewer.innerHTML = nextViewer.innerHTML;
            gsap.set($$('[data-reveal]', viewer), { autoAlpha: 1 });

            bindPress($('#miniCalendar'));
            bindPress($('#filterCard'));
            bindPress(viewer);
            tiltCards(viewer);
            ScrollTrigger.refresh();

            if (push) history.pushState({ tanggal: viewer.dataset.selectedDate }, '', nextUrl.href);

            if (!reduced && animateSchedule) {
                const newDateEl = $('[data-schedule-date]', viewer);
                const items = $$('[data-schedule-item]', viewer);
                if (dateChanged && newDateEl) {
                    gsap.fromTo(
                        newDateEl,
                        { autoAlpha: 0, x: 16 * dir },
                        { autoAlpha: 1, x: 0, duration: 0.28, ease: 'power3.out', clearProps: 'transform' },
                    );
                }
                if (items.length) {
                    gsap.fromTo(
                        items,
                        { autoAlpha: 0, x: dateChanged ? 22 * dir : 0, y: dateChanged ? 8 : 14 },
                        { autoAlpha: 1, x: 0, y: 0, stagger: 0.055, duration: 0.4, ease: 'power3.out', clearProps: 'transform' },
                    );
                }
            }
        } catch (err) {
            if (err.name === 'AbortError') return;
            location.href = nextUrl.href;
        }
    };

    document.addEventListener('click', (e) => {
        const a = e.target.closest('a[data-jadwal-nav]');
        if (!a || e.defaultPrevented || e.button !== 0 || e.metaKey || e.ctrlKey || e.shiftKey || e.altKey) return;
        e.preventDefault();
        loadJadwal(a.href, true);
    });

    window.addEventListener('popstate', () => {
        if ($('#scheduleViewer')) loadJadwal(location.href, false);
    });
}

/* ---------- Navigasi parsial Agenda Non-Akademik: hanya daftar agenda yang beranimasi ---------- */
function agendaNavigation() {
    const viewer = $('#agendaViewer');
    if (!viewer) return;

    let ctrl = null;

    const loadAgenda = async (href, push = true) => {
        const nextUrl = new URL(href, location.origin);
        const prevKat = (viewer.dataset.selectedKategori || 'SEMUA').toUpperCase();
        const nextKat = (nextUrl.searchParams.get('kategori') || 'SEMUA').toUpperCase();
        if (push && nextKat === prevKat) return;

        ctrl?.abort();
        ctrl = new AbortController();

        const grid = $('#agendaGrid', viewer);
        if (grid) gsap.killTweensOf([grid, ...$$('[data-agenda-item]', grid)]);

        try {
            const fadeOut = reduced || !grid
                ? Promise.resolve()
                : gsap.to(grid, { autoAlpha: 0, y: -10, duration: 0.14, ease: 'power2.in' });

            const [res] = await Promise.all([
                fetch(nextUrl.href, { signal: ctrl.signal, headers: { 'X-Requested-With': 'XMLHttpRequest' } }),
                fadeOut,
            ]);
            if (!res.ok) throw new Error(res.statusText);

            const doc = new DOMParser().parseFromString(await res.text(), 'text/html');
            const nextViewer = $('#agendaViewer', doc);
            const nextChips = $('#agendaChips', doc);
            const nextGrid = $('#agendaGrid', doc);
            if (!nextViewer || !nextGrid || !grid) {
                location.href = nextUrl.href;
                return;
            }

            // Sinkronkan bar chip kategori tanpa menganimasikan kartu luar / header / sidebar
            const chips = $('#agendaChips', viewer);
            if (chips && nextChips) {
                chips.innerHTML = nextChips.innerHTML;
                bindPress(chips);
            }

            viewer.dataset.selectedKategori = nextViewer.dataset.selectedKategori || nextKat;
            grid.innerHTML = nextGrid.innerHTML;
            gsap.set(grid, { autoAlpha: 1, clearProps: 'transform' });
            gsap.set($$('[data-reveal]', grid), { autoAlpha: 1 });

            bindPress(grid);
            tiltCards(grid);
            ScrollTrigger.refresh();

            if (push) history.pushState({ kategori: viewer.dataset.selectedKategori }, '', nextUrl.href);

            if (!reduced) {
                const items = $$('[data-agenda-item]', grid);
                if (items.length) {
                    gsap.fromTo(
                        items,
                        { autoAlpha: 0, y: 18, scale: 0.98 },
                        { autoAlpha: 1, y: 0, scale: 1, stagger: 0.055, duration: 0.4, ease: 'power3.out', clearProps: 'transform' },
                    );
                }
            }
        } catch (err) {
            if (err.name === 'AbortError') return;
            location.href = nextUrl.href;
        }
    };

    document.addEventListener('click', (e) => {
        const a = e.target.closest('a[data-agenda-nav]');
        if (!a || e.defaultPrevented || e.button !== 0 || e.metaKey || e.ctrlKey || e.shiftKey || e.altKey) return;
        e.preventDefault();
        loadAgenda(a.href, true);
    });

    window.addEventListener('popstate', () => {
        if ($('#agendaViewer')) loadAgenda(location.href, false);
    });
}

/* ---------- Transisi antar halaman (GSAP) ---------- */
function pageTransitions() {
    if (reduced) return;
    const main = $('[data-page]');
    if (!main) return;

    document.addEventListener('click', (e) => {
        const a = e.target.closest('a[href]');
        if (!a || e.defaultPrevented || e.button !== 0 || e.metaKey || e.ctrlKey || e.shiftKey || e.altKey) return;
        if (
            a.target ||
            a.hasAttribute('download') ||
            a.dataset.noTransition !== undefined ||
            a.dataset.jadwalNav !== undefined ||
            a.dataset.agendaNav !== undefined
        ) return;
        const url = new URL(a.href, location.href);
        if (url.origin !== location.origin || url.hash || a.getAttribute('href') === '#') return;

        e.preventDefault();
        gsap.to(main, { autoAlpha: 0, y: 12, duration: 0.25, ease: 'power2.in', onComplete: () => (location.href = url.href) });
    });

    // Kembali via tombol back (bfcache): tampilkan lagi
    window.addEventListener('pageshow', (e) => { if (e.persisted) gsap.set(main, { autoAlpha: 1, y: 0 }); });
}

// Satu fitur gagal tidak boleh membuat konten tetap tersembunyi
for (const init of [intro, tiltCards, magnetic, microInteractions, reminderPopover, manageModal, flashMessages, jadwalNavigation, agendaNavigation, pageTransitions]) {
    try {
        init();
    } catch (err) {
        console.error(err);
        gsap.set('[data-anim], [data-reveal]', { autoAlpha: 1, clearProps: 'transform' });
    }
}

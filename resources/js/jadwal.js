const $ = (sel, root = document) => root.querySelector(sel);
const $$ = (sel, root = document) => [...root.querySelectorAll(sel)];

// 1. Popover lonceng pengingat di navbar
function initReminder() {
    const btn = $('#reminderToggleBtn');
    const panel = $('#reminderDropdown');
    const wrap = $('#reminderContainer');
    if (!btn || !panel) return;

    const setOpen = (open) => {
        panel.classList.toggle('hidden', !open);
        btn.setAttribute('aria-expanded', String(open));
    };

    btn.addEventListener('click', (e) => {
        e.stopPropagation();
        setOpen(panel.classList.contains('hidden'));
    });
    document.addEventListener('click', (e) => { if (!wrap?.contains(e.target)) setOpen(false); });
    document.addEventListener('keydown', (e) => { if (e.key === 'Escape') setOpen(false); });
}

// 2. Modal kelola kategori & status agenda
function initManageModal() {
    const modal = $('#manageModal');
    if (!modal) return;

    const tabs = $$('[data-tab]', modal);
    const indicator = $('[data-tab-indicator]', modal);
    const syncIndicator = (tab) => {
        if (!indicator || !tab) return;
        indicator.style.transform = `translateX(${tab.offsetLeft}px)`;
        indicator.style.width = `${tab.offsetWidth}px`;
    };

    const setOpen = (open) => {
        modal.classList.toggle('hidden', !open);
        if (open) {
            const active = tabs.find((t) => t.getAttribute('aria-selected') === 'true') ?? tabs[0];
            requestAnimationFrame(() => syncIndicator(active));
        }
    };

    $('#openManageModalBtn')?.addEventListener('click', () => setOpen(true));
    $$('[data-modal-close]', modal).forEach((b) => b.addEventListener('click', () => setOpen(false)));
    modal.addEventListener('click', (e) => { if (e.target === modal) setOpen(false); });
    document.addEventListener('keydown', (e) => { if (e.key === 'Escape' && !modal.classList.contains('hidden')) setOpen(false); });

    tabs.forEach((tab) => tab.addEventListener('click', () => {
        tabs.forEach((t) => {
            const active = t === tab;
            t.classList.toggle('text-[#1d4ed8]', active);
            t.classList.toggle('text-slate-400', !active);
            t.setAttribute('aria-selected', String(active));
            $(`#${t.dataset.tab}`)?.classList.toggle('hidden', !active);
        });
        syncIndicator(tab);
    }));
}

// 3. Tutup flash message otomatis & tombol silang
function initFlash() {
    $$('[data-flash]').forEach((el) => {
        $('button', el)?.addEventListener('click', () => el.remove());
        setTimeout(() => el.remove(), 5000);
    });
}

// 4. Navigasi parsial tanpa reload penuh untuk Jadwal & Agenda
function initPartialNav(attr, selectors, afterSync) {
    let ctrl = null;
    const load = async (href, push = true) => {
        ctrl?.abort();
        ctrl = new AbortController();
        try {
            const res = await fetch(href, { signal: ctrl.signal, headers: { 'X-Requested-With': 'XMLHttpRequest' } });
            if (!res.ok) throw new Error(res.statusText);
            const doc = new DOMParser().parseFromString(await res.text(), 'text/html');
            for (const sel of selectors) {
                const cur = $(sel);
                const next = $(sel, doc);
                if (cur && next) cur.innerHTML = next.innerHTML;
            }
            afterSync?.(doc);
            if (push) history.pushState({}, '', href);
        } catch (err) {
            if (err.name !== 'AbortError') location.href = href;
        }
    };

    document.addEventListener('click', (e) => {
        const a = e.target.closest(`a[${attr}]`);
        if (!a || e.defaultPrevented || e.button !== 0 || e.metaKey || e.ctrlKey || e.shiftKey || e.altKey) return;
        e.preventDefault();
        load(a.href, true);
    });
    window.addEventListener('popstate', () => {
        if ($(selectors[0])) load(location.href, false);
    });
}

initReminder();
initManageModal();
initFlash();
initPartialNav('data-jadwal-nav', ['#scheduleViewer', '#miniCalendar', '#filterCard', '#statBebanSks', '#statTotalSesi', '#nonAkademikSlot'], (doc) => {
    const cur = $('#createScheduleBtn');
    const next = $('#createScheduleBtn', doc);
    if (cur && next) cur.href = next.href;
});
initPartialNav('data-agenda-nav', ['#agendaChips', '#agendaGrid']);

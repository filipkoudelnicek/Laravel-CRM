document.addEventListener('DOMContentLoaded', () => {
    const menu = document.querySelector('.workspace-menu-button');
    const backdrop = document.querySelector('.workspace-backdrop');
    const toggleMenu = (open) => {
        document.body.classList.toggle('navigation-open', open);
        menu?.setAttribute('aria-expanded', String(open));
        if (backdrop) backdrop.hidden = !open;
    };
    menu?.addEventListener('click', () => {
        toggleMenu(!document.body.classList.contains('navigation-open'));
        if (document.body.classList.contains('navigation-open')) document.querySelector('.workspace-sidebar a')?.focus();
    });
    backdrop?.addEventListener('click', () => { toggleMenu(false); menu?.focus(); });
    document.addEventListener('keydown', event => {
        if (!document.body.classList.contains('navigation-open')) return;
        if (event.key === 'Escape') { toggleMenu(false); menu?.focus(); }
        if (event.key === 'Tab') {
            const links = [...document.querySelectorAll('.workspace-sidebar a')];
            const first = links[0], last = links[links.length - 1];
            if (event.shiftKey && document.activeElement === first) { event.preventDefault(); last.focus(); }
            else if (!event.shiftKey && document.activeElement === last) { event.preventDefault(); first.focus(); }
        }
    });

    const theme = document.getElementById('darkModeToggle');
    theme?.addEventListener('click', async () => {
        const next = !document.body.classList.contains('dark-mode');
        theme.disabled = true;
        try {
            const response = await fetch(theme.dataset.url, {
                method: 'POST', headers: {'Content-Type': 'application/json', 'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content},
                body: JSON.stringify({dark_mode: next})
            });
            if (!response.ok) throw new Error('theme');
            const data = await response.json();
            document.body.classList.toggle('dark-mode', !!data.dark_mode);
            theme.textContent = data.dark_mode ? 'Světlý režim' : 'Tmavý režim';
            theme.setAttribute('aria-pressed', String(!!data.dark_mode));
            theme.setAttribute('aria-label', data.dark_mode ? 'Zapnout světlý režim' : 'Zapnout tmavý režim');
        } catch { window.showToast?.('Vzhled se nepodařilo uložit. Zkuste to znovu.', 'error'); }
        finally { theme.disabled = false; }
    });

    document.querySelectorAll('[data-timer-start]').forEach(element => {
        const start = Date.parse(element.dataset.timerStart);
        const serverNow = Date.parse(element.dataset.serverNow);
        const mounted = performance.now();
        const tick = () => {
            const seconds = Math.max(0, Math.floor((serverNow + performance.now() - mounted - start) / 1000));
            element.textContent = [Math.floor(seconds / 3600), Math.floor(seconds % 3600 / 60), seconds % 60]
                .map(value => String(value).padStart(2, '0')).join(':');
        };
        tick(); setInterval(tick, 1000);
    });

    document.querySelectorAll('[data-client-project-form]').forEach(form => {
        const client = form.querySelector('[name=client_id]');
        const project = form.querySelector('[name=project_id]');
        if (!client || !project) return;
        const filter = () => {
            [...project.options].forEach(option => {
                const available = !option.value || option.dataset.client === client.value;
                option.hidden = !available; option.disabled = !available;
            });
            if (project.selectedOptions[0]?.disabled) project.value = '';
        };
        client.addEventListener('change', filter); filter();
    });
});

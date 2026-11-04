document.addEventListener('DOMContentLoaded', () => {
    const editors = new Map();
    const initializingEditors = new WeakSet();
    const initializeEditor = textarea => {
        if (!window.ClassicEditor || editors.has(textarea) || initializingEditors.has(textarea)) return;
        initializingEditors.add(textarea);
        window.ClassicEditor.create(textarea, {
            toolbar: ['undo', 'redo', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote']
        }).then(editor => {
            editors.set(textarea, editor);
            // Native form validation runs before submit, so waiting until submit
            // leaves a required, hidden textarea empty and blocks valid comments.
            const sync = () => editor.updateSourceElement();
            editor.model.document.on('change:data', sync);
            sync();
            textarea.addEventListener('invalid', event => {
                event.preventDefault();
                editor.editing.view.focus();
                window.showToast?.(textarea.name === 'body' ? 'Napište text komentáře.' : 'Vyplňte text pole.', 'error');
            });
        }).catch(() => {
            // A failed CDN/editor initialization keeps the ordinary textarea usable.
        }).finally(() => initializingEditors.delete(textarea));
    };
    document.querySelectorAll('textarea.rich-editor-source').forEach(textarea => {
        if (textarea.offsetParent !== null) initializeEditor(textarea);
    });
    document.addEventListener('focusin', event => {
        if (event.target.matches('textarea.rich-editor-source')) initializeEditor(event.target);
    });
    document.addEventListener('submit', event => {
        event.target.querySelectorAll('textarea.rich-editor-source').forEach(textarea => editors.get(textarea)?.updateSourceElement());
    });

    // Move menus outside scroll containers so the last row never gets clipped.
    let activeActions = null;
    const closeActions = (restoreFocus = false) => {
        if (!activeActions) return;
        const {trigger, popup, host} = activeActions;
        popup.hidden = true;
        host.append(popup);
        trigger.setAttribute('aria-expanded', 'false');
        activeActions = null;
        if (restoreFocus) trigger.focus();
    };
    const openActions = (trigger, focusLast = false) => {
        const host = trigger.closest('[data-row-actions]');
        const popup = host.querySelector('.action-menu');
        closeActions();
        document.body.append(popup);
        popup.hidden = false;
        trigger.setAttribute('aria-expanded', 'true');
        const rect = trigger.getBoundingClientRect();
        const width = popup.offsetWidth, height = popup.offsetHeight;
        popup.style.left = `${Math.max(12, Math.min(rect.right - width, innerWidth - width - 12))}px`;
        const below = rect.bottom + 5;
        popup.style.top = `${Math.max(12, below + height <= innerHeight - 12 ? below : rect.top - height - 5)}px`;
        activeActions = {trigger, popup, host};
        const items = [...popup.querySelectorAll('[role=menuitem]')];
        (focusLast ? items.at(-1) : items[0])?.focus();
    };
    document.querySelectorAll('[data-action-trigger]').forEach(trigger => {
        trigger.addEventListener('click', () => activeActions?.trigger === trigger ? closeActions(true) : openActions(trigger));
        trigger.addEventListener('keydown', event => {
            if (['ArrowDown', 'ArrowUp'].includes(event.key)) {
                event.preventDefault(); openActions(trigger, event.key === 'ArrowUp');
            }
        });
    });
    document.addEventListener('pointerdown', event => {
        if (activeActions && !activeActions.popup.contains(event.target) && !activeActions.trigger.contains(event.target)) closeActions();
    });
    document.addEventListener('keydown', event => {
        if (!activeActions) return;
        if (event.key === 'Escape') { event.preventDefault(); closeActions(true); return; }
        if (event.key === 'Tab') { closeActions(true); return; }
        const items = [...activeActions.popup.querySelectorAll('[role=menuitem]')];
        const index = items.indexOf(document.activeElement);
        let next;
        if (event.key === 'ArrowDown') next = (index + 1) % items.length;
        if (event.key === 'ArrowUp') next = (index - 1 + items.length) % items.length;
        if (event.key === 'Home') next = 0;
        if (event.key === 'End') next = items.length - 1;
        if (next !== undefined) { event.preventDefault(); items[next]?.focus(); }
    });
    window.addEventListener('resize', () => closeActions());
    window.addEventListener('scroll', event => {
        if (activeActions && !activeActions.popup.contains(event.target)) closeActions();
    }, true);

    const dialog = document.getElementById('confirm-dialog');
    let pendingDelete = null;
    const approvedForms = new WeakSet();
    document.addEventListener('submit', event => {
        const form = event.target;
        if (!form.matches('[data-confirm]')) return;
        if (approvedForms.has(form)) { approvedForms.delete(form); return; }
        event.preventDefault();
        if (!dialog?.showModal) {
            if (window.confirm(form.dataset.confirm)) { approvedForms.add(form); form.requestSubmit(event.submitter); }
            return;
        }
        // The form may live inside a menu that is about to be closed.
        pendingDelete = {form, submitter: event.submitter, trigger: activeActions?.trigger || document.activeElement};
        closeActions();
        dialog.querySelector('#confirm-title').textContent = form.dataset.confirm;
        dialog.querySelector('#confirm-description').textContent = form.dataset.confirmDetail || 'Tuto akci nelze vrátit zpět.';
        dialog.returnValue = 'cancel';
        dialog.showModal();
    });
    dialog?.addEventListener('close', () => {
        if (!pendingDelete) return;
        const {form, submitter, trigger} = pendingDelete;
        pendingDelete = null;
        if (dialog.returnValue === 'confirm') { approvedForms.add(form); form.requestSubmit(submitter); }
        else trigger?.focus();
    });
    document.querySelectorAll('[data-status-form] select').forEach(select => {
        select.addEventListener('change', () => select.form.requestSubmit());
    });
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

(() => {
    const toggle = document.querySelector('#sidebarToggle');
    const sidebar = document.querySelector('#adminSidebar');
    const backdrop = document.querySelector('#sidebarBackdrop');
    if (!toggle || !sidebar || !backdrop) return;
    const mobile = window.matchMedia('(max-width: 767.98px)');
    const sync = () => {
        const open = mobile.matches ? document.body.classList.contains('sidebar-open') : !document.body.classList.contains('sidebar-collapsed');
        toggle.setAttribute('aria-expanded', String(open));
        sidebar.inert = !open;
        backdrop.hidden = !(mobile.matches && open);
    };
    toggle.addEventListener('click', () => {
        document.body.classList.toggle(mobile.matches ? 'sidebar-open' : 'sidebar-collapsed');
        sync();
        if (mobile.matches && document.body.classList.contains('sidebar-open')) sidebar.querySelector('a')?.focus();
    });
    const close = () => { document.body.classList.remove('sidebar-open'); sync(); toggle.focus(); };
    backdrop.addEventListener('click', close);
    document.addEventListener('keydown', event => { if (event.key === 'Escape' && mobile.matches && document.body.classList.contains('sidebar-open')) close(); });
    mobile.addEventListener('change', () => { document.body.classList.remove('sidebar-open'); sync(); });
    sync();
})();

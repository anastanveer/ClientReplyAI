import './bootstrap';

// ── Theme store (Alpine) ──
document.addEventListener('alpine:init', () => {
    Alpine.store('theme', {
        // Read from localStorage — reliable across Alpine re-inits and wire:navigate
        dark: localStorage.getItem('theme') === 'dark' ||
              (!localStorage.getItem('theme') && window.matchMedia('(prefers-color-scheme: dark)').matches),

        toggle() {
            this.dark = !this.dark;
            document.documentElement.classList.toggle('dark', this.dark);
            localStorage.setItem('theme', this.dark ? 'dark' : 'light');
        },
    });
});

// ── Re-apply theme after wire:navigate page transitions ──
// wire:navigate morphs the DOM and can strip the dark class from <html>
document.addEventListener('livewire:navigated', () => {
    const savedTheme = localStorage.getItem('theme');
    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
    const isDark = savedTheme === 'dark' || (!savedTheme && prefersDark);

    document.documentElement.classList.toggle('dark', isDark);

    // Sync Alpine store if available
    if (typeof Alpine !== 'undefined') {
        try { Alpine.store('theme').dark = isDark; } catch (_) {}
    }
});

// ── PWA service worker ──
if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
        navigator.serviceWorker
            .register('/sw.js', { scope: '/' })
            .catch(() => {});
    });
}

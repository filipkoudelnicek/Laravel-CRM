import './bootstrap';

// Alpine.js je již součástí Livewire, není potřeba ho načítat znovu

// Zajistit, aby se dark mode načítal při každé navigaci
document.addEventListener('livewire:navigating', () => {
    const saved = localStorage.getItem('darkMode');
    if (saved === 'true') {
        document.documentElement.classList.add('dark');
    } else if (saved === 'false') {
        document.documentElement.classList.remove('dark');
    }
});

document.addEventListener('livewire:navigated', () => {
    const saved = localStorage.getItem('darkMode');
    if (saved === 'true') {
        document.documentElement.classList.add('dark');
    } else if (saved === 'false') {
        document.documentElement.classList.remove('dark');
    }
    
    // Aktualizovat UI pokud existuje funkce
    if (typeof updateDarkModeUI === 'function') {
        const isDark = document.documentElement.classList.contains('dark');
        updateDarkModeUI(isDark);
    }
});

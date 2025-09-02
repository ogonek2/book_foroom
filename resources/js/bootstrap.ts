// Simple theme initialization
function initializeTheme() {
    const appearance = document.documentElement.getAttribute('data-appearance') || 'system';
    
    if (appearance === 'system') {
        const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
        if (prefersDark) {
            document.documentElement.classList.add('dark');
        }
    } else if (appearance === 'dark') {
        document.documentElement.classList.add('dark');
    }
}

// Initialize theme on page load
document.addEventListener('DOMContentLoaded', initializeTheme);

// Export for potential use in other modules
export { initializeTheme };

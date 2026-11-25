const STORAGE_KEY = 'dark-theme';

function resolveMode() {
    const stored = localStorage.getItem(STORAGE_KEY);

    if (stored === 'true') {
        return 'dark';
    }

    if (stored === 'false') {
        return 'light';
    }

    if (['light', 'dark', 'system'].includes(stored)) {
        return stored;
    }

    return 'light';
}

function isDark(mode = resolveMode()) {
    return mode === 'dark'
        || (mode !== 'light' && window.matchMedia('(prefers-color-scheme: dark)').matches);
}

/**
 * Troca só data-theme / .dark.
 * Sidebar:
 * - tema dark  → sempre dark (CSS)
 * - tema light → data-sidebar-theme light|dark (CSS)
 */
export function applyTheme(dark = isDark()) {
    const theme = dark ? 'dark' : 'light';

    document.documentElement.classList.toggle('dark', dark);
    document.documentElement.dataset.theme = theme;
    document.body?.setAttribute('data-theme', theme);
}

export function bootTheme() {
    window.tsuiApplyTheme = applyTheme;

    applyTheme();

    const originalSetItem = Storage.prototype.setItem;

    Storage.prototype.setItem = function (key, value) {
        originalSetItem.call(this, key, value);

        if (this === localStorage && key === STORAGE_KEY) {
            applyTheme();
        }
    };

    window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', () => {
        if (resolveMode() === 'system') {
            applyTheme();
        }
    });

    document.addEventListener('livewire:navigated', () => applyTheme());
}

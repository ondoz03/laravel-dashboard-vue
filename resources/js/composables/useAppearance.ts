import { onMounted, ref } from 'vue';

type Appearance = 'light' | 'dark' | 'system';
type ColorTheme = 'blue' | 'green' | 'purple' | 'red' | 'orange' | 'default';

export function updateTheme(value: Appearance, colorTheme: ColorTheme = 'default') {
    if (typeof window === 'undefined') {
        return;
    }

    // Remove all color theme classes
    document.documentElement.classList.remove('theme-blue', 'theme-green', 'theme-purple', 'theme-red', 'theme-orange');

    // Add the selected color theme class if not default
    if (colorTheme !== 'default') {
        document.documentElement.classList.add(`theme-${colorTheme}`);
    }

    if (value === 'system') {
        const mediaQueryList = window.matchMedia('(prefers-color-scheme: dark)');
        const systemTheme = mediaQueryList.matches ? 'dark' : 'light';

        document.documentElement.classList.toggle('dark', systemTheme === 'dark');
    } else {
        document.documentElement.classList.toggle('dark', value === 'dark');
    }
}

const setCookie = (name: string, value: string, days = 365) => {
    if (typeof document === 'undefined') {
        return;
    }

    const maxAge = days * 24 * 60 * 60;

    document.cookie = `${name}=${value};path=/;max-age=${maxAge};SameSite=Lax`;
};

const mediaQuery = () => {
    if (typeof window === 'undefined') {
        return null;
    }

    return window.matchMedia('(prefers-color-scheme: dark)');
};

const getStoredAppearance = () => {
    if (typeof window === 'undefined') {
        return null;
    }

    return localStorage.getItem('appearance') as Appearance | null;
};

const getStoredColorTheme = () => {
    if (typeof window === 'undefined') {
        return null;
    }

    return localStorage.getItem('colorTheme') as ColorTheme | null;
};

const handleSystemThemeChange = () => {
    const currentAppearance = getStoredAppearance();
    const currentColorTheme = getStoredColorTheme();

    updateTheme(currentAppearance || 'system', currentColorTheme || 'default');
};

export function initializeTheme() {
    if (typeof window === 'undefined') {
        return;
    }

    // Initialize theme from saved preference or default to system...
    const savedAppearance = getStoredAppearance();
    const savedColorTheme = getStoredColorTheme();
    updateTheme(savedAppearance || 'system', savedColorTheme || 'default');

    // Set up system theme change listener...
    mediaQuery()?.addEventListener('change', handleSystemThemeChange);
}

export function useAppearance() {
    const appearance = ref<Appearance>('system');
    const colorTheme = ref<ColorTheme>('default');

    onMounted(() => {
        initializeTheme();

        const savedAppearance = localStorage.getItem('appearance') as Appearance | null;
        const savedColorTheme = localStorage.getItem('colorTheme') as ColorTheme | null;

        if (savedAppearance) {
            appearance.value = savedAppearance;
        }

        if (savedColorTheme) {
            colorTheme.value = savedColorTheme;
        }
    });

    function updateAppearance(value: Appearance) {
        appearance.value = value;

        // Store in localStorage for client-side persistence...
        localStorage.setItem('appearance', value);

        // Store in cookie for SSR...
        setCookie('appearance', value);

        updateTheme(value, colorTheme.value);
    }

    function updateColorTheme(value: ColorTheme) {
        colorTheme.value = value;

        // Store in localStorage for client-side persistence...
        localStorage.setItem('colorTheme', value);

        // Store in cookie for SSR...
        setCookie('colorTheme', value);

        updateTheme(appearance.value, value);
    }

    return {
        appearance,
        updateAppearance,
        colorTheme,
        updateColorTheme,
    };
}

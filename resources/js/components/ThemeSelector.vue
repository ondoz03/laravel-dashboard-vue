<script setup lang="ts">
import { useAppearance } from '../composables/useAppearance';
import { Sun, Moon, Monitor, Palette } from 'lucide-vue-next';

const { appearance, updateAppearance, colorTheme, updateColorTheme } = useAppearance();

const themes = [
    { value: 'light', icon: Sun, label: 'Light Mode' },
    { value: 'dark', icon: Moon, label: 'Dark Mode' },
    { value: 'system', icon: Monitor, label: 'System Default' },
];

const colorThemes = [
    { value: 'default', icon: Palette, label: 'Default', color: '#3b82f6' },
    { value: 'blue', icon: Palette, label: 'Blue', color: '#2563eb' },
    { value: 'green', icon: Palette, label: 'Green', color: '#16a34a' },
    { value: 'purple', icon: Palette, label: 'Purple', color: '#9333ea' },
    { value: 'red', icon: Palette, label: 'Red', color: '#dc2626' },
    { value: 'orange', icon: Palette, label: 'Orange', color: '#ea580c' },
];
</script>

<template>
    <div class="rounded-lg border p-6">
        <div class="mb-4">
            <h3 class="text-lg font-medium">Theme Settings</h3>
            <p class="text-sm text-neutral-500 dark:text-neutral-400">
                Choose your preferred theme for the application interface.
            </p>
        </div>

        <!-- Appearance Theme Options -->
        <div class="mb-6">
            <h4 class="mb-3 text-base font-medium">Mode</h4>
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                <div
                    v-for="theme in themes"
                    :key="theme.value"
                    @click="updateAppearance(theme.value)"
                    :class="[
                        'flex cursor-pointer flex-col items-center justify-center rounded-lg border p-4 transition-colors',
                        appearance === theme.value
                            ? 'border-primary-500 bg-primary-50 dark:border-primary-400 dark:bg-primary-900/20'
                            : 'hover:border-neutral-300 hover:bg-neutral-50 dark:hover:border-neutral-600 dark:hover:bg-neutral-800/50'
                    ]"
                >
                    <component :is="theme.icon" class="mb-2 h-8 w-8" />
                    <span class="font-medium">{{ theme.label }}</span>
                </div>
            </div>
        </div>

        <!-- Color Theme Options -->
        <div>
            <h4 class="mb-3 text-base font-medium">Color</h4>
            <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-6">
                <div
                    v-for="theme in colorThemes"
                    :key="theme.value"
                    @click="updateColorTheme(theme.value)"
                    :class="[
                        'flex cursor-pointer flex-col items-center justify-center rounded-lg border p-3 transition-colors',
                        colorTheme === theme.value
                            ? 'border-primary-500 bg-primary-50 dark:border-primary-400 dark:bg-primary-900/20'
                            : 'hover:border-neutral-300 hover:bg-neutral-50 dark:hover:border-neutral-600 dark:hover:bg-neutral-800/50'
                    ]"
                >
                    <div class="mb-2 flex h-8 w-8 items-center justify-center rounded-full" :style="{ backgroundColor: theme.color }">
                        <component :is="theme.icon" class="h-4 w-4 text-white" />
                    </div>
                    <span class="text-sm font-medium">{{ theme.label }}</span>
                </div>
            </div>
        </div>
    </div>
</template>

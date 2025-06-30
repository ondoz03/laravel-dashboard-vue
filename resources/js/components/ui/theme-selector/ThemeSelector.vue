<script setup lang="ts">
import { ref, watch, onMounted } from 'vue';
import { Palette } from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { router } from '@inertiajs/vue3';

interface Theme {
  name: string;
  value: string;
  color: string;
}

const themes: Theme[] = [
  { name: 'Default', value: 'light', color: '#ffffff' },
  { name: 'Dark', value: 'dark', color: '#1e293b' },
  { name: 'Blue', value: 'blue', color: '#3b82f6' },
  { name: 'Green', value: 'green', color: '#10b981' },
  { name: 'Purple', value: 'purple', color: '#8b5cf6' },
  { name: 'Rose', value: 'rose', color: '#f43f5e' },
];

const currentTheme = ref<string>('light');

onMounted(() => {
  // Load saved theme preference if available
  loadThemePreference();

  // Apply the theme
  applyTheme(currentTheme.value);
});

function setTheme(theme: string) {
  currentTheme.value = theme;
  applyTheme(theme);
  saveThemePreference(theme);
}

function applyTheme(theme: string) {
  // Remove all theme classes
  document.documentElement.classList.remove('light', 'dark', 'blue', 'green', 'purple', 'rose');

  // Add the selected theme class
  document.documentElement.classList.add(theme);

  // Store the theme in localStorage for persistence across page loads
  localStorage.setItem('theme', theme);
}

</script>

<template>
  <DropdownMenu>
    <DropdownMenuTrigger asChild>
      <Button variant="outline" size="icon">
        <Palette class="h-4 w-4" />
        <span class="sr-only">Toggle theme</span>
      </Button>
    </DropdownMenuTrigger>
    <DropdownMenuContent align="end">
      <DropdownMenuItem v-for="theme in themes" :key="theme.value" @click="setTheme(theme.value)">
        <div class="flex items-center gap-2">
          <div class="h-4 w-4 rounded-full border" :style="{ backgroundColor: theme.color }"></div>
          <span>{{ theme.name }}</span>
          <svg v-if="currentTheme === theme.value" class="ml-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
          </svg>
        </div>
      </DropdownMenuItem>
    </DropdownMenuContent>
  </DropdownMenu>
</template>

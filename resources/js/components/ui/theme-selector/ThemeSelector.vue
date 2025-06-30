<script setup lang="ts">
import { Palette, Sun, Moon, Monitor } from 'lucide-vue-next';
import { Button } from '../../../components/ui/button';
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuTrigger,
} from '../../../components/ui/dropdown-menu';
import { useAppearance } from '../../../composables/useAppearance';

// Use the shared appearance composable
const { appearance, updateAppearance, colorTheme, updateColorTheme } = useAppearance();

// Define appearance themes
const appearanceThemes = [
  { name: 'Light', value: 'light', icon: Sun, color: '#ffffff' },
  { name: 'Dark', value: 'dark', icon: Moon, color: '#1e293b' },
  { name: 'System', value: 'system', icon: Monitor, color: '#64748b' },
];

// Define color themes
const colorThemes = [
  { name: 'Default', value: 'default', icon: Palette, color: '#3b82f6' },
  { name: 'Blue', value: 'blue', icon: Palette, color: '#2563eb' },
  { name: 'Green', value: 'green', icon: Palette, color: '#16a34a' },
  { name: 'Purple', value: 'purple', icon: Palette, color: '#9333ea' },
  { name: 'Red', value: 'red', icon: Palette, color: '#dc2626' },
  { name: 'Orange', value: 'orange', icon: Palette, color: '#ea580c' },
];
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
      <!-- Appearance Themes -->
      <div class="p-2">
        <h3 class="mb-2 text-xs font-medium text-muted-foreground">Mode</h3>
        <div class="grid grid-cols-3 gap-2">
          <DropdownMenuItem v-for="theme in appearanceThemes" :key="theme.value" @click="updateAppearance(theme.value)" class="p-1">
            <div class="flex flex-col items-center justify-center gap-1 p-1">
              <component :is="theme.icon" class="h-4 w-4" />
              <span class="text-xs">{{ theme.name }}</span>
              <svg v-if="appearance === theme.value" class="absolute top-1 right-1 h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
              </svg>
            </div>
          </DropdownMenuItem>
        </div>
      </div>

      <div class="px-2 pt-2 pb-0">
        <div class="h-px bg-muted"></div>
      </div>

      <!-- Color Themes -->
      <div class="p-2">
        <h3 class="mb-2 text-xs font-medium text-muted-foreground">Color</h3>
        <div class="grid grid-cols-3 gap-2">
          <DropdownMenuItem v-for="theme in colorThemes" :key="theme.value" @click="updateColorTheme(theme.value)" class="p-1">
            <div class="flex flex-col items-center justify-center gap-1 p-1">
              <div class="flex h-4 w-4 items-center justify-center rounded-full" :style="{ backgroundColor: theme.color }">
                <svg v-if="colorTheme === theme.value" class="h-3 w-3 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
              </div>
              <span class="text-xs">{{ theme.name }}</span>
            </div>
          </DropdownMenuItem>
        </div>
      </div>
    </DropdownMenuContent>
  </DropdownMenu>
</template>

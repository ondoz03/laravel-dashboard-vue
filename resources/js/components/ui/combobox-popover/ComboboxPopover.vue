<script setup lang="ts">
import { ref, computed } from 'vue';

import { Button } from "../../ui/button"
import { Input } from "../../ui/input"
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuTrigger,
} from "../../ui/dropdown-menu"
import { Check, Search, X } from 'lucide-vue-next';

type Option = {
  value: string
  label: string
  count?: number
}

const props = defineProps<{
  placeholder?: string;
  label?: string;
  options: Option[];
}>();

const emit = defineEmits<{
  (e: 'update:selected', value: Option[]): void;
}>();

const open = ref(false);
const selectedStatuses = ref<Option[]>([]);
const searchQuery = ref('');

// Allow setting initial selected values and toggling the dropdown
defineExpose({
  setSelected(values: string[]) {
    selectedStatuses.value = props.options.filter(option =>
      values.includes(option.value)
    );
  },
  toggle() {
    open.value = !open.value;
  }
});

const toggleStatus = (option: Option) => {
  const index = selectedStatuses.value.findIndex(s => s.value === option.value);
  if (index === -1) {
    selectedStatuses.value.push(option);
  } else {
    selectedStatuses.value.splice(index, 1);
  }
  emit('update:selected', selectedStatuses.value);
};

const isSelected = (option: Option) => {
  return selectedStatuses.value.some(s => s.value === option.value);
};

const filteredOptions = computed(() => {
  if (!searchQuery.value) return props.options;

  return props.options.filter(option =>
    option.label.toLowerCase().includes(searchQuery.value.toLowerCase())
  );
});

const clearSearch = () => {
  searchQuery.value = '';
};

const clearAllSelections = () => {
  selectedStatuses.value = [];
  emit('update:selected', selectedStatuses.value);
  open.value = false;
};

const buttonText = computed(() => {
  if (selectedStatuses.value.length === 0) {
    return `+ ${props.placeholder || 'Set status'}`;
  } else if (selectedStatuses.value.length <= 2) {
    return selectedStatuses.value.map(s => s.label).join(', ');
  } else {
    return `${selectedStatuses.value.length} selected`;
  }
});
</script>

<template>
  <div class="flex items-center space-x-4">
    <p v-if="label" class="text-muted-foreground text-sm">{{ label }}</p>
    <DropdownMenu v-model:open="open">
      <DropdownMenuTrigger asChild>
        <Button variant="outline" class="w-[150px] justify-start">
          {{ buttonText }}
        </Button>
      </DropdownMenuTrigger>
      <DropdownMenuContent class="p-0 w-[200px]" side="top" align="start">
        <div class="bg-popover text-popover-foreground flex h-full w-full flex-col overflow-hidden rounded-md">
          <div class="flex h-9 items-center gap-2 border-b px-3">
            <Search class="size-4 shrink-0 opacity-50" />
            <Input
              v-model="searchQuery"
              :placeholder="placeholder || 'Status'"
              class="flex h-10 w-full rounded-md bg-transparent py-3 text-sm outline-hidden disabled:cursor-not-allowed disabled:opacity-50"
            />
          </div>
          <div class="max-h-[300px] scroll-py-1 overflow-x-hidden overflow-y-auto">
            <div class="text-foreground overflow-hidden p-1">
              <div role="group">
                <div
                  v-for="option in filteredOptions"
                  :key="option.value"
                  @click="toggleStatus(option)"
                  class="data-[selected=true]:bg-accent data-[selected=true]:text-accent-foreground [&_svg:not([class*='text-'])]:text-muted-foreground relative flex cursor-default items-center gap-2 rounded-sm px-2 py-1.5 text-sm outline-hidden select-none data-[disabled=true]:pointer-events-none data-[disabled=true]:opacity-50 [&_svg]:pointer-events-none [&_svg]:shrink-0 [&_svg:not([class*='size-'])]:size-4"
                  :data-selected="isSelected(option)"
                >
                  <div :class="['flex size-4 items-center justify-center rounded-[4px] border', isSelected(option) ? 'bg-primary border-primary text-primary-foreground' : 'border-input [&_svg]:invisible']">
                    <Check class="text-primary-foreground size-3.5" />
                  </div>
                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-muted-foreground size-4">
                    <circle cx="12" cy="12" r="10"></circle>
                    <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path>
                    <path d="M12 17h.01"></path>
                  </svg>
                  <span>{{ option.label }}</span>
                  <span class="text-muted-foreground ml-auto flex size-4 items-center justify-center font-mono text-xs">{{ option.count || '' }}</span>
                </div>
                <div v-if="filteredOptions.length === 0" class="py-6 text-center text-sm text-muted-foreground">
                  No results found.
                </div>
              </div>
            </div>
            <div class="bg-border -mx-1 h-px" role="separator"></div>
            <div class="text-foreground overflow-hidden p-1">
              <div role="group">
                <div
                  @click="clearAllSelections"
                  class="data-[selected=true]:bg-accent data-[selected=true]:text-accent-foreground [&_svg:not([class*='text-'])]:text-muted-foreground relative flex cursor-default items-center gap-2 rounded-sm px-2 py-1.5 text-sm outline-hidden select-none data-[disabled=true]:pointer-events-none data-[disabled=true]:opacity-50 [&_svg]:pointer-events-none [&_svg]:shrink-0 [&_svg:not([class*='size-'])]:size-4 justify-center text-center"
                >
                  Clear filters
                </div>
              </div>
            </div>
          </div>
        </div>
      </DropdownMenuContent>
    </DropdownMenu>
  </div>
</template>

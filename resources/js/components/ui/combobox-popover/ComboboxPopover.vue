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
import { Check, Search, X, ChevronsUpDown } from 'lucide-vue-next';

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
  (e: 'update:selected', value: string | Option[]): void;
}>();

const open = ref(false);
const value = ref('');
const searchQuery = ref('');

// Allow setting initial value and toggling the dropdown
defineExpose({
  setSelected(selectedValue: string | string[]) {
    // Handle both string and array for backward compatibility
    if (Array.isArray(selectedValue) && selectedValue.length > 0) {
      value.value = selectedValue[0];
    } else if (typeof selectedValue === 'string') {
      value.value = selectedValue;
    }
  },
  toggle() {
    open.value = !open.value;
  }
});

const onSelect = (currentValue: string) => {
  value.value = currentValue === value.value ? '' : currentValue;

  // For backward compatibility, emit both formats
  if (value.value) {
    const selectedOption = props.options.find(option => option.value === value.value);
    if (selectedOption) {
      // Emit both the string value and an array with the selected option
      emit('update:selected', value.value);
      emit('update:selected', [selectedOption]);
    } else {
      emit('update:selected', value.value);
      emit('update:selected', []);
    }
  } else {
    emit('update:selected', '');
    emit('update:selected', []);
  }

  open.value = false;
};

const getSelectedLabel = computed(() => {
  if (!value.value) return '';
  const selectedOption = props.options.find(option => option.value === value.value);
  return selectedOption ? selectedOption.label : '';
});

const filteredOptions = computed(() => {
  if (!searchQuery.value) return props.options;

  return props.options.filter(option =>
    option.label.toLowerCase().includes(searchQuery.value.toLowerCase())
  );
});

const clearSearch = () => {
  searchQuery.value = '';
};

const buttonText = computed(() => {
  return getSelectedLabel.value || props.placeholder || 'Select...';
});
</script>

<template>
  <div class="flex items-center space-x-4 relative">
    <p v-if="label" class="text-muted-foreground text-sm">{{ label }}</p>
    <DropdownMenu v-model:open="open">
      <DropdownMenuTrigger asChild>
        <Button
          variant="outline"
          role="combobox"
          :aria-expanded="open"
          class="w-[200px] justify-between"
        >
          {{ buttonText }}
          <ChevronsUpDown class="opacity-50" />
        </Button>
      </DropdownMenuTrigger>
      <DropdownMenuContent class="p-0 w-[200px]" side="bottom" align="start" :sideOffset="5" position="popper">
        <div tabindex="-1" data-slot="command" class="bg-popover text-popover-foreground flex h-full w-full flex-col overflow-hidden rounded-md" cmdk-root="">
          <label cmdk-label="" for="radix-id" id="radix-label-id" style="position: absolute; width: 1px; height: 1px; padding: 0px; margin: -1px; overflow: hidden; clip: rect(0px, 0px, 0px, 0px); white-space: nowrap; border-width: 0px;"></label>
          <div data-slot="command-input-wrapper" class="flex h-9 items-center gap-2 border-b px-3">
            <Search class="size-4 shrink-0 opacity-50" />
            <Input
              v-model="searchQuery"
              :placeholder="`Search ${placeholder || ''}...`"
              class="placeholder:text-muted-foreground flex h-10 w-full rounded-md bg-transparent py-3 text-sm outline-hidden disabled:cursor-not-allowed disabled:opacity-50"
              data-slot="command-input"
              cmdk-input=""
              autocomplete="off"
              autocorrect="off"
              spellcheck="false"
              aria-autocomplete="list"
              role="combobox"
              aria-expanded="true"
              type="text"
            />
          </div>
          <div data-slot="command-list" class="max-h-[300px] scroll-py-1 overflow-x-hidden overflow-y-auto" cmdk-list="" role="listbox" tabindex="-1" aria-label="Suggestions" style="--cmdk-list-height: 209.0px;">
            <div cmdk-list-sizer="">
              <div v-if="filteredOptions.length === 0" class="py-6 text-center text-sm text-muted-foreground">
                No results found.
              </div>
              <div data-slot="command-group" class="text-foreground [&_[cmdk-group-heading]]:text-muted-foreground overflow-hidden p-1 [&_[cmdk-group-heading]]:px-2 [&_[cmdk-group-heading]]:py-1.5 [&_[cmdk-group-heading]]:text-xs [&_[cmdk-group-heading]]:font-medium" cmdk-group="" role="presentation">
                <div cmdk-group-items="" role="group">
                  <div
                    v-for="option in filteredOptions"
                    :key="option.value"
                    @click="onSelect(option.value)"
                    data-slot="command-item"
                    class="data-[selected=true]:bg-accent data-[selected=true]:text-accent-foreground [&_svg:not([class*='text-'])]:text-muted-foreground relative flex cursor-default items-center gap-2 rounded-sm px-2 py-1.5 text-sm outline-hidden select-none data-[disabled=true]:pointer-events-none data-[disabled=true]:opacity-50 [&_svg]:pointer-events-none [&_svg]:shrink-0 [&_svg:not([class*='size-'])]:size-4"
                    cmdk-item=""
                    role="option"
                    aria-disabled="false"
                    :aria-selected="(option.value === value).toString()"
                    :data-disabled="false"
                    :data-selected="option.value === value"
                  >
                    {{ option.label }}
                    <Check
                      class="ml-auto"
                      :class="option.value === value ? 'opacity-100' : 'opacity-0'"
                    />
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </DropdownMenuContent>
    </DropdownMenu>
  </div>
</template>

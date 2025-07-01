<template>
  <div class="flex flex-col md:flex-row gap-4">
    <div class="relative flex-grow">
      <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
        <Search class="h-4 w-4 text-muted-foreground" />
      </div>
      <Input
        :value="modelValue"
        @input="$emit('update:modelValue', $event.target.value)"
        type="search"
        :placeholder="`Search ${page || 'items'}...`"
        class="pl-10"
      />
    </div>
    <div class="flex gap-2">
      <Button v-if="modelValue" variant="ghost" @click="$emit('reset')">
        <X class="mr-2 h-4 w-4" />
        Clear
      </Button>
      <slot></slot>
    </div>
  </div>
</template>

<script setup lang="ts">
import { Search, X } from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';

interface Props {
  modelValue: string;
  page?: string;
}

defineProps<Props>();
defineEmits(['update:modelValue', 'reset']);
</script>

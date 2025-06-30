<script setup lang="ts">
import { MoreHorizontal } from 'lucide-vue-next';
import { cn } from '@/lib/utils';

interface Props {
  class?: string;
}

const props = defineProps<Props>();
</script>

<template>
  <div
    aria-hidden="true"
    :class="cn('flex h-8 w-8 items-center justify-center', props.class)"
  >
    <MoreHorizontal class="h-4 w-4" />
    <span class="sr-only">More pages</span>
  </div>
</template>

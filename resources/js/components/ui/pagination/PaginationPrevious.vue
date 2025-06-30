<script setup lang="ts">
import { ChevronLeft } from 'lucide-vue-next';
import { cn } from '@/lib/utils';
import { PaginationLink } from '.';

interface Props {
  class?: string;
}

const props = defineProps<Props>();
</script>

<template>
  <PaginationLink
    aria-label="Go to previous page"
    :class="cn('gap-1 pl-2.5', props.class)"
  >
    <ChevronLeft class="h-4 w-4" />
    <span>Previous</span>
  </PaginationLink>
</template>

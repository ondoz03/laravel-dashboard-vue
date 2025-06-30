<script setup lang="ts">
import { ChevronRight } from 'lucide-vue-next';
import { cn } from '@/lib/utils';
import { PaginationLink } from '.';

interface Props {
  class?: string;
}

const props = defineProps<Props>();
</script>

<template>
  <PaginationLink
    aria-label="Go to next page"
    :class="cn('gap-1 pr-2.5', props.class)"
  >
    <span>Next</span>
    <ChevronRight class="h-4 w-4" />
  </PaginationLink>
</template>

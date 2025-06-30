<script setup lang="ts">
import { cn } from '@/lib/utils';
import { Button } from '@/components/ui/button';

interface Props {
  isActive?: boolean;
  class?: string;
}

const props = withDefaults(defineProps<Props>(), {
  isActive: false,
});
</script>

<template>
  <Button
    :variant="props.isActive ? 'default' : 'outline'"
    size="icon"
    class="h-8 w-8"
    :class="cn('', props.class)"
    :aria-current="props.isActive ? 'page' : undefined"
  >
    <slot />
  </Button>
</template>

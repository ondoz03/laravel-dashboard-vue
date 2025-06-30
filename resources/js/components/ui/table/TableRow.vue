<script setup lang="ts">
defineProps<{
  class?: string;
}>();
</script>

<template>
  <tr class="border-b transition-colors hover:bg-muted/50 data-[state=selected]:bg-muted even:bg-muted/5">
    <slot />
  </tr>
</template>

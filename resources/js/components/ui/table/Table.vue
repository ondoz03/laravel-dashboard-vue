<script setup lang="ts">
defineProps<{
  class?: string;
}>();
</script>

<template>
  <div class="w-full overflow-auto">
    <table class="w-full caption-bottom text-sm border-collapse">
      <slot />
    </table>
  </div>
</template>

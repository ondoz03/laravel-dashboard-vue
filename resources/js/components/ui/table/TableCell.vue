<script setup lang="ts">
defineProps<{
  class?: string;
}>();
</script>

<template>
  <td class="p-2 align-middle text-center [&:has([role=checkbox])]:pr-0">
    <slot />
  </td>
</template>

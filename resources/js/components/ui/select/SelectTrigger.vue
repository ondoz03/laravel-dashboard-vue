<script setup lang="ts">
import { SelectTrigger, useForwardProps, type SelectTriggerProps } from 'radix-vue';

const props = defineProps<SelectTriggerProps>();

const forwardedProps = useForwardProps(props);
</script>

<template>
    <SelectTrigger class="flex h-10 w-full items-center justify-between rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50" v-bind="forwardedProps">
        <slot />
    </SelectTrigger>
</template>

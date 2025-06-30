<script setup lang="ts">
import { SelectValue, useForwardProps, type SelectValueProps } from 'radix-vue';

const props = defineProps<SelectValueProps>();

const forwardedProps = useForwardProps(props);
</script>

<template>
    <SelectValue class="text-sm" v-bind="forwardedProps">
        <slot />
    </SelectValue>
</template>

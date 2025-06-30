<script setup lang="ts">
import { DropdownMenuTrigger, useForwardProps, type DropdownMenuTriggerProps } from 'radix-vue';
import { ref, onMounted, onBeforeUnmount } from 'vue';

const props = defineProps<DropdownMenuTriggerProps>();
const forwardedProps = useForwardProps(props);
const mounted = ref(false);

onMounted(() => {
    mounted.value = true;
});

onBeforeUnmount(() => {
    mounted.value = false;
});
</script>

<template>
    <DropdownMenuTrigger v-if="mounted" class="outline-none" v-bind="forwardedProps">
        <slot />
    </DropdownMenuTrigger>
</template>

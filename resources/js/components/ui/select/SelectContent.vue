<script setup lang="ts">
import { SelectContent, SelectPortal, SelectViewport, useForwardProps, type SelectContentProps } from 'radix-vue';
import { ref, onMounted, onBeforeUnmount } from 'vue';

const props = defineProps<SelectContentProps>();
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
    <SelectPortal v-if="mounted">
        <SelectContent class="relative z-50 min-w-[8rem] overflow-hidden rounded-md border bg-popover text-popover-foreground shadow-md data-[state=open]:animate-in data-[state=closed]:animate-out data-[state=closed]:fade-out-0 data-[state=open]:fade-in-0 data-[state=closed]:zoom-out-95 data-[state=open]:zoom-in-95 data-[side=bottom]:slide-in-from-top-2 data-[side=left]:slide-in-from-right-2 data-[side=right]:slide-in-from-left-2 data-[side=top]:slide-in-from-bottom-2" position="popper" v-bind="forwardedProps">
            <SelectViewport class="p-1">
                <slot />
            </SelectViewport>
        </SelectContent>
    </SelectPortal>
</template>

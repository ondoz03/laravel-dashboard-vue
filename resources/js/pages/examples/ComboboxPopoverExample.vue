<script setup lang="ts">
import { ref } from 'vue';
import { Head } from '@inertiajs/vue3';
import AppLayout from '../../layouts/AppLayout.vue';
import { type BreadcrumbItem } from '../../types';
import { ComboboxPopover } from '../../components/ui/combobox-popover';

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Examples',
    href: '/examples',
  },
  {
    title: 'ComboboxPopover',
    href: '/examples/combobox-popover',
  },
];

type Status = {
  value: string
  label: string
}

const selectedStatuses = ref<Status[]>([]);
</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <Head title="ComboboxPopover Example" />

    <div class="flex flex-col space-y-6 px-4 py-6">
      <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold">ComboboxPopover Example</h1>
      </div>

      <div class="rounded-lg border p-6 space-y-6">
        <div class="space-y-2">
          <h2 class="text-lg font-medium">Basic Example</h2>
          <ComboboxPopover
            label="Status"
            placeholder="Set status"
            v-model:selected="selectedStatuses"
          />
        </div>

        <div class="space-y-2">
          <h2 class="text-lg font-medium">Selected Values</h2>
          <pre class="rounded-md bg-muted p-4">{{ selectedStatuses }}</pre>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

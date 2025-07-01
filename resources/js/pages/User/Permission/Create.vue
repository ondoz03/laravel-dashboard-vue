<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <Head title="Create Permission" />

    <div class="flex flex-col space-y-6 px-4 py-6">
      <HeadingSmall title="Create Permission" description="Add a new permission to the system" />

      <div class="rounded-lg border bg-card shadow-sm">
        <form @submit.prevent="submit" class="p-6">
          <div class="grid gap-4">
            <div class="grid gap-2">
              <Label for="name">Name</Label>
              <Input
                id="name"
                v-model="form.name"
                type="text"
                placeholder="Enter permission name"
                :disabled="form.processing"
                required
              />
              <InputError :message="form.errors.name" />
            </div>
          </div>

          <div class="flex justify-end mt-6 space-x-2">
            <Button type="button" variant="outline" @click="cancel" :disabled="form.processing">
              Cancel
            </Button>
            <Button type="submit" :disabled="form.processing">
              <Loader2 v-if="form.processing" class="mr-2 h-4 w-4 animate-spin" />
              Create Permission
            </Button>
          </div>
        </form>
      </div>
    </div>
  </AppLayout>
</template>

<script setup lang="ts">
import { Head, useForm, router } from '@inertiajs/vue3';
import { Loader2 } from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import InputError from '@/components/InputError.vue';
import HeadingSmall from '@/components/HeadingSmall.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Permissions',
    href: '/permissions',
  },
  {
    title: 'Create',
    href: '/permissions/create',
  },
];

const form = useForm({
  name: '',
});

function submit() {
  form.post(route('permissions.store'), {
    onSuccess: () => {
      form.reset();
    },
  });
}

function cancel() {
  router.get('/permissions');
}
</script>

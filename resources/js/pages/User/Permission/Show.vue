<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <Head title="View Permission" />

    <div class="flex flex-col space-y-6 px-4 py-6">
      <HeadingSmall title="View Permission" :description="permission.name" />

      <div class="rounded-lg border bg-card shadow-sm">
        <div class="p-6 grid gap-4">
          <div>
            <h3 class="text-sm font-medium">ID</h3>
            <p class="mt-1">{{ permission?.id }}</p>
          </div>

          <div>
            <h3 class="text-sm font-medium">Name</h3>
            <p class="mt-1">{{ permission?.name }}</p>
          </div>

          <div>
            <h3 class="text-sm font-medium">Roles</h3>
            <div class="mt-1 flex flex-wrap gap-1">
              <Badge v-for="role in permission?.roles" :key="role.id" variant="outline">
                {{ role.name }}
              </Badge>
              <span v-if="!permission?.roles || permission?.roles.length === 0" class="text-muted-foreground">
                No roles
              </span>
            </div>
          </div>

          <div>
            <h3 class="text-sm font-medium">Created At</h3>
            <p class="mt-1">{{ permission?.created_at ? new Date(permission.created_at).toLocaleString() : 'N/A' }}</p>
          </div>

          <div>
            <h3 class="text-sm font-medium">Updated At</h3>
            <p class="mt-1">{{ permission?.updated_at ? new Date(permission.updated_at).toLocaleString() : 'N/A' }}</p>
          </div>
        </div>

        <div class="p-6 border-t flex justify-end space-x-2">
          <Button variant="outline" @click="goBack">Back</Button>
          <Button @click="editPermission">Edit</Button>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import HeadingSmall from '@/components/HeadingSmall.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';

interface Role {
  id: number;
  name: string;
}

interface Permission {
  id: number;
  name: string;
  roles: Role[];
  created_at: string;
  updated_at: string;
}

interface Props {
  permission: Permission;
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Permissions',
    href: '/permissions',
  },
  {
    title: 'View',
    href: `/permissions/${props.permission.id}`,
  },
];

function goBack() {
  router.get('/permissions');
}

function editPermission() {
  router.get(route('permissions.edit', props.permission.id));
}
</script>

<template>
  <Dialog :open="show" @update:open="$emit('close')">
    <DialogContent class="sm:max-w-md">
      <DialogHeader>
        <DialogTitle>View Role</DialogTitle>
        <DialogDescription v-if="role">
          {{ role.name }}
        </DialogDescription>
      </DialogHeader>

      <div class="grid gap-4 py-4">
        <div>
          <h3 class="text-sm font-medium">ID</h3>
          <p class="mt-1">{{ role?.id }}</p>
        </div>

        <div>
          <h3 class="text-sm font-medium">Name</h3>
          <p class="mt-1">{{ role?.name }}</p>
        </div>

        <div>
          <h3 class="text-sm font-medium">Permissions</h3>
          <div class="mt-1 flex flex-wrap gap-1">
            <Badge v-for="permission in role?.permissions" :key="permission.id" variant="outline">
              {{ permission.name }}
            </Badge>
            <span v-if="!role?.permissions || role?.permissions.length === 0" class="text-muted-foreground">
              No permissions
            </span>
          </div>
        </div>

        <div>
          <h3 class="text-sm font-medium">Created At</h3>
          <p class="mt-1">{{ role?.created_at ? new Date(role.created_at).toLocaleString() : 'N/A' }}</p>
        </div>

        <div>
          <h3 class="text-sm font-medium">Updated At</h3>
          <p class="mt-1">{{ role?.updated_at ? new Date(role.updated_at).toLocaleString() : 'N/A' }}</p>
        </div>
      </div>

      <DialogFooter>
        <Button @click="$emit('close')">Close</Button>
      </DialogFooter>
    </DialogContent>
  </Dialog>
</template>

<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogFooter,
  DialogHeader,
  DialogTitle,
} from '@/components/ui/dialog';

interface Permission {
  id: number;
  name: string;
}

interface Role {
  id: number;
  name: string;
  permissions: Permission[];
  created_at: string;
  updated_at: string;
}

interface Props {
  show: boolean;
  role: Role;
}

defineProps<Props>();
defineEmits(['close']);
</script>

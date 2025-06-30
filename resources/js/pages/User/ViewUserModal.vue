<script setup lang="ts">
import { Button } from '@/components/ui/button';
import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogFooter,
  DialogHeader,
  DialogTitle,
} from '@/components/ui/dialog';

interface User {
  id: number;
  name: string;
  email: string;
  email_verified_at: string | null;
  created_at: string;
  updated_at: string;
}

interface Props {
  show: boolean;
  user: User;
}

const props = defineProps<Props>();
const emit = defineEmits(['close']);

function closeModal() {
  emit('close');
}
</script>

<template>
  <Dialog :open="show" @update:open="closeModal">
    <DialogContent class="sm:max-w-[700px]">
      <DialogHeader>
        <DialogTitle>View User</DialogTitle>
        <DialogDescription v-if="user">
          {{ user.name }} ({{ user.email }})
        </DialogDescription>
      </DialogHeader>

      <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
        <div>
          <h3 class="text-sm font-medium text-muted-foreground">ID</h3>
          <p class="mt-1">{{ user?.id }}</p>
        </div>

        <div>
          <h3 class="text-sm font-medium text-muted-foreground">Name</h3>
          <p class="mt-1">{{ user?.name }}</p>
        </div>

        <div>
          <h3 class="text-sm font-medium text-muted-foreground">Email</h3>
          <p class="mt-1">{{ user?.email }}</p>
        </div>

        <div>
          <h3 class="text-sm font-medium text-muted-foreground">Email Verified</h3>
          <p class="mt-1">{{ user?.email_verified_at ? new Date(user.email_verified_at).toLocaleString() : 'Not verified' }}</p>
        </div>

        <div>
          <h3 class="text-sm font-medium text-muted-foreground">Created At</h3>
          <p class="mt-1">{{ user?.created_at ? new Date(user.created_at).toLocaleString() : 'N/A' }}</p>
        </div>

        <div>
          <h3 class="text-sm font-medium text-muted-foreground">Updated At</h3>
          <p class="mt-1">{{ user?.updated_at ? new Date(user.updated_at).toLocaleString() : 'N/A' }}</p>
        </div>
      </div>

      <DialogFooter>
        <Button type="button" variant="outline" @click="closeModal">
          Close
        </Button>
      </DialogFooter>
    </DialogContent>
  </Dialog>
</template>

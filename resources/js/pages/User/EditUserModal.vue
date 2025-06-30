<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';

import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogFooter,
  DialogHeader,
  DialogTitle,
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';

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

const form = useForm({
  name: props.user?.name || '',
  email: props.user?.email || '',
  password: '',
  password_confirmation: '',
});

function submit() {
  form.patch(route('users.update', props.user.id), {
    preserveScroll: true,
    onSuccess: () => {
      emit('close');
    },
  });
}

function closeModal() {
  emit('close');
}
</script>

<template>
  <Dialog :open="show" @update:open="closeModal">
    <DialogContent class="sm:max-w-[700px]">
      <DialogHeader>
        <DialogTitle>Edit User</DialogTitle>
        <DialogDescription v-if="user">
          Editing: {{ user.name }} ({{ user.email }})
        </DialogDescription>
      </DialogHeader>

      <form @submit.prevent="submit" class="space-y-6">
        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
          <div class="grid gap-2">
            <Label for="name">Name *</Label>
            <Input
              id="name"
              v-model="form.name"
              required
              placeholder="Enter name"
            />
            <InputError :message="form.errors.name" />
          </div>

          <div class="grid gap-2">
            <Label for="email">Email *</Label>
            <Input
              id="email"
              type="email"
              v-model="form.email"
              required
              placeholder="Enter email"
            />
            <InputError :message="form.errors.email" />
          </div>

          <div class="grid gap-2">
            <Label for="password">Password (leave blank to keep current)</Label>
            <Input
              id="password"
              type="password"
              v-model="form.password"
              placeholder="Enter new password"
            />
            <InputError :message="form.errors.password" />
          </div>

          <div class="grid gap-2">
            <Label for="password_confirmation">Confirm Password</Label>
            <Input
              id="password_confirmation"
              type="password"
              v-model="form.password_confirmation"
              placeholder="Confirm new password"
            />
            <InputError :message="form.errors.password_confirmation" />
          </div>
        </div>

        <DialogFooter>
          <Button type="button" variant="outline" @click="closeModal">
            Cancel
          </Button>
          <Button type="submit" :disabled="form.processing">Save Changes</Button>
        </DialogFooter>
      </form>
    </DialogContent>
  </Dialog>
</template>

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

interface Props {
  show: boolean;
}

const props = defineProps<Props>();
const emit = defineEmits(['close']);

const form = useForm({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
});

function submit() {
  form.post(route('users.store'), {
    preserveScroll: true,
    onSuccess: () => {
      emit('close');
      form.reset();
    },
  });
}

function closeModal() {
  emit('close');
  form.reset();
}
</script>

<template>
  <Dialog :open="show" @update:open="closeModal">
    <DialogContent class="sm:max-w-[700px]">
      <DialogHeader>
        <DialogTitle>Add New User</DialogTitle>
        <DialogDescription>
          Create a new user by filling out the form below.
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
            <Label for="password">Password *</Label>
            <Input
              id="password"
              type="password"
              v-model="form.password"
              required
              placeholder="Enter password"
            />
            <InputError :message="form.errors.password" />
          </div>

          <div class="grid gap-2">
            <Label for="password_confirmation">Confirm Password *</Label>
            <Input
              id="password_confirmation"
              type="password"
              v-model="form.password_confirmation"
              required
              placeholder="Confirm password"
            />
            <InputError :message="form.errors.password_confirmation" />
          </div>
        </div>

        <DialogFooter>
          <Button type="button" variant="outline" @click="closeModal">
            Cancel
          </Button>
          <Button type="submit" :disabled="form.processing">Save</Button>
        </DialogFooter>
      </form>
    </DialogContent>
  </Dialog>
</template>

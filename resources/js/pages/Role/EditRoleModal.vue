<template>
  <Dialog :open="show" @update:open="$emit('close')">
    <DialogContent class="sm:max-w-md">
      <DialogHeader>
        <DialogTitle>Edit Role</DialogTitle>
        <DialogDescription v-if="role">
          Editing: {{ role.name }}
        </DialogDescription>
      </DialogHeader>

      <form @submit.prevent="submit">
        <div class="grid gap-4 py-4">
          <div class="grid gap-2">
            <Label for="name">Name</Label>
            <Input
              id="name"
              v-model="form.name"
              type="text"
              placeholder="Enter role name"
              :disabled="form.processing"
              required
            />
            <InputError :message="form.errors.name" />
          </div>

          <div class="grid gap-2">
            <Label>Permissions</Label>
            <div class="border rounded-md p-4 max-h-60 overflow-y-auto">
              <div v-if="permissions.length === 0" class="text-muted-foreground">
                No permissions available
              </div>
              <div v-else class="grid grid-cols-1 md:grid-cols-2 gap-2">
                <div v-for="permission in permissions" :key="permission.id" class="flex items-center space-x-2">
                  <Checkbox
                    :id="`permission-${permission.id}`"
                    v-model:checked="form.permissions"
                    :value="permission.id"
                  />
                  <Label :for="`permission-${permission.id}`" class="cursor-pointer">
                    {{ permission.name }}
                  </Label>
                </div>
              </div>
            </div>
            <InputError :message="form.errors.permissions" />
          </div>
        </div>

        <DialogFooter>
          <Button type="button" variant="outline" @click="$emit('close')" :disabled="form.processing">
            Cancel
          </Button>
          <Button type="submit" :disabled="form.processing">
            <Loader2 v-if="form.processing" class="mr-2 h-4 w-4 animate-spin" />
            Update Role
          </Button>
        </DialogFooter>
      </form>
    </DialogContent>
  </Dialog>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { Loader2 } from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
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
import InputError from '@/components/InputError.vue';

interface Permission {
  id: number;
  name: string;
}

interface Role {
  id: number;
  name: string;
  permissions: Permission[];
}

interface Props {
  show: boolean;
  role: Role;
  permissions?: Permission[];
}

const props = defineProps<Props>();
const emit = defineEmits(['close']);

const form = useForm({
  name: props.role?.name || '',
  permissions: [] as number[],
});

const permissions = ref<Permission[]>(props.permissions || []);

onMounted(() => {
  // Initialize selected permissions
  if (props.role?.permissions) {
    form.permissions = props.role.permissions.map(p => p.id);
  }
});

function submit() {
  form.patch(route('roles.update', props.role.id), {
    onSuccess: () => {
      emit('close');
    },
  });
}
</script>

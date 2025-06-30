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
  item_code: '',
  item_name: '',
  category_item_id: null,
  aol_id: '',
  item_category: '',
  buyer: '',
  ppn: 0,
  pph: 0,
});

function submit() {
  form.post(route('master-items.store'), {
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
        <DialogTitle>Add New Master Item</DialogTitle>
        <DialogDescription>
          Create a new master item by filling out the form below.
        </DialogDescription>
      </DialogHeader>

      <form @submit.prevent="submit" class="space-y-6">
        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
          <div class="grid gap-2">
            <Label for="item_code">Item Code *</Label>
            <Input
              id="item_code"
              v-model="form.item_code"
              required
              placeholder="Enter item code"
            />
            <InputError :message="form.errors.item_code" />
          </div>

          <div class="grid gap-2">
            <Label for="item_name">Item Name *</Label>
            <Input
              id="item_name"
              v-model="form.item_name"
              required
              placeholder="Enter item name"
            />
            <InputError :message="form.errors.item_name" />
          </div>

          <div class="grid gap-2">
            <Label for="category_item_id">Category Item ID</Label>
            <Input
              id="category_item_id"
              type="number"
              v-model="form.category_item_id"
              placeholder="Enter category item ID"
            />
            <InputError :message="form.errors.category_item_id" />
          </div>

          <div class="grid gap-2">
            <Label for="aol_id">AOL ID</Label>
            <Input
              id="aol_id"
              v-model="form.aol_id"
              placeholder="Enter AOL ID"
            />
            <InputError :message="form.errors.aol_id" />
          </div>

          <div class="grid gap-2">
            <Label for="item_category">Item Category</Label>
            <Input
              id="item_category"
              v-model="form.item_category"
              placeholder="Enter item category"
            />
            <InputError :message="form.errors.item_category" />
          </div>

          <div class="grid gap-2">
            <Label for="buyer">Buyer</Label>
            <Input
              id="buyer"
              v-model="form.buyer"
              placeholder="Enter buyer"
            />
            <InputError :message="form.errors.buyer" />
          </div>

          <div class="grid gap-2">
            <Label for="ppn">PPN (%)</Label>
            <Input
              id="ppn"
              type="number"
              step="0.01"
              min="0"
              max="100"
              v-model="form.ppn"
              placeholder="Enter PPN percentage"
            />
            <InputError :message="form.errors.ppn" />
          </div>

          <div class="grid gap-2">
            <Label for="pph">PPH (%)</Label>
            <Input
              id="pph"
              type="number"
              step="0.01"
              min="0"
              max="100"
              v-model="form.pph"
              placeholder="Enter PPH percentage"
            />
            <InputError :message="form.errors.pph" />
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

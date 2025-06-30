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

interface MasterItem {
  id: number;
  uuid: string;
  category_item_id: number | null;
  aol_id: string | null;
  item_code: string;
  item_name: string;
  item_category: string | null;
  buyer: string | null;
  ppn: number;
  pph: number;
  created_at: string;
  updated_at: string;
}

interface Props {
  show: boolean;
  masterItem: MasterItem;
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
        <DialogTitle>View Master Item</DialogTitle>
        <DialogDescription v-if="masterItem">
          {{ masterItem.item_code }} - {{ masterItem.item_name }}
        </DialogDescription>
      </DialogHeader>

      <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
        <div>
          <h3 class="text-sm font-medium text-muted-foreground">Item Code</h3>
          <p class="mt-1">{{ masterItem?.item_code }}</p>
        </div>

        <div>
          <h3 class="text-sm font-medium text-muted-foreground">Item Name</h3>
          <p class="mt-1">{{ masterItem?.item_name }}</p>
        </div>

        <div>
          <h3 class="text-sm font-medium text-muted-foreground">Category Item ID</h3>
          <p class="mt-1">{{ masterItem?.category_item_id || 'N/A' }}</p>
        </div>

        <div>
          <h3 class="text-sm font-medium text-muted-foreground">AOL ID</h3>
          <p class="mt-1">{{ masterItem?.aol_id || 'N/A' }}</p>
        </div>

        <div>
          <h3 class="text-sm font-medium text-muted-foreground">Item Category</h3>
          <p class="mt-1">{{ masterItem?.item_category || 'N/A' }}</p>
        </div>

        <div>
          <h3 class="text-sm font-medium text-muted-foreground">Buyer</h3>
          <p class="mt-1">{{ masterItem?.buyer || 'N/A' }}</p>
        </div>

        <div>
          <h3 class="text-sm font-medium text-muted-foreground">PPN</h3>
          <p class="mt-1">{{ masterItem?.ppn }}%</p>
        </div>

        <div>
          <h3 class="text-sm font-medium text-muted-foreground">PPH</h3>
          <p class="mt-1">{{ masterItem?.pph }}%</p>
        </div>

        <div>
          <h3 class="text-sm font-medium text-muted-foreground">UUID</h3>
          <p class="mt-1">{{ masterItem?.uuid }}</p>
        </div>

        <div>
          <h3 class="text-sm font-medium text-muted-foreground">Created At</h3>
          <p class="mt-1">{{ masterItem?.created_at ? new Date(masterItem.created_at).toLocaleString() : 'N/A' }}</p>
        </div>

        <div>
          <h3 class="text-sm font-medium text-muted-foreground">Updated At</h3>
          <p class="mt-1">{{ masterItem?.updated_at ? new Date(masterItem.updated_at).toLocaleString() : 'N/A' }}</p>
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

<script setup lang="ts">
import { ref, watch, onMounted } from 'vue';
import { Check, Columns, Settings } from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import {
  Dialog,
  DialogContent,
  DialogHeader,
  DialogTitle,
  DialogFooter,
} from '@/components/ui/dialog';
import { Checkbox } from '@/components/ui/checkbox';
import { Label } from '@/components/ui/label';
import { router } from '@inertiajs/vue3';

interface Column {
  key: string;
  label: string;
  visible: boolean;
}

interface Props {
  columns: Column[];
  page: string;
}

const props = defineProps<Props>();
const emit = defineEmits(['update:columns']);

const isOpen = ref(false);
const localColumns = ref<Column[]>([]);

// Initialize local columns from props
onMounted(() => {
  localColumns.value = JSON.parse(JSON.stringify(props.columns));

  // Load saved preferences if available
  loadPreferences();
});

// Watch for changes in props.columns
watch(() => props.columns, (newColumns) => {
  localColumns.value = JSON.parse(JSON.stringify(newColumns));
}, { deep: true });

function toggleColumn(key: string) {
  const column = localColumns.value.find(col => col.key === key);
  if (column) {
    column.visible = !column.visible;
  }
}

function applyChanges() {
  emit('update:columns', localColumns.value);
  savePreferences();
  isOpen.value = false;
}

function resetToDefault() {
  localColumns.value = props.columns.map(col => ({
    ...col,
    visible: true
  }));
}



</script>

<template>
  <div>
    <Button variant="outline" size="sm" @click="isOpen = true">
      <Columns class="mr-2 h-4 w-4" />
      Customize Columns
    </Button>

    <Dialog :open="isOpen" @update:open="isOpen = $event">
      <DialogContent class="sm:max-w-md">
        <DialogHeader>
          <DialogTitle>Customize Columns</DialogTitle>
        </DialogHeader>

        <div class="py-4">
          <div class="space-y-4">
            <div v-for="column in localColumns" :key="column.key" class="flex items-center space-x-2">
              <Checkbox
                :id="column.key"
                :checked="column.visible"
                @update:checked="toggleColumn(column.key)"
              />
              <Label :for="column.key" class="cursor-pointer">{{ column.label }}</Label>
            </div>
          </div>
        </div>

        <DialogFooter>
          <Button variant="outline" @click="resetToDefault">Reset to Default</Button>
          <Button @click="applyChanges">Apply Changes</Button>
        </DialogFooter>
      </DialogContent>
    </Dialog>
  </div>
</template>

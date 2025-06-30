<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch, computed } from 'vue';
import { debounce } from 'lodash';
import { Eye, Pencil, Trash2, ArrowUp, ArrowDown, MoreHorizontal, MoreVertical, Plus, Filter, Search as SearchIcon } from 'lucide-vue-next';

import HeadingSmall from '@/components/HeadingSmall.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from '@/components/ui/select';
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from '@/components/ui/table';
import { TablePagination } from '@/components/ui/table-pagination';
import { ColumnSelector } from '@/components/ui/column-selector';
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import CreateItemModal from './CreateItemModal.vue';
import EditItemModal from './EditItemModal.vue';
import ViewItemModal from './ViewItemModal.vue';

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
}

interface Props {
  masterItems: {
    data: MasterItem[];
    links: any[];
    meta: {
      current_page: number;
      from: number;
      last_page: number;
      links: any[];
      path: string;
      per_page: number;
      to: number;
      total: number;
    };
  };
  filters: {
    search: string;
    category: string;
    buyer: string;
  };
  sort?: {
    field: string;
    direction: 'asc' | 'desc';
  };
  categories: string[];
  buyers: string[];
}

const props = withDefaults(defineProps<Props>(), {
  masterItems: () => ({
    data: [],
    links: [],
    meta: {
      current_page: 0,
      from: 0,
      last_page: 0,
      links: [],
      path: '',
      per_page: 20,
      to: 0,
      total: 0
    }
  }),
  filters: () => ({
    search: '',
    category: 'all',
    buyer: 'all'
  }),
  sort: () => ({
    field: 'item_code',
    direction: 'asc'
  }),
  categories: () => [],
  buyers: () => []
});

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Master Items',
    href: '/master-items',
  },
];

const search = ref(props.filters.search);
const category = ref(props.filters.category);
const buyer = ref(props.filters.buyer);

// Sorting state
const sortField = ref(props.sort?.field || 'item_code');
const sortDirection = ref(props.sort?.direction || 'asc');


// Modal state
const showCreateModal = ref(false);
const showEditModal = ref(false);
const showViewModal = ref(false);
const selectedItem = ref<MasterItem | null>(null);

// Column configuration for the table
const columns = ref([
  { key: 'item_code', label: 'Item Code', visible: true },
  { key: 'item_name', label: 'Item Name', visible: true },
  { key: 'item_category', label: 'Category', visible: true },
  { key: 'buyer', label: 'Buyer', visible: true },
  { key: 'ppn', label: 'PPN', visible: true },
  { key: 'pph', label: 'PPH', visible: true },
]);

// Computed property to get only visible columns
const visibleColumns = computed(() => {
  return columns.value.filter(column => column.visible);
});

const debouncedSearch = debounce(() => {
  const params: Record<string, string> = {
    search: search.value,
    sort: sortField.value,
    direction: sortDirection.value
  };

  if (category.value && category.value !== 'all') {
    params.category = category.value;
  }

  if (buyer.value && buyer.value !== 'all') {
    params.buyer = buyer.value;
  }

  router.get(
    '/master-items',
    params,
    { preserveState: true, replace: true }
  );
}, 300);

watch(search, debouncedSearch);

function updateFilter(type: string, value: string) {
  if (type === 'category') {
    category.value = value;
  } else if (type === 'buyer') {
    buyer.value = value;
  }

  const params: Record<string, string> = {
    search: search.value,
    sort: sortField.value,
    direction: sortDirection.value
  };

  if (category.value && category.value !== 'all') {
    params.category = category.value;
  }

  if (buyer.value && buyer.value !== 'all') {
    params.buyer = buyer.value;
  }

  router.get(
    '/master-items',
    params,
    { preserveState: true, replace: true }
  );
}

function resetFilters() {
  search.value = '';
  category.value = 'all';
  buyer.value = 'all';
  sortField.value = 'item_code';
  sortDirection.value = 'asc';

  // Explicitly set page to 1 when resetting filters
  router.get('/master-items', { page: '1' }, { preserveState: true, replace: true });
}

function sort(field: string) {
  // If clicking on the same field, toggle direction
  if (field === sortField.value) {
    sortDirection.value = sortDirection.value === 'asc' ? 'desc' : 'asc';
  } else {
    // If clicking on a new field, set it as the sort field and default to ascending
    sortField.value = field;
    sortDirection.value = 'asc';
  }

  // Build params object with all current filters and sort settings
  const params: Record<string, string> = {
    sort: sortField.value,
    direction: sortDirection.value
  };

  if (search.value) {
    params.search = search.value;
  }

  if (category.value && category.value !== 'all') {
    params.category = category.value;
  }

  if (buyer.value && buyer.value !== 'all') {
    params.buyer = buyer.value;
  }

  // Navigate with the updated params
  router.get('/master-items', params, { preserveState: true, replace: true });
}

function openCreateModal() {
  showCreateModal.value = true;
}

function openEditModal(item: MasterItem) {
  selectedItem.value = item;
  showEditModal.value = true;
}

function openViewModal(item: MasterItem) {
  selectedItem.value = item;
  showViewModal.value = true;
}

function closeModals() {
  showCreateModal.value = false;
  showEditModal.value = false;
  showViewModal.value = false;
  selectedItem.value = null;
}


function deleteItem(id: number) {
  if (confirm('Are you sure you want to delete this item?')) {
    router.delete(`/master-items/${id}`);
  }
}
</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <Head title="Master Items" />

    <div class="flex flex-col space-y-6 px-4 py-6">
      <div class="flex items-center justify-between">
        <HeadingSmall title="Master Items" description="Manage your master items" />
        <div class="flex items-center gap-2">
          <ColumnSelector
            v-model:columns="columns"
            page="master_items"
          />
          <Button @click="openCreateModal"><Plus class="mr-2 h-4 w-4" />Add New Item</Button>
        </div>
      </div>

      <!-- Filters - Redesigned to be more minimalist -->
      <div class="rounded-lg border bg-card shadow-sm">
        <div class="flex flex-col md:flex-row items-center justify-between p-4 gap-4">
          <div class="relative w-full md:w-1/3">
            <SearchIcon class="absolute left-2.5 top-2.5 h-4 w-4 text-muted-foreground" />
            <Input
              id="search"
              v-model="search"
              placeholder="Search by code, name, category or buyer"
              class="pl-8"
            />
          </div>

          <div class="flex flex-wrap items-center gap-2 w-full md:w-auto">
            <Select v-model="category" @update:modelValue="updateFilter('category', $event)" class="w-full md:w-auto">
              <SelectTrigger id="category" class="min-w-[150px]">
                <Filter class="mr-2 h-4 w-4" />
                <SelectValue placeholder="All Categories" />
              </SelectTrigger>
              <SelectContent>
                <SelectItem value="all">All Categories</SelectItem>
                <SelectItem v-for="cat in categories" :key="cat" :value="cat">
                  {{ cat }}
                </SelectItem>
              </SelectContent>
            </Select>

            <Select v-model="buyer" @update:modelValue="updateFilter('buyer', $event)" class="w-full md:w-auto">
              <SelectTrigger id="buyer" class="min-w-[150px]">
                <Filter class="mr-2 h-4 w-4" />
                <SelectValue placeholder="All Buyers" />
              </SelectTrigger>
              <SelectContent>
                <SelectItem value="all">All Buyers</SelectItem>
                <SelectItem v-for="b in buyers" :key="b" :value="b">
                  {{ b }}
                </SelectItem>
              </SelectContent>
            </Select>

            <Button variant="outline" size="sm" @click="resetFilters">
              <ArrowDown class="mr-2 h-4 w-4 rotate-45" />
              Reset
            </Button>
          </div>
        </div>
      </div>

      <!-- Table -->
      <div class="rounded-lg border shadow-sm">
        <Table>
          <TableHeader>
            <TableRow>
              <!-- Dynamic column headers based on visibility -->
              <template v-for="column in columns" :key="column.key">
                <TableHead
                  v-if="column.visible"
                  class="cursor-pointer hover:bg-muted/50 text-center"
                  @click="sort(column.key)"
                >
                  <div class="flex items-center justify-center space-x-1">
                    <span>{{ column.label }}</span>
                    <ArrowUp v-if="sortField === column.key && sortDirection === 'asc'" class="h-4 w-4" />
                    <ArrowDown v-if="sortField === column.key && sortDirection === 'desc'" class="h-4 w-4" />
                  </div>
                </TableHead>
              </template>
              <!-- Always show Actions column -->
              <TableHead class="text-center">
                <div class="flex items-center justify-center space-x-1">
                  <span>Actions</span>
                </div>
              </TableHead>
            </TableRow>
          </TableHeader>
          <TableBody>
            <TableRow v-for="item in masterItems?.data || []" :key="item.id">
              <!-- Dynamic cells based on column visibility -->
              <TableCell v-if="columns.find(col => col.key === 'item_code' && col.visible)" class="text-center font-medium">
                {{ item.item_code }}
              </TableCell>
              <TableCell v-if="columns.find(col => col.key === 'item_name' && col.visible)" class="text-center">
                {{ item.item_name }}
              </TableCell>
              <TableCell v-if="columns.find(col => col.key === 'item_category' && col.visible)" class="text-center">
                {{ item.item_category }}
              </TableCell>
              <TableCell v-if="columns.find(col => col.key === 'buyer' && col.visible)" class="text-center">
                {{ item.buyer }}
              </TableCell>
              <TableCell v-if="columns.find(col => col.key === 'ppn' && col.visible)" class="text-center">
                {{ item.ppn }}%
              </TableCell>
              <TableCell v-if="columns.find(col => col.key === 'pph' && col.visible)" class="text-center">
                {{ item.pph }}%
              </TableCell>
              <!-- Always show Actions cell -->
              <TableCell class="text-center">
                <div class="flex justify-center">
                  <DropdownMenu>
                    <DropdownMenuTrigger asChild>
                      <Button variant="ghost" size="icon">
                        <MoreHorizontal class="h-4 w-4" />
                      </Button>
                    </DropdownMenuTrigger>
                    <DropdownMenuContent align="end">
                      <DropdownMenuItem @click="openViewModal(item)">
                        <Eye class="mr-2 h-4 w-4" />
                        View
                      </DropdownMenuItem>
                      <DropdownMenuItem @click="openEditModal(item)">
                        <Pencil class="mr-2 h-4 w-4" />
                        Edit
                      </DropdownMenuItem>
                      <DropdownMenuItem @click="deleteItem(item.id)" class="text-destructive focus:text-destructive">
                        <Trash2 class="mr-2 h-4 w-4" />
                        Delete
                      </DropdownMenuItem>
                    </DropdownMenuContent>
                  </DropdownMenu>
                </div>
              </TableCell>
            </TableRow>
            <TableRow v-if="!masterItems?.data?.length">
              <TableCell :colspan="visibleColumns.length + 1" class="text-center py-2">
                No master items found.
              </TableCell>
            </TableRow>
          </TableBody>
        </Table>
      </div>

      <!-- Pagination -->
      <TablePagination
        :meta="masterItems?.meta"
        :allowedPerPageValues="[10, 20, 50, 100]"
        route-path="/master-items"
        :queryParams="{
          search: search,
          sort: sortField,
          direction: sortDirection,
          ...(category && category !== 'all' ? { category: category } : {}),
          ...(buyer && buyer !== 'all' ? { buyer: buyer } : {})
        }"
      />
    </div>

    <!-- Modals -->
    <CreateItemModal :show="showCreateModal" @close="closeModals" />
    <EditItemModal
      v-if="selectedItem"
      :show="showEditModal"
      :master-item="selectedItem"
      @close="closeModals"
    />
    <ViewItemModal
      v-if="selectedItem"
      :show="showViewModal"
      :master-item="selectedItem"
      @close="closeModals"
    />
  </AppLayout>
</template>

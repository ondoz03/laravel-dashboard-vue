<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch, computed, onMounted } from 'vue';
import { debounce } from 'lodash';
import { Eye, Pencil, Trash2, ArrowUp, ArrowDown, MoreHorizontal, MoreVertical, Plus, Filter, Search as SearchIcon, X, Check } from 'lucide-vue-next';
import { ComboboxPopover } from '@/components/ui/combobox-popover';

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
import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogFooter,
  DialogHeader,
  DialogTitle,
  DialogTrigger,
} from '@/components/ui/dialog';
import { Checkbox } from '@/components/ui/checkbox';
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
// Convert single string values to arrays for multi-select
const category = ref<string[]>(props.filters.category === '' ? [] : [props.filters.category]);
const buyer = ref<string[]>(props.filters.buyer === '' ? [] : [props.filters.buyer]);

// Refs for filter components
const categoryFilter = ref(null);
const buyerFilter = ref(null);

// Sorting state
const sortField = ref(props.sort?.field || 'item_code');
const sortDirection = ref(props.sort?.direction || 'asc');

// Set initial selected values when component is mounted
onMounted(() => {
  if (categoryFilter.value && category.value.length > 0) {
    categoryFilter.value.setSelected(category.value);
  }
  if (buyerFilter.value && buyer.value.length > 0) {
    buyerFilter.value.setSelected(buyer.value);
  }
});


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

  if (category.value.length > 0) {
    params.category = category.value.join(',');
  }

  if (buyer.value.length > 0) {
    params.buyer = buyer.value.join(',');
  }

  router.get(
    '/master-items',
    params,
    { preserveState: true, replace: true, preserveScroll: true }
  );
}, 300);

watch(search, debouncedSearch);

function updateFilter(type: string, value: string, checked: boolean) {
  if (type === 'category') {
    if (checked) {
      // Add the value if it's not already in the array
      if (!category.value.includes(value)) {
        category.value.push(value);
      }
    } else {
      // Remove the value from the array
      category.value = category.value.filter(v => v !== value);
    }
  } else if (type === 'buyer') {
    if (checked) {
      // Add the value if it's not already in the array
      if (!buyer.value.includes(value)) {
        buyer.value.push(value);
      }
    } else {
      // Remove the value from the array
      buyer.value = buyer.value.filter(v => v !== value);
    }
  }

  const params: Record<string, string> = {
    search: search.value,
    sort: sortField.value,
    direction: sortDirection.value
  };

  if (category.value.length > 0) {
    params.category = category.value.join(',');
  }

  if (buyer.value.length > 0) {
    params.buyer = buyer.value.join(',');
  }

  router.get(
    '/master-items',
    params,
    { preserveState: true, replace: true, preserveScroll: true }
  );
}

function resetFilters() {
  search.value = '';
  category.value = [];
  buyer.value = [];
  sortField.value = 'item_code';
  sortDirection.value = 'asc';

  // Reset the ComboboxPopover components
  if (categoryFilter.value) {
    categoryFilter.value.setSelected([]);
  }
  if (buyerFilter.value) {
    buyerFilter.value.setSelected([]);
  }

  // Explicitly set page to 1 when resetting filters
  router.get('/master-items', { page: '1' }, { preserveState: true, replace: true, preserveScroll: true });
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

  if (category.value.length > 0) {
    params.category = category.value.join(',');
  }

  if (buyer.value.length > 0) {
    params.buyer = buyer.value.join(',');
  }

  // Navigate with the updated params
  router.get('/master-items', params, { preserveState: true, replace: true, preserveScroll: true });
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
          <Button size="sm" @click="openCreateModal"><Plus class="mr-2 h-4 w-4" />Add New Item</Button>
        </div>
      </div>

      <!-- Filters - Redesigned to match the requested UI design -->
      <div class="flex items-center justify-between">
        <div class="flex flex-1 items-center gap-2">
          <input
            data-slot="input"
            class="file:text-foreground placeholder:text-muted-foreground selection:bg-primary selection:text-primary-foreground dark:bg-input/30 border-input flex min-w-0 rounded-md border bg-transparent px-3 py-1 text-base shadow-xs transition-[color,box-shadow] outline-none file:inline-flex file:h-7 file:border-0 file:bg-transparent file:text-sm file:font-medium disabled:pointer-events-none disabled:cursor-not-allowed disabled:opacity-50 md:text-sm focus-visible:border-ring focus-visible:ring-ring/50 focus-visible:ring-[3px] aria-invalid:ring-destructive/20 dark:aria-invalid:ring-destructive/40 aria-invalid:border-destructive h-8 w-[150px] lg:w-[250px]"
            placeholder="Filter tasks..."
            v-model="search"
          >

          <!-- Category Filter with ComboboxPopover -->
          <div id="category-filter-container" class="relative">
            <ComboboxPopover
              ref="categoryFilter"
              placeholder="Category"
              label="Category"
              :options="categories.map(cat => ({ value: cat, label: cat }))"
              @update:selected="
                (selected) => {
                  category = Array.isArray(selected) ? selected.map(item => item.value) : [selected];
                  updateFilter('category', '', false);
                }
              "
            />
            <div v-if="category.length > 0" class="absolute top-0 right-0 -mt-2 -mr-2">
              <span data-slot="badge" class="inline-flex items-center justify-center border py-0.5 text-xs w-fit whitespace-nowrap shrink-0 [&>svg]:size-3 gap-1 [&>svg]:pointer-events-none focus-visible:border-ring focus-visible:ring-ring/50 focus-visible:ring-[3px] aria-invalid:ring-destructive/20 dark:aria-invalid:ring-destructive/40 aria-invalid:border-destructive transition-[color,box-shadow] overflow-hidden border-transparent bg-secondary text-secondary-foreground [a&]:hover:bg-secondary/90 rounded-sm px-1 font-normal">{{ category.length }}</span>
            </div>
          </div>

          <!-- Buyer Filter with ComboboxPopover -->
          <div id="buyer-filter-container" class="relative">
            <ComboboxPopover
              ref="buyerFilter"
              placeholder="Priority"
              label="Priority"
              :options="buyers.map(b => ({ value: b, label: b }))"
              @update:selected="
                (selected) => {
                  buyer = Array.isArray(selected) ? selected.map(item => item.value) : [selected];
                  updateFilter('buyer', '', false);
                }
              "
            />
            <div v-if="buyer.length > 0" class="absolute top-0 right-0 -mt-2 -mr-2">
              <span data-slot="badge" class="inline-flex items-center justify-center border py-0.5 text-xs w-fit whitespace-nowrap shrink-0 [&>svg]:size-3 gap-1 [&>svg]:pointer-events-none focus-visible:border-ring focus-visible:ring-ring/50 focus-visible:ring-[3px] aria-invalid:ring-destructive/20 dark:aria-invalid:ring-destructive/40 aria-invalid:border-destructive transition-[color,box-shadow] overflow-hidden border-transparent bg-secondary text-secondary-foreground [a&]:hover:bg-secondary/90 rounded-sm px-1 font-normal">{{ buyer.length }}</span>
            </div>
          </div>

          <!-- Reset Filters Button -->
          <Button
            data-slot="button"
            variant="ghost"
            size="sm"
            @click="resetFilters"
            class="hover:bg-accent hover:text-accent-foreground dark:hover:bg-accent/50 h-8 rounded-md gap-1.5 px-3 has-[>svg]:px-2.5"
          >
            Reset
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-x">
              <path d="M18 6 6 18"></path>
              <path d="m6 6 12 12"></path>
            </svg>
          </Button>
        </div>
        <div class="flex items-center gap-2">
          <Button
            data-slot="button"
            class="items-center justify-center whitespace-nowrap text-sm font-medium transition-all disabled:pointer-events-none disabled:opacity-50 [&_svg]:pointer-events-none [&_svg:not([class*='size-'])]:size-4 shrink-0 [&_svg]:shrink-0 outline-none focus-visible:border-ring focus-visible:ring-ring/50 focus-visible:ring-[3px] aria-invalid:ring-destructive/20 dark:aria-invalid:ring-destructive/40 aria-invalid:border-destructive border bg-background shadow-xs hover:bg-accent hover:text-accent-foreground dark:bg-input/30 dark:border-input dark:hover:bg-input/50 rounded-md gap-1.5 px-3 has-[>svg]:px-2.5 ml-auto hidden h-8 lg:flex"
            type="button"
          >
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-settings2">
              <path d="M20 7h-9"></path>
              <path d="M14 17H5"></path>
              <circle cx="17" cy="17" r="3"></circle>
              <circle cx="7" cy="7" r="3"></circle>
            </svg>
            View
          </Button>
          <Button
            size="sm"
            @click="openCreateModal"
            class="inline-flex items-center justify-center whitespace-nowrap text-sm font-medium transition-all disabled:pointer-events-none disabled:opacity-50 [&_svg]:pointer-events-none [&_svg:not([class*='size-'])]:size-4 shrink-0 [&_svg]:shrink-0 outline-none focus-visible:border-ring focus-visible:ring-ring/50 focus-visible:ring-[3px] aria-invalid:ring-destructive/20 dark:aria-invalid:ring-destructive/40 aria-invalid:border-destructive bg-primary text-primary-foreground shadow-xs hover:bg-primary/90 h-8 rounded-md gap-1.5 px-3 has-[>svg]:px-2.5"
          >
            Add Task
          </Button>
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
          ...(category.length > 0 ? { category: category.join(',') } : {}),
          ...(buyer.length > 0 ? { buyer: buyer.join(',') } : {})
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

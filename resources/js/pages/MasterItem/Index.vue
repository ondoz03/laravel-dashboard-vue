<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import { debounce } from 'lodash';
import { Eye, Pencil, Trash2, ArrowUp, ArrowDown, MoreHorizontal, MoreVertical } from 'lucide-vue-next';

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
        <Button @click="openCreateModal"><Plus class="mr-2 h-4 w-4" />Add New Item</Button>
      </div>

      <!-- Filters -->
      <div class="rounded-lg border p-4">
        <div class="space-y-4">
          <h3 class="text-sm font-medium">Filters</h3>
          <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
            <div class="grid gap-2">
              <Label for="search">Search</Label>
              <Input
                id="search"
                v-model="search"
                placeholder="Search by code, name, category or buyer"
              />
            </div>
            <div class="grid gap-2">
              <Label for="category">Category</Label>
              <Select v-model="category" @update:modelValue="updateFilter('category', $event)">
                <SelectTrigger id="category">
                  <SelectValue placeholder="Select category" />
                </SelectTrigger>
                <SelectContent>
                  <SelectItem value="all">All Categories</SelectItem>
                  <SelectItem v-for="cat in categories" :key="cat" :value="cat">
                    {{ cat }}
                  </SelectItem>
                </SelectContent>
              </Select>
            </div>
            <div class="grid gap-2">
              <Label for="buyer">Buyer</Label>
              <Select v-model="buyer" @update:modelValue="updateFilter('buyer', $event)">
                <SelectTrigger id="buyer">
                  <SelectValue placeholder="Select buyer" />
                </SelectTrigger>
                <SelectContent>
                  <SelectItem value="all">All Buyers</SelectItem>
                  <SelectItem v-for="b in buyers" :key="b" :value="b">
                    {{ b }}
                  </SelectItem>
                </SelectContent>
              </Select>
            </div>
          </div>
          <div class="flex justify-end">
            <Button variant="outline" @click="resetFilters">Reset Filters</Button>
          </div>
        </div>
      </div>

      <!-- Table -->
      <div class="rounded-lg border shadow-sm">
        <Table>
          <TableHeader>
            <TableRow>
              <TableHead
                class="cursor-pointer hover:bg-muted/50 text-center"
                @click="sort('item_code')"
              >
                <div class="flex items-center justify-center space-x-1">
                  <span>Item Code</span>
                  <ArrowUp v-if="sortField === 'item_code' && sortDirection === 'asc'" class="h-4 w-4" />
                  <ArrowDown v-if="sortField === 'item_code' && sortDirection === 'desc'" class="h-4 w-4" />
                </div>
              </TableHead>
              <TableHead
                class="cursor-pointer hover:bg-muted/50 text-center"
                @click="sort('item_name')"
              >
                <div class="flex items-center justify-center space-x-1">
                  <span>Item Name</span>
                  <ArrowUp v-if="sortField === 'item_name' && sortDirection === 'asc'" class="h-4 w-4" />
                  <ArrowDown v-if="sortField === 'item_name' && sortDirection === 'desc'" class="h-4 w-4" />
                </div>
              </TableHead>
              <TableHead
                class="cursor-pointer hover:bg-muted/50 text-center"
                @click="sort('item_category')"
              >
                <div class="flex items-center justify-center space-x-1">
                  <span>Category</span>
                  <ArrowUp v-if="sortField === 'item_category' && sortDirection === 'asc'" class="h-4 w-4" />
                  <ArrowDown v-if="sortField === 'item_category' && sortDirection === 'desc'" class="h-4 w-4" />
                </div>
              </TableHead>
              <TableHead
                class="cursor-pointer hover:bg-muted/50 text-center"
                @click="sort('buyer')"
              >
                <div class="flex items-center justify-center space-x-1">
                  <span>Buyer</span>
                  <ArrowUp v-if="sortField === 'buyer' && sortDirection === 'asc'" class="h-4 w-4" />
                  <ArrowDown v-if="sortField === 'buyer' && sortDirection === 'desc'" class="h-4 w-4" />
                </div>
              </TableHead>
              <TableHead
                class="cursor-pointer hover:bg-muted/50 text-center"
                @click="sort('ppn')"
              >
                <div class="flex items-center justify-center space-x-1">
                  <span>PPN</span>
                  <ArrowUp v-if="sortField === 'ppn' && sortDirection === 'asc'" class="h-4 w-4" />
                  <ArrowDown v-if="sortField === 'ppn' && sortDirection === 'desc'" class="h-4 w-4" />
                </div>
              </TableHead>
              <TableHead
                class="cursor-pointer hover:bg-muted/50 text-center"
                @click="sort('pph')"
              >
                <div class="flex items-center justify-center space-x-1">
                  <span>PPH</span>
                  <ArrowUp v-if="sortField === 'pph' && sortDirection === 'asc'" class="h-4 w-4" />
                  <ArrowDown v-if="sortField === 'pph' && sortDirection === 'desc'" class="h-4 w-4" />
                </div>
              </TableHead>
              <TableHead class="text-center">
                  <div class="flex items-center justify-center space-x-1">
                      <span>Actions</span>
                  </div>
              </TableHead>
            </TableRow>
          </TableHeader>
          <TableBody>
            <TableRow v-for="item in masterItems?.data || []" :key="item.id">
              <TableCell class="text-center font-medium">{{ item.item_code }}</TableCell>
              <TableCell class="text-center">{{ item.item_name }}</TableCell>
              <TableCell class="text-center">{{ item.item_category }}</TableCell>
              <TableCell class="text-center">{{ item.buyer }}</TableCell>
              <TableCell class="text-center">{{ item.ppn }}%</TableCell>
              <TableCell class="text-center">{{ item.pph }}%</TableCell>
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
              <TableCell colspan="7" class="text-center py-2">
                No master items found.
              </TableCell>
            </TableRow>
          </TableBody>
        </Table>
      </div>

      <!-- Pagination -->
      <TablePagination
        :meta="masterItems?.meta"
        :allowed-per-page-values="[10, 20, 50, 100]"
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

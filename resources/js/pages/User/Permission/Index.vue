<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <Head title="Permissions" />

    <div class="flex flex-col space-y-6 px-4 py-6">
      <div class="flex items-center justify-between">
        <HeadingSmall title="Permissions" description="Manage your permissions" />
        <div class="flex items-center gap-2">
          <ColumnSelector
            v-model:columns="columns"
            page="permissions"
          />
          <Button @click="openCreateModal"><Plus class="mr-2 h-4 w-4" />Add New Permission</Button>
        </div>
      </div>

      <!-- Filters -->
      <div class="rounded-lg border bg-card shadow-sm">
        <div class="flex flex-col md:flex-row items-center justify-between p-4 gap-4">
          <div class="relative w-full md:w-1/2">
            <SearchIcon class="absolute left-2.5 top-2.5 h-4 w-4 text-muted-foreground" />
            <Input
              id="search"
              v-model="search"
              placeholder="Search by name"
              class="pl-8"
            />
          </div>

          <div class="flex items-center gap-2 w-full md:w-auto">
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
            <TableRow v-for="permission in permissions?.data || []" :key="permission.id">
              <!-- Name column -->
              <TableCell v-if="columns.find(col => col.key === 'name' && col.visible)" class="text-center font-medium">
                {{ permission.name }}
              </TableCell>
              <!-- Created At column -->
              <TableCell v-if="columns.find(col => col.key === 'created_at' && col.visible)" class="text-center">
                {{ new Date(permission.created_at).toLocaleDateString() }}
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
                      <DropdownMenuItem @click="openViewModal(permission)">
                        <Eye class="mr-2 h-4 w-4" />
                        View
                      </DropdownMenuItem>
                      <DropdownMenuItem @click="openEditModal(permission)">
                        <Pencil class="mr-2 h-4 w-4" />
                        Edit
                      </DropdownMenuItem>
                      <DropdownMenuItem @click="deletePermission(permission.id)" class="text-destructive focus:text-destructive">
                        <Trash2 class="mr-2 h-4 w-4" />
                        Delete
                      </DropdownMenuItem>
                    </DropdownMenuContent>
                  </DropdownMenu>
                </div>
              </TableCell>
            </TableRow>
            <TableRow v-if="!permissions?.data?.length">
              <TableCell :colspan="visibleColumns.length + 1" class="text-center py-2">
                No permissions found.
              </TableCell>
            </TableRow>
          </TableBody>
        </Table>

        <div class="p-4 border-t">
          <TablePagination
            :meta="permissions?.meta"
            :per-page="perPage"
            :allowed-per-page-values="allowedPerPageValues"
            :route-path="props['route-path'] || '/permissions'"
            @update:per-page="updatePerPage"
          />
        </div>
      </div>
    </div>

    <CreatePermissionModal :show="showCreateModal" @close="closeModals" />
    <EditPermissionModal
      v-if="selectedPermission"
      :show="showEditModal"
      :permission="selectedPermission"
      @close="closeModals"
    />
    <ViewPermissionModal
      v-if="selectedPermission"
      :show="showViewModal"
      :permission="selectedPermission"
      @close="closeModals"
    />
  </AppLayout>
</template>

<script setup lang="ts">
import { ref, computed, watch } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import { debounce } from 'lodash';
import { Button } from '@/components/ui/button';
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from '@/components/ui/table';
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { Eye, MoreHorizontal, Pencil, Plus, Trash2, ArrowUp, ArrowDown, Search as SearchIcon } from 'lucide-vue-next';
import HeadingSmall from '@/components/HeadingSmall.vue';
import { Input } from '@/components/ui/input';
import { TablePagination } from '@/components/ui/table-pagination';
import { ColumnSelector } from '@/components/ui/column-selector';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import CreatePermissionModal from '../../Permission/CreatePermissionModal.vue';
import EditPermissionModal from '../../Permission/EditPermissionModal.vue';
import ViewPermissionModal from '../../Permission/ViewPermissionModal.vue';

interface Permission {
  id: number;
  name: string;
  created_at: string;
  updated_at: string;
}

interface Props {
  permissions?: {
    data: Permission[];
    meta: any;
  };
  filters?: {
    search: string;
  };
  sort?: {
    field: string;
    direction: 'asc' | 'desc';
  };
  allowedPerPageValues?: number[];
  'route-path'?: string;
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Permissions',
    href: '/permissions',
  },
];

// Search and filters
const search = ref(props.filters?.search || '');

// Sorting state
const sortField = ref(props.sort?.field || 'name');
const sortDirection = ref(props.sort?.direction || 'asc');

// Column configuration for the table
const columns = ref([
  { key: 'name', label: 'Name', visible: true },
  { key: 'created_at', label: 'Created At', visible: true },
]);

// Computed property to get only visible columns
const visibleColumns = computed(() => {
  return columns.value.filter(column => column.visible);
});

const perPage = ref(props.permissions?.meta?.per_page || 10);
const allowedPerPageValues = computed(() => props.allowedPerPageValues || [10, 20, 50, 100]);

// Modals
const showCreateModal = ref(false);
const showEditModal = ref(false);
const showViewModal = ref(false);
const selectedPermission = ref<Permission | null>(null);

// Debounced search
watch(
  search,
  debounce(function (value) {
    router.get('/permissions', { search: value, page: 1 }, { preserveState: true, replace: true, preserveScroll: true });
  }, 300)
);

function resetFilters() {
  search.value = '';
  sortField.value = 'name';
  sortDirection.value = 'asc';

  // Explicitly set page to 1 when resetting filters
  router.get('/permissions', { page: '1' }, { preserveState: true, replace: true, preserveScroll: true });
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

  // Navigate with the updated params
  router.get('/permissions', params, { preserveState: true, replace: true, preserveScroll: true });
}

function updatePerPage(value: number) {
  perPage.value = value;
}

function openCreateModal() {
  showCreateModal.value = true;
  showEditModal.value = false;
  showViewModal.value = false;
}

function openEditModal(permission: Permission) {
  selectedPermission.value = permission;
  showEditModal.value = true;
  showCreateModal.value = false;
  showViewModal.value = false;
}

function openViewModal(permission: Permission) {
  selectedPermission.value = permission;
  showViewModal.value = true;
  showCreateModal.value = false;
  showEditModal.value = false;
}

function closeModals() {
  showCreateModal.value = false;
  showEditModal.value = false;
  showViewModal.value = false;
  selectedPermission.value = null;
}

function deletePermission(id: number) {
  if (confirm('Are you sure you want to delete this permission?')) {
    router.delete(`/permissions/${id}`);
  }
}
</script>

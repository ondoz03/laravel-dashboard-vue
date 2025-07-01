<template>
  <div>
    <Head title="Permissions" />

    <div class="container mx-auto py-6">
      <HeadingSmall title="Permissions" description="Manage your permissions" />

      <div class="flex justify-between items-center mb-6">
        <SearchFilter
          v-model="search"
          @reset="resetFilters"
          page="permissions"
          class="mr-4 flex-grow max-w-md"
        >
          <Button @click="openCreateModal"><Plus class="mr-2 h-4 w-4" />Add New Permission</Button>
        </SearchFilter>
      </div>

      <Card>
        <Table>
          <TableHeader>
            <TableRow>
              <TableHead
                :sortable="true"
                :direction="sort.field === 'name' ? sort.direction : null"
                @click="updateSort('name')"
              >
                Name
              </TableHead>
              <TableHead
                :sortable="true"
                :direction="sort.field === 'created_at' ? sort.direction : null"
                @click="updateSort('created_at')"
              >
                Created At
              </TableHead>
              <TableHead class="text-right">Actions</TableHead>
            </TableRow>
          </TableHeader>
          <TableBody>
            <TableRow v-for="permission in permissions?.data || []" :key="permission.id">
              <TableCell>
                {{ permission.name }}
              </TableCell>
              <TableCell>
                {{ new Date(permission.created_at).toLocaleDateString() }}
              </TableCell>
              <TableCell class="text-right">
                <DropdownMenu>
                  <DropdownMenuTrigger as-child>
                    <Button variant="ghost" size="icon">
                      <MoreHorizontal class="h-4 w-4" />
                      <span class="sr-only">Open menu</span>
                    </Button>
                  </DropdownMenuTrigger>
                  <DropdownMenuContent align="end">
                    <DropdownMenuItem @click="openViewModal(permission)">
                      <Eye class="mr-2 h-4 w-4" /> View
                    </DropdownMenuItem>
                    <DropdownMenuItem @click="openEditModal(permission)">
                      <Pencil class="mr-2 h-4 w-4" /> Edit
                    </DropdownMenuItem>
                    <DropdownMenuItem @click="deletePermission(permission.id)" class="text-destructive focus:text-destructive">
                      <Trash class="mr-2 h-4 w-4" /> Delete
                    </DropdownMenuItem>
                  </DropdownMenuContent>
                </DropdownMenu>
              </TableCell>
            </TableRow>
            <TableRow v-if="!permissions?.data?.length">
              <TableCell colspan="3" class="text-center py-8">
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
      </Card>
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
  </div>
</template>

<script setup lang="ts">
import { ref, computed, watch } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import { debounce } from 'lodash';
import { Button } from '@/components/ui/button';
import { Card } from '@/components/ui/card';
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
import { Eye, MoreHorizontal, Pencil, Plus, Trash } from 'lucide-vue-next';
import HeadingSmall from '@/components/HeadingSmall.vue';
import SearchFilter from '@/components/SearchFilter.vue';
import TablePagination from '@/components/ui/table-pagination/TablePagination.vue';
import CreatePermissionModal from './CreatePermissionModal.vue';
import EditPermissionModal from './EditPermissionModal.vue';
import ViewPermissionModal from './ViewPermissionModal.vue';

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

// Search and filters
const search = ref(props.filters?.search || '');
const sort = ref({
  field: props.sort?.field || 'name',
  direction: props.sort?.direction || 'asc',
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
  router.get('/permissions', { page: '1' }, { preserveState: true, replace: true, preserveScroll: true });
}

function updateSort(field: string) {
  let direction = 'asc';
  if (sort.value.field === field) {
    direction = sort.value.direction === 'asc' ? 'desc' : 'asc';
  }
  sort.value = { field, direction };

  const params = {
    sort: field,
    direction,
    search: search.value,
  };

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

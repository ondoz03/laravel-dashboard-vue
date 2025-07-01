<template>
  <div>
    <Head title="Roles" />

    <div class="container mx-auto py-6">
      <HeadingSmall title="Roles" description="Manage your roles" />

      <div class="flex justify-between items-center mb-6">
        <SearchFilter
          v-model="search"
          @reset="resetFilters"
          page="roles"
          class="mr-4 flex-grow max-w-md"
        >
          <Button @click="openCreateModal"><Plus class="mr-2 h-4 w-4" />Add New Role</Button>
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
              <TableHead>Permissions</TableHead>
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
            <TableRow v-for="role in roles?.data || []" :key="role.id">
              <TableCell>
                {{ role.name }}
              </TableCell>
              <TableCell>
                <div class="flex flex-wrap gap-1">
                  <Badge v-for="permission in role.permissions" :key="permission.id" variant="outline">
                    {{ permission.name }}
                  </Badge>
                  <span v-if="!role.permissions || role.permissions.length === 0" class="text-muted-foreground">
                    No permissions
                  </span>
                </div>
              </TableCell>
              <TableCell>
                {{ new Date(role.created_at).toLocaleDateString() }}
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
                    <DropdownMenuItem @click="openViewModal(role)">
                      <Eye class="mr-2 h-4 w-4" /> View
                    </DropdownMenuItem>
                    <DropdownMenuItem @click="openEditModal(role)">
                      <Pencil class="mr-2 h-4 w-4" /> Edit
                    </DropdownMenuItem>
                    <DropdownMenuItem @click="deleteRole(role.id)" class="text-destructive focus:text-destructive">
                      <Trash class="mr-2 h-4 w-4" /> Delete
                    </DropdownMenuItem>
                  </DropdownMenuContent>
                </DropdownMenu>
              </TableCell>
            </TableRow>
            <TableRow v-if="!roles?.data?.length">
              <TableCell colspan="4" class="text-center py-8">
                No roles found.
              </TableCell>
            </TableRow>
          </TableBody>
        </Table>

        <div class="p-4 border-t">
          <TablePagination
            :meta="roles?.meta"
            :per-page="perPage"
            :allowed-per-page-values="allowedPerPageValues"
            :route-path="props['route-path'] || '/roles'"
            @update:per-page="updatePerPage"
          />
        </div>
      </Card>
    </div>

    <CreateRoleModal :show="showCreateModal" @close="closeModals" />
    <EditRoleModal
      v-if="selectedRole"
      :show="showEditModal"
      :role="selectedRole"
      @close="closeModals"
    />
    <ViewRoleModal
      v-if="selectedRole"
      :show="showViewModal"
      :role="selectedRole"
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
import { Badge } from '@/components/ui/badge';
import { Eye, MoreHorizontal, Pencil, Plus, Trash } from 'lucide-vue-next';
import HeadingSmall from '@/components/HeadingSmall.vue';
import SearchFilter from '@/components/SearchFilter.vue';
import TablePagination from '@/components/ui/table-pagination/TablePagination.vue';
import CreateRoleModal from './CreateRoleModal.vue';
import EditRoleModal from './EditRoleModal.vue';
import ViewRoleModal from './ViewRoleModal.vue';

interface Permission {
  id: number;
  name: string;
}

interface Role {
  id: number;
  name: string;
  permissions: Permission[];
  created_at: string;
  updated_at: string;
}

interface Props {
  roles?: {
    data: Role[];
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
const perPage = ref(props.roles?.meta?.per_page || 10);
const allowedPerPageValues = computed(() => props.allowedPerPageValues || [10, 20, 50, 100]);

// Modals
const showCreateModal = ref(false);
const showEditModal = ref(false);
const showViewModal = ref(false);
const selectedRole = ref<Role | null>(null);

// Debounced search
watch(
  search,
  debounce(function (value) {
    router.get('/roles', { search: value, page: 1 }, { preserveState: true, replace: true, preserveScroll: true });
  }, 300)
);

function resetFilters() {
  search.value = '';
  router.get('/roles', { page: '1' }, { preserveState: true, replace: true, preserveScroll: true });
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

  router.get('/roles', params, { preserveState: true, replace: true, preserveScroll: true });
}

function updatePerPage(value: number) {
  perPage.value = value;
}

function openCreateModal() {
  showCreateModal.value = true;
  showEditModal.value = false;
  showViewModal.value = false;
}

function openEditModal(role: Role) {
  selectedRole.value = role;
  showEditModal.value = true;
  showCreateModal.value = false;
  showViewModal.value = false;
}

function openViewModal(role: Role) {
  selectedRole.value = role;
  showViewModal.value = true;
  showCreateModal.value = false;
  showEditModal.value = false;
}

function closeModals() {
  showCreateModal.value = false;
  showEditModal.value = false;
  showViewModal.value = false;
  selectedRole.value = null;
}

function deleteRole(id: number) {
  if (confirm('Are you sure you want to delete this role?')) {
    router.delete(`/roles/${id}`);
  }
}
</script>

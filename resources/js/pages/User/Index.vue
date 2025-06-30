<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import { debounce } from 'lodash';
import { Eye, Pencil, Trash2, ArrowUp, ArrowDown, MoreHorizontal, Plus } from 'lucide-vue-next';

import HeadingSmall from '@/components/HeadingSmall.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
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
import CreateUserModal from './CreateUserModal.vue';
import EditUserModal from './EditUserModal.vue';
import ViewUserModal from './ViewUserModal.vue';

interface User {
  id: number;
  name: string;
  email: string;
  email_verified_at: string | null;
  created_at: string;
  updated_at: string;
}

interface Props {
  users: {
    data: User[];
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
  };
  sort?: {
    field: string;
    direction: 'asc' | 'desc';
  };
}

const props = withDefaults(defineProps<Props>(), {
  users: () => ({
    data: [],
    links: [],
    meta: {
      current_page: 0,
      from: 0,
      last_page: 0,
      links: [],
      path: '',
      per_page: 10,
      to: 0,
      total: 0
    }
  }),
  filters: () => ({
    search: '',
  }),
  sort: () => ({
    field: 'name',
    direction: 'asc'
  }),
});

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Users',
    href: '/users',
  },
];

const search = ref(props.filters.search);

// Sorting state
const sortField = ref(props.sort?.field || 'name');
const sortDirection = ref(props.sort?.direction || 'asc');

// Modal state
const showCreateModal = ref(false);
const showEditModal = ref(false);
const showViewModal = ref(false);
const selectedUser = ref<User | null>(null);

const debouncedSearch = debounce(() => {
  const params: Record<string, string> = {
    search: search.value,
    sort: sortField.value,
    direction: sortDirection.value
  };

  router.get(
    '/users',
    params,
    { preserveState: true, replace: true }
  );
}, 300);

watch(search, debouncedSearch);

function resetFilters() {
  search.value = '';
  sortField.value = 'name';
  sortDirection.value = 'asc';

  // Explicitly set page to 1 when resetting filters
  router.get('/users', { page: '1' }, { preserveState: true, replace: true });
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
  router.get('/users', params, { preserveState: true, replace: true });
}

function openCreateModal() {
  showCreateModal.value = true;
}

function openEditModal(user: User) {
  selectedUser.value = user;
  showEditModal.value = true;
}

function openViewModal(user: User) {
  selectedUser.value = user;
  showViewModal.value = true;
}

function closeModals() {
  showCreateModal.value = false;
  showEditModal.value = false;
  showViewModal.value = false;
  selectedUser.value = null;
}

function deleteUser(id: number) {
  if (confirm('Are you sure you want to delete this user?')) {
    router.delete(`/users/${id}`);
  }
}
</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <Head title="Users" />

    <div class="flex flex-col space-y-6 px-4 py-6">
      <div class="flex items-center justify-between">
        <HeadingSmall title="Users" description="Manage your users" />
        <Button @click="openCreateModal"><Plus class="mr-2 h-4 w-4" />Add New User</Button>
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
                placeholder="Search by name or email"
              />
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
                @click="sort('name')"
              >
                <div class="flex items-center justify-center space-x-1">
                  <span>Name</span>
                  <ArrowUp v-if="sortField === 'name' && sortDirection === 'asc'" class="h-4 w-4" />
                  <ArrowDown v-if="sortField === 'name' && sortDirection === 'desc'" class="h-4 w-4" />
                </div>
              </TableHead>
              <TableHead
                class="cursor-pointer hover:bg-muted/50 text-center"
                @click="sort('email')"
              >
                <div class="flex items-center justify-center space-x-1">
                  <span>Email</span>
                  <ArrowUp v-if="sortField === 'email' && sortDirection === 'asc'" class="h-4 w-4" />
                  <ArrowDown v-if="sortField === 'email' && sortDirection === 'desc'" class="h-4 w-4" />
                </div>
              </TableHead>
              <TableHead
                class="cursor-pointer hover:bg-muted/50 text-center"
                @click="sort('created_at')"
              >
                <div class="flex items-center justify-center space-x-1">
                  <span>Created At</span>
                  <ArrowUp v-if="sortField === 'created_at' && sortDirection === 'asc'" class="h-4 w-4" />
                  <ArrowDown v-if="sortField === 'created_at' && sortDirection === 'desc'" class="h-4 w-4" />
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
            <TableRow v-for="user in users?.data || []" :key="user.id">
              <TableCell class="text-center font-medium">{{ user.name }}</TableCell>
              <TableCell class="text-center">{{ user.email }}</TableCell>
              <TableCell class="text-center">{{ new Date(user.created_at).toLocaleDateString() }}</TableCell>
              <TableCell class="text-center">
                <div class="flex justify-center">
                  <DropdownMenu>
                    <DropdownMenuTrigger asChild>
                      <Button variant="ghost" size="icon">
                        <MoreHorizontal class="h-4 w-4" />
                      </Button>
                    </DropdownMenuTrigger>
                    <DropdownMenuContent align="end">
                      <DropdownMenuItem @click="openViewModal(user)">
                        <Eye class="mr-2 h-4 w-4" />
                        View
                      </DropdownMenuItem>
                      <DropdownMenuItem @click="openEditModal(user)">
                        <Pencil class="mr-2 h-4 w-4" />
                        Edit
                      </DropdownMenuItem>
                      <DropdownMenuItem @click="deleteUser(user.id)" class="text-destructive focus:text-destructive">
                        <Trash2 class="mr-2 h-4 w-4" />
                        Delete
                      </DropdownMenuItem>
                    </DropdownMenuContent>
                  </DropdownMenu>
                </div>
              </TableCell>
            </TableRow>
            <TableRow v-if="!users?.data?.length">
              <TableCell colspan="4" class="text-center py-2">
                No users found.
              </TableCell>
            </TableRow>
          </TableBody>
        </Table>
      </div>

      <!-- Pagination -->
      <TablePagination
        :meta="users?.meta"
        :allowed-per-page-values="[10, 20, 50, 100]"
        route-path="/users"
        :query-params="{
          search: search,
          sort: sortField,
          direction: sortDirection,
        }"
      />
    </div>

    <!-- Modals -->
    <CreateUserModal :show="showCreateModal" @close="closeModals" />
    <EditUserModal
      v-if="selectedUser"
      :show="showEditModal"
      :user="selectedUser"
      @close="closeModals"
    />
    <ViewUserModal
      v-if="selectedUser"
      :show="showViewModal"
      :user="selectedUser"
      @close="closeModals"
    />
  </AppLayout>
</template>

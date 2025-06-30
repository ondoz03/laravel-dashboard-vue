<script setup lang="ts">
import { ref, watch } from 'vue';
import { router } from '@inertiajs/vue3';
import { Input } from '@/components/ui/input';
import { Button } from '@/components/ui/button';
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from '@/components/ui/select';
import {
  Pagination,
  PaginationContent,
  PaginationEllipsis,
  PaginationItem,
  PaginationLink,
  PaginationNext,
  PaginationPrevious,
} from '@/components/ui/pagination';

interface PaginationMeta {
  current_page: number;
  from: number;
  last_page: number;
  links: {
    url: string | null;
    label: string;
    active: boolean;
  }[];
  path: string;
  per_page: number;
  to: number;
  total: number;
}

interface Props {
  meta?: PaginationMeta;
  allowedPerPageValues?: number[];
  routePath: string;
  queryParams?: Record<string, string>;
}

const props = withDefaults(defineProps<Props>(), {
  allowedPerPageValues: () => [10, 20, 50, 100],
  queryParams: () => ({})
});

const perPage = ref<string>((props.meta?.per_page || 20).toString());
const goToPage = ref<number | null>(null);

// Watch for changes in meta to update perPage
watch(() => props.meta?.per_page, (newValue) => {
  if (newValue) {
    perPage.value = newValue.toString();
  }
});

function updatePerPage(value: string) {
  perPage.value = value;

  // Build query params
  const params: Record<string, string> = {
    ...props.queryParams,
    per_page: perPage.value,
    page: '1' // Reset to first page when changing items per page
  };

  // Navigate with the updated params
  router.get(props.routePath, params, { preserveState: true, replace: true });
}

function jumpToPage(pageNumber?: number) {
  // If pageNumber is provided, use it; otherwise use goToPage.value
  const targetPage = pageNumber || goToPage.value;

  if (!targetPage || targetPage < 1 || targetPage > (props.meta?.last_page || 1)) {
    // Reset to current page if invalid
    goToPage.value = props.meta?.current_page || 1;
    return;
  }

  // Update goToPage.value to match the target page
  goToPage.value = targetPage;

  // Build query params
  const params: Record<string, string> = {
    ...props.queryParams,
    page: targetPage.toString(),
    per_page: perPage.value
  };

  // Navigate to the specified page with preserveState and replace options
  router.get(props.routePath, params, { preserveState: true, replace: true });
}

</script>

<template>
  <div class="flex flex-col space-y-4 sm:flex-row sm:justify-between sm:items-center sm:space-y-0 mt-4 p-4 border rounded-lg bg-background shadow-sm">
    <div class="flex flex-col space-y-2 sm:flex-row sm:items-center sm:space-x-4 sm:space-y-0">
      <div class="text-sm text-muted-foreground">
        Showing <span class="font-medium">{{ meta?.from || 0 }}</span> to <span class="font-medium">{{ meta?.to || 0 }}</span> of <span class="font-medium">{{ meta?.total || 0 }}</span> items
      </div>
      <div class="flex items-center space-x-2">
        <span class="text-sm text-muted-foreground">Items per page:</span>
        <Select v-model="perPage" @update:modelValue="updatePerPage">
          <SelectTrigger class="w-20 h-8">
            <SelectValue :placeholder="perPage" />
          </SelectTrigger>
          <SelectContent>
            <SelectItem v-for="value in allowedPerPageValues" :key="value" :value="value.toString()">
              {{ value }}
            </SelectItem>
          </SelectContent>
        </Select>
      </div>
    </div>

    <div class="flex items-center justify-between sm:justify-end">
      <div v-if="(meta?.last_page || 0) > 1" class="flex items-center space-x-2 mr-4">
        <span class="text-sm text-muted-foreground">Go to page:</span>
        <Input
          type="number"
          class="w-16 h-8"
          min="1"
          :max="meta?.last_page || 1"
          v-model="goToPage"
          @keyup.enter="jumpToPage"
        />
        <Button size="sm" variant="outline" class="h-8" @click="jumpToPage">Go</Button>
      </div>
      <Pagination>
        <PaginationContent>
          <!-- Previous Page Button -->
          <PaginationItem v-if="meta?.current_page > 1">
            <PaginationPrevious @click="jumpToPage(meta?.current_page - 1)" />
          </PaginationItem>
          <PaginationItem v-else>
            <PaginationPrevious class="opacity-50 cursor-not-allowed" />
          </PaginationItem>

          <!-- Page Number Buttons -->
          <template v-if="meta?.last_page <= 7">
            <!-- Show all pages if there are 7 or fewer -->
            <PaginationItem v-for="page in meta?.last_page" :key="page">
              <PaginationLink
                :isActive="page === meta?.current_page"
                @click="goToPage = page; jumpToPage()"
              >
                {{ page }}
              </PaginationLink>
            </PaginationItem>
          </template>
          <template v-else>
            <!-- Show first page -->
            <PaginationItem>
              <PaginationLink
                :isActive="meta?.current_page === 1"
                @click="goToPage = 1; jumpToPage()"
              >
                1
              </PaginationLink>
            </PaginationItem>

            <!-- Show ellipsis if current page is > 3 -->
            <PaginationItem v-if="meta?.current_page > 3">
              <PaginationEllipsis />
            </PaginationItem>

            <!-- Show previous page if current page > 2 -->
            <PaginationItem v-if="meta?.current_page > 2">
              <PaginationLink
                @click="goToPage = meta?.current_page - 1; jumpToPage()"
              >
                {{ meta?.current_page - 1 }}
              </PaginationLink>
            </PaginationItem>

            <!-- Show current page if not first or last -->
            <PaginationItem v-if="meta?.current_page !== 1 && meta?.current_page !== meta?.last_page">
              <PaginationLink
                :isActive="true"
                @click="goToPage = meta?.current_page; jumpToPage()"
              >
                {{ meta?.current_page }}
              </PaginationLink>
            </PaginationItem>

            <!-- Show next page if current page < last_page - 1 -->
            <PaginationItem v-if="meta?.current_page < meta?.last_page - 1">
              <PaginationLink
                @click="goToPage = meta?.current_page + 1; jumpToPage()"
              >
                {{ meta?.current_page + 1 }}
              </PaginationLink>
            </PaginationItem>

            <!-- Show ellipsis if current page < last_page - 2 -->
            <PaginationItem v-if="meta?.current_page < meta?.last_page - 2">
              <PaginationEllipsis />
            </PaginationItem>

            <!-- Show last page -->
            <PaginationItem>
              <PaginationLink
                :isActive="meta?.current_page === meta?.last_page"
                @click="goToPage = meta?.last_page; jumpToPage()"
              >
                {{ meta?.last_page }}
              </PaginationLink>
            </PaginationItem>
          </template>

          <!-- Next Page Button -->
          <PaginationItem v-if="meta?.current_page < meta?.last_page">
            <PaginationNext @click="jumpToPage(meta?.current_page + 1)" />
          </PaginationItem>
          <PaginationItem v-else>
            <PaginationNext class="opacity-50 cursor-not-allowed" />
          </PaginationItem>
        </PaginationContent>
      </Pagination>
    </div>
  </div>
</template>

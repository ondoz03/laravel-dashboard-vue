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
    per_page: perPage.value
  };

  // Navigate with the updated params
  router.get(props.routePath, params, { preserveState: true, replace: true });
}

function jumpToPage() {
  if (!goToPage.value || goToPage.value < 1 || goToPage.value > (props.meta?.last_page || 1)) {
    // Reset to current page if invalid
    goToPage.value = props.meta?.current_page || 1;
    return;
  }

  // Build query params
  const params: Record<string, string> = {
    ...props.queryParams,
    page: goToPage.value.toString(),
    per_page: perPage.value
  };

  // Navigate to the specified page with preserveState and replace options
  router.get(props.routePath, params, { preserveState: true, replace: true });
}

function navigateToPage(url: string) {
  // Parse the URL to extract the path and query parameters
  const urlObj = new URL(url, window.location.origin);
  const path = urlObj.pathname;

  // Extract query parameters from the URL
  const queryParams: Record<string, string> = {};
  urlObj.searchParams.forEach((value, key) => {
    queryParams[key] = value;
  });

  // Navigate to the page with the extracted query parameters
  router.get(path, queryParams, { preserveState: true, replace: true });
}
</script>

<template>
  <div class="flex flex-col space-y-4 sm:flex-row sm:justify-between sm:items-center sm:space-y-0 mt-4 p-4 border rounded-md bg-background">
    <div class="flex flex-col space-y-2 sm:flex-row sm:items-center sm:space-x-4 sm:space-y-0">
      <div class="text-sm text-muted-foreground">
        Showing {{ meta?.from || 0 }} to {{ meta?.to || 0 }} of {{ meta?.total || 0 }} items
      </div>
      <div class="flex items-center space-x-2">
        <span class="text-sm text-muted-foreground">Items per page:</span>
        <Select v-model="perPage" @update:modelValue="updatePerPage">
          <SelectTrigger class="w-20">
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
        <Button size="sm" variant="outline" @click="jumpToPage">Go</Button>
      </div>
      <Pagination>
        <PaginationContent>
          <template v-for="link in meta?.links || []" :key="link.label">
            <PaginationItem v-if="link.url && link.label.includes('Previous')">
              <PaginationPrevious @click="navigateToPage(link.url)" />
            </PaginationItem>
            <PaginationItem v-else-if="link.url && link.label.includes('Next')">
              <PaginationNext @click="navigateToPage(link.url)" />
            </PaginationItem>
            <PaginationItem v-else-if="link.url">
              <PaginationLink
                :isActive="link.active"
                @click="navigateToPage(link.url)"
              >
                {{ link.label }}
              </PaginationLink>
            </PaginationItem>
            <PaginationItem v-else>
              <PaginationEllipsis />
            </PaginationItem>
          </template>
        </PaginationContent>
      </Pagination>
    </div>
  </div>
</template>

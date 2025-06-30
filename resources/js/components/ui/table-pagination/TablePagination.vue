<script setup lang="ts">
import { computed, ref } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import { ChevronLeft, ChevronRight, ChevronsLeft, ChevronsRight } from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';

interface Props {
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
  allowedPerPageValues: number[];
  'route-path': string;
  queryParams: Record<string, any>;
}

const props = withDefaults(defineProps<Props>(), {
  meta: () => ({
    current_page: 0,
    from: 0,
    last_page: 0,
    links: [],
    path: '',
    per_page: 10,
    to: 0,
    total: 0
  }),
  allowedPerPageValues: () => [10, 20, 50, 100],
  queryParams: () => ({})
});

const pageInput = ref<string>((props.meta?.current_page || 1).toString());

// Update page input when current page changes
const currentPage = computed(() => props.meta?.current_page || 1);
const lastPage = computed(() => props.meta?.last_page || 1);

// Build page URL with all current query parameters
function buildPageUrl(page: number, perPage?: number) {
  // Create a new object to avoid modifying the original
  const params = { ...props.queryParams };

  // Always set the page parameter
  params.page = page.toString();

  // Set per_page parameter if provided
  if (perPage) {
    params.per_page = perPage.toString();
  } else if (props.meta?.per_page) {
    // Otherwise use the current per_page from meta
    params.per_page = props.meta.per_page.toString();
  }

  // Build query string
  const queryString = Object.entries(params)
    .filter(([_, value]) => value !== undefined && value !== null && value !== '')
    .map(([key, value]) => {
      if (typeof value === 'object' && value !== null) {
        return `${encodeURIComponent(key)}=${encodeURIComponent(JSON.stringify(value))}`;
      }
      return `${encodeURIComponent(key)}=${encodeURIComponent(value.toString())}`;
    })
    .join('&');

  // Ensure route-path is defined
  const routePath = props['route-path'] || '';
  return `${routePath}?${queryString}`;
}

  // Handle per page change
function handlePerPageChange(value: string) {
  const perPage = parseInt(value, 10);

  // Reset to page 1 when changing items per page
  // Create a new object to avoid modifying the original
  const params = { ...props.queryParams };

  // Always set the page parameter to 1 when changing per_page
  params.page = '1';
  params.per_page = perPage.toString();

  // Use Inertia router to navigate
  router.get(props['route-path'], params, { preserveState: true, preserveScroll: true });
}

// Handle manual page input
function jumpToPage() {
  let page = parseInt(pageInput.value, 10);

  // Validate page number
  if (isNaN(page) || page < 1) {
    page = 1;
  } else if (page > (props.meta?.last_page || 1)) {
    page = props.meta?.last_page || 1;
  }

  // Update the input value to the validated page number
  pageInput.value = page.toString();

  // Create a new object to avoid modifying the original
  const params = { ...props.queryParams };

  // Set the page parameter
  params.page = page.toString();

  // Set per_page parameter if available
  if (props.meta?.per_page) {
    params.per_page = props.meta.per_page.toString();
  }

  // Use Inertia router to navigate
  router.get(props['route-path'], params, { preserveState: true, preserveScroll: true });
}

// Generate page numbers to display
const pageNumbers = computed(() => {
  const pages = [];
  const maxPagesToShow = 5;

  if (lastPage.value <= maxPagesToShow) {
    // If we have 5 or fewer pages, show all
    for (let i = 1; i <= lastPage.value; i++) {
      pages.push(i);
    }
  } else if (currentPage.value <= 3) {
    // If we're near the start, show 1-5
    for (let i = 1; i <= maxPagesToShow; i++) {
      pages.push(i);
    }
  } else if (currentPage.value >= lastPage.value - 2) {
    // If we're near the end, show last 5 pages
    for (let i = lastPage.value - maxPagesToShow + 1; i <= lastPage.value; i++) {
      pages.push(i);
    }
  } else {
    // Otherwise show 2 before and 2 after current page
    for (let i = currentPage.value - 2; i <= currentPage.value + 2; i++) {
      pages.push(i);
    }
  }

  return pages;
});
</script>

<template>
  <div class="flex flex-col sm:flex-row items-center justify-between gap-4 py-2">
    <!-- Top row with pagination info and per page selector -->
    <div class="flex w-full justify-between items-center mb-2 sm:mb-0">
      <!-- Pagination info -->
      <div class="text-sm text-muted-foreground">
        Showing {{ meta?.from || 0 }} to {{ meta?.to || 0 }} of {{ meta?.total || 0 }} entries (Page {{ meta?.current_page || 1 }} of {{ meta?.last_page || 1 }})
      </div>

      <!-- Per page selector aligned with pagination info -->
      <div class="flex items-center gap-2">
        <span class="text-sm text-muted-foreground">Per page:</span>
        <Select
            :model-value="(meta?.per_page || 10).toString()"
            @update:model-value="handlePerPageChange"
        >
            <SelectTrigger class="h-8 w-[70px]">
                <SelectValue :placeholder="(meta?.per_page || 10).toString()" />
            </SelectTrigger>
            <SelectContent>
                <SelectItem
                    v-for="value in props.allowedPerPageValues"
                    :key="value"
                    :value="value.toString()"
                >
                    {{ value }}
                </SelectItem>
            </SelectContent>
        </Select>
      </div>
    </div>

    <div class="flex flex-wrap items-center justify-between gap-4 w-full sm:w-auto">
      <!-- Pagination controls -->
      <div class="flex flex-wrap items-center gap-2">

        <div class="flex items-center gap-1">
          <!-- Previous Page Button -->
          <Link
            :href="buildPageUrl(1)"
            class="inline-flex h-8 w-8 items-center justify-center rounded-md border border-input bg-background p-0 text-sm font-medium ring-offset-background transition-colors hover:bg-accent hover:text-accent-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50"
            :class="{ 'opacity-50 pointer-events-none': currentPage === 1 }"
          >
            <span class="sr-only">First Page</span>
            <ChevronsLeft class="h-4 w-4" />
          </Link>

          <!-- Page Number Buttons -->
          <Link
            :href="buildPageUrl(currentPage - 1)"
            class="inline-flex h-8 w-8 items-center justify-center rounded-md border border-input bg-background p-0 text-sm font-medium ring-offset-background transition-colors hover:bg-accent hover:text-accent-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50"
            :class="{ 'opacity-50 pointer-events-none': currentPage === 1 }"
          >
            <span class="sr-only">Previous Page</span>
            <ChevronLeft class="h-4 w-4" />
          </Link>

          <Link
            v-for="page in pageNumbers"
            :key="page"
            :href="buildPageUrl(page)"
            class="inline-flex h-8 w-8 items-center justify-center rounded-md border text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50"
            :class="page === currentPage
              ? 'border-primary bg-primary text-primary-foreground hover:bg-primary/90'
              : 'border-input bg-background hover:bg-accent hover:text-accent-foreground'"
          >
            {{ page }}
          </Link>

          <!-- Next Page Button -->
          <Link
            :href="buildPageUrl(currentPage + 1)"
            class="inline-flex h-8 w-8 items-center justify-center rounded-md border border-input bg-background p-0 text-sm font-medium ring-offset-background transition-colors hover:bg-accent hover:text-accent-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50"
            :class="{ 'opacity-50 pointer-events-none': currentPage === lastPage }"
          >
            <span class="sr-only">Next Page</span>
            <ChevronRight class="h-4 w-4" />
          </Link>

          <Link
            :href="buildPageUrl(lastPage)"
            class="inline-flex h-8 w-8 items-center justify-center rounded-md border border-input bg-background p-0 text-sm font-medium ring-offset-background transition-colors hover:bg-accent hover:text-accent-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50"
            :class="{ 'opacity-50 pointer-events-none': currentPage === lastPage }"
          >
            <span class="sr-only">Last Page</span>
            <ChevronsRight class="h-4 w-4" />
          </Link>
        </div>
      </div>
    </div>
  </div>
</template>

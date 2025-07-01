<script setup>
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader, SidebarMenu, SidebarMenuButton, SidebarMenuItem, SidebarGroup, SidebarGroupLabel, SidebarGroupContent } from '@/components/ui/sidebar';
import { Collapsible, CollapsibleContent, CollapsibleTrigger } from '@/components/ui/collapsible';
import { Link, usePage } from '@inertiajs/vue3';
import { BookOpen, Folder, LayoutGrid, Package, Users, Shield, Key, ChevronRight } from 'lucide-vue-next';
import AppLogo from './AppLogo.vue';
import { computed, ref } from 'vue';

// Get user permissions from shared data
const page = usePage();
const userPermissions = computed(() => page.props.auth.permissions || []);
const isUserManagementOpen = ref(false);

// Define all navigation items with their required permissions
const allNavItems = [
    {
        title: 'Dashboard',
        href: '/dashboard',
        icon: LayoutGrid,
        // Dashboard is accessible to all authenticated users
    },
    {
        title: 'Master Items',
        href: '/master-items',
        icon: Package,
        permission: 'view master items',
    },
    {
        title: 'Users',
        href: '/users',
        icon: Users,
        permission: 'view users',
    },
];

// Filter navigation items based on user permissions
const mainNavItems = computed(() => {
    return allNavItems.filter(item => {
        // If no permission is required, show the item
        if (!item.permission) return true;

        // Otherwise, check if the user has the required permission
        return userPermissions.value.includes(item.permission);
    });
});

const footerNavItems = [
    {
        title: 'Github Repo',
        href: 'https://github.com/laravel/vue-starter-kit',
        icon: Folder,
    },
    {
        title: 'Documentation',
        href: 'https://laravel.com/docs/starter-kits',
        icon: BookOpen,
    },
];
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link href="/dashboard">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="mainNavItems" />

            <!-- User Management Group (Collapsible) -->
            <SidebarGroup v-if="userPermissions.includes('view roles') || userPermissions.includes('view permissions')" class="px-2 py-0">
                <Collapsible :open="isUserManagementOpen" @update:open="isUserManagementOpen = $event">
                    <CollapsibleTrigger asChild>
                        <div class="flex cursor-pointer items-center justify-between rounded-md p-2 hover:bg-sidebar-accent/50">
                            <div class="flex items-center gap-2">
                                <Shield class="h-4 w-4" />
                                <span>User Management</span>
                            </div>
                            <ChevronRight
                                class="h-4 w-4 transition-transform duration-200"
                                :class="{ 'rotate-90': isUserManagementOpen }"
                            />
                        </div>
                    </CollapsibleTrigger>
                    <CollapsibleContent>
                        <SidebarMenu class="ml-2 mt-1">
                            <SidebarMenuItem v-if="userPermissions.includes('view roles')">
                                <SidebarMenuButton as-child :is-active="page.url.startsWith('/roles')">
                                    <Link href="/roles">
                                        <Shield class="h-4 w-4" />
                                        <span>Roles</span>
                                    </Link>
                                </SidebarMenuButton>
                            </SidebarMenuItem>
                            <SidebarMenuItem v-if="userPermissions.includes('view permissions')">
                                <SidebarMenuButton as-child :is-active="page.url.startsWith('/permissions')">
                                    <Link href="/permissions">
                                        <Key class="h-4 w-4" />
                                        <span>Permissions</span>
                                    </Link>
                                </SidebarMenuButton>
                            </SidebarMenuItem>
                        </SidebarMenu>
                    </CollapsibleContent>
                </Collapsible>
            </SidebarGroup>
        </SidebarContent>

        <SidebarFooter>
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>

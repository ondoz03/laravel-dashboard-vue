<script setup lang="ts">
import { Head } from '@inertiajs/vue3';

import AppearanceTabs from '../../components/AppearanceTabs.vue';
import HeadingSmall from '../../components/HeadingSmall.vue';
import ThemeSelector from '../../components/ThemeSelector.vue';
import { type BreadcrumbItem } from '../../types';

import AppLayout from '../../layouts/AppLayout.vue';
import SettingsLayout from '../../layouts/settings/Layout.vue';

const breadcrumbItems: BreadcrumbItem[] = [
    {
        title: 'Appearance settings',
        href: '/settings/appearance',
    },
];
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbItems">
        <Head title="Appearance settings" />

        <SettingsLayout>
            <div class="space-y-6">
                <HeadingSmall title="Appearance settings" description="Update your account's appearance settings" />

                <!-- Theme Selector Component -->
                <ThemeSelector />

                <!-- Original Appearance Tabs -->
                <div class="mt-6">
                    <h3 class="mb-2 text-sm font-medium">Quick Theme Toggle</h3>
                    <AppearanceTabs />
                </div>
            </div>
        </SettingsLayout>
    </AppLayout>
</template>

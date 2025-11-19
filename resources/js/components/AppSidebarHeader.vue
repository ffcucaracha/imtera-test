<script setup lang="ts">
import Breadcrumbs from '@/components/Breadcrumbs.vue';
import { SidebarTrigger } from '@/components/ui/sidebar';
import type { BreadcrumbItemType } from '@/types';
import { logout } from '@/routes';
import { Link, router } from '@inertiajs/vue3';
import { LogOut } from 'lucide-vue-next';

withDefaults(
    defineProps<{
        breadcrumbs?: BreadcrumbItemType[];
    }>(),
    {
        breadcrumbs: () => [],
    },
);
const handleLogout = () => {
    logout();
    router.flushAll();
};
</script>

<template>
    <header
        class="flex justify-between h-16 shrink-0 items-center gap-2 border-b border-sidebar-border/70 px-6 transition-[width,height] ease-linear group-has-data-[collapsible=icon]/sidebar-wrapper:h-12 md:px-4">
        <div class="flex items-center gap-2">
            <SidebarTrigger class="-ml-1" />
            <template v-if="breadcrumbs && breadcrumbs.length > 0">
                <Breadcrumbs :breadcrumbs="breadcrumbs" />
            </template>
        </div>
        <div class="flex justify-end">
            <Link class="block p-2 rounded w-full hover:bg-accent hover:text-accent-foreground dark:hover:bg-accent/50" :href="logout()" @click="handleLogout" as="button" >
                <LogOut class="h-4 w-4" />
            </Link>
            
        </div>
    </header>
</template>

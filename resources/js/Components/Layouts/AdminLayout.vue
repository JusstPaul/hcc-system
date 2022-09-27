<template>
    <n-layout has-sider style="height: 100%;">
        <n-layout-sider bordered collapse-mode="width" :collapsed-width="64" :width="240" :collapsed="collapsed"
            show-trigger @collapse="collapsed = true" @expand="collapsed = false">
            <n-layout style="height: 100%;">
                <n-layout-content>
                    <n-menu :value="currentRouteKey()" :options="routes" style="padding-top: 24px;"
                        :collapsed="collapsed" :collapsed-width="64" :collapsed-icon-size="22" />
                </n-layout-content>
                <n-layout-footer position="absolute" bordered style="padding: 24px;">
                    <n-button v-if="!collapsed" @click="logout">Logout</n-button>
                </n-layout-footer>
            </n-layout>
        </n-layout-sider>
        <n-layout>
            <n-layout-content content-style="padding: 24px;">
                <slot />
            </n-layout-content>
        </n-layout>
    </n-layout>
</template>

<script>
import { h, ref } from 'vue'
import { Link } from '@inertiajs/inertia-vue3'
import { NLayoutContent, NLayout, NLayoutSider, NLayoutFooter, NMenu, NButton } from 'naive-ui'
import { logout } from '@/utils'
import Layout from './BaseLayout.vue'

const routes = [
    {
        label: () => h(Link, {
            href: route('admin.index'),
        }, {
            default: () => 'Users'
        }),
        key: 'admin-index'
    },
    {
        label: () => h(Link, {
            href: route('admin.classrooms'),
        }, {
            default: () => 'Classrooms'
        }),
        key: 'admin-classrooms'
    },
    {
        label: () => h(Link, {
            href: route('admin.profile'),
        }, {
            default: () => 'Profile'
        }),
        key: 'admin-profile'
    }
]

export default {
    layout: Layout,
    components: {
        NLayoutContent,
        NMenu,
        NLayout,
        NLayoutSider,
        NLayoutFooter,
        NButton,
    },
    setup() {
        function currentRouteKey() {
            switch (route().current()) {
                case 'admin.index':
                case 'admin.create_user':
                    return 'admin-index'
                case 'admin.classrooms':
                case 'admin.create_classroom':
                    return 'admin-classrooms'
                case 'admin.profile':
                    return 'admin-profile'
                default:
                    alert('AdminLayout: Invalid route')
                    return ''
            }
        }

        return {
            logout,
            routes,
            currentRouteKey,
            collapsed: ref(false),
        }
    }
}
</script>

<style scoped>
.content {
    padding: 1rem;
}
</style>


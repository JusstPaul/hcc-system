<template>
    <n-layout has-sider style="height: 100%;">
        <n-layout-sider bordered collapse-mode="width" :collapsed-width="64" :width="240" :collapsed="collapsed"
            show-trigger @collapse="collapsed = true" @expand="collapsed = false">
            <n-layout-content>
                <n-menu style="padding-top: 24px;" :collapsed="collapsed" :collapsed-width="64"
                    :collapsed-icon-size="22" />
            </n-layout-content>
            <n-layout-footer position="absolute" bordered style="padding: 24px;">
                <n-button v-if="!collapsed" @click="logout">Logout</n-button>
            </n-layout-footer>
        </n-layout-sider>
        <n-layout>
            <n-layout-content content-style="padding: 24px;">
                <slot />
            </n-layout-content>
        </n-layout>
    </n-layout>
</template>

<script>
import { ref } from 'vue'
import { NLayoutContent, NLayout, NLayoutSider, NLayoutFooter, NMenu, NButton, } from 'naive-ui'
import { logout } from '@/utils'
import Layout from './BaseLayout.vue'

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
        return {
            collapsed: ref(false),
            logout,
        }
    }
}
</script>

<style scoped>
.content {
    padding: 1rem;
}
</style>


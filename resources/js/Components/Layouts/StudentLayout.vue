<template>
    <n-layout has-sider style="height: 100%;">
        <n-layout-sider bordered collapse-mode="width" :collapsed-width="64" :width="240" :collapsed="collapsed"
            show-trigger @collapse="collapsed = true" @expand="collapsed = false">
            <n-layout-content>
                <n-menu style="padding-top: 24px;" :collapsed="collapsed" :collapsed-width="64"
                    :collapsed-icon-size="22" />
            </n-layout-content>
            <n-layout-footer position="absolute" bordered style="padding: 24px;">
                <i-link :href="route('post.logout')" method="post" as="button"
                    class="van-button van-button--default van-button--normal">Logout
                </i-link>
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
import { NLayoutContent, NLayout, NLayoutSider, NLayoutFooter, NMenu } from 'naive-ui'
import Layout from './BaseLayout.vue'

export default {
    layout: Layout,
    components: {
        NLayoutContent,
        NMenu,
        NLayout,
        NLayoutSider,
        NLayoutFooter,
    },
    setup() {
        return {
            collapsed: ref(false)
        }
    }
}
</script>

<style scoped>
.content {
    padding: 1rem;
}
</style>


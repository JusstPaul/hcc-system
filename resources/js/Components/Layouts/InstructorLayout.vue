<template>
    <n-layout has-sider style="height: 100%;">
        <n-layout-sider bordered collapse-mode="width" :collapsed-width="64" :width="240" :collapsed="collapsed"
            show-trigger @collapse="collapsed = true" @expand="collapsed = false">
            <n-layout-content>
                <n-menu :value="currentRouteKey()" :options="routes()" style="padding-top: 24px;" :collapsed="collapsed"
                    :collapsed-width="64" :collapsed-icon-size="22" />
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
import { h, ref } from 'vue'
import { NLayoutContent, NLayout, NLayoutSider, NLayoutFooter, NMenu } from 'naive-ui'
import { Link } from '@inertiajs/inertia-vue3'
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
        function currentRouteKey() {
            switch (route().current()) {
                case 'instructor.index':
                    return 'instructor-index'
                case 'instructor.classroom':
                    return 'instructor-classroom'
                case 'instructor.create_activity':
                    return 'instructor-create_activity'
                default:
                    alert('InstructorLayout: Invalid route')
                    return ''
            }
        }

        function routes() {
            let defaultRoutes = [
                {
                    label: () => h(Link, {
                        href: route('instructor.index'),
                    }, {
                        default: () => 'Classrooms',
                    }),
                    key: 'instructor-index',
                },
            ]

            switch (route().current()) {
                case 'instructor.index':
                    return defaultRoutes;
                case 'instructor.classroom':
                    defaultRoutes.push({
                        label: () => h(Link, {
                            href: route('instructor.create_activity', {
                                classroom_id: route().params.classroom_id,
                            })
                        }, {
                            default: () => 'Create Activity'
                        }),
                        key: 'classroom-create_activity',
                    })

                case 'instructor.create_activity':
                    return defaultRoutes;
                default:
                    alert('InstructorLayout: Invalid route')
                    return defaultRoutes;
            }
        }

        return {
            collapsed: ref(false),
            routes,
            currentRouteKey,
        }
    }
}
</script>

<style scoped>
.content {
    padding: 1rem;
}
</style>

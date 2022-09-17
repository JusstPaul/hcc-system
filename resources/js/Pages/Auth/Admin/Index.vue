<template>
    <n-layout>
        <n-layout-header>
            <div :style="{
                display: 'flex',
                gap: '1rem'
            }">
                <n-h2>Users</n-h2>
                <n-button type="primary" :tag="createUserLink">
                    <template #icon>
                        <n-icon>
                            <user-plus-icon />
                        </n-icon>
                    </template>
                    Add
                </n-button>
            </div>
        </n-layout-header>
        <n-layout-content content-style="padding: 0 24px 0 24px;">
            <n-data-table :single-line="false" :border="false" :columns="userTableColumns" />
            {{ users }}
        </n-layout-content>
    </n-layout>
</template>

<script>
import { h } from 'vue'
import {
    NLayout,
    NLayoutContent,
    NLayoutHeader,
    NH2,
    NButton,
    NIcon,
    NDataTable,
} from 'naive-ui'
import { UserPlus } from '@vicons/tabler'
import { Link } from '@inertiajs/inertia-vue3'
import Layout from '@/Components/Layouts/AdminLayout.vue'

export default {
    layout: Layout,
    components: {
        NLayout,
        NLayoutContent,
        NLayoutHeader,
        NH2,
        NButton,
        NIcon,
        NDataTable,
        UserPlusIcon: UserPlus,
    },
    props: {
        users: Object,
    },
    setup(props) {
        const userTableColumns = [
            {
                title: 'Username',
                key: 'username'
            },
            {
                title: 'Name',
                key: 'name'
            },
            {
                title: 'Role',
                key: 'role'
            },
            {
                title: 'Action',
                key: 'action'
            }
        ];

        const createUserLink = h(Link, { href: route('admin.create_user') })

        return {
            userTableColumns,
            createUserLink
        }
    }
}
</script>

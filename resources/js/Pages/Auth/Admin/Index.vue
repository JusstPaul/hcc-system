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
            <n-data-table :single-line="false" :bordered="false" :columns="userTableColumns" :data="userTableData" />
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
import { formatName } from '@/utils'
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
        users: Array,
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
        const userTableData = props.users.map((val) => {
            const { username, role_name } = val
            const { l_name, m_name, f_name } = val.profile
            const role = role_name.charAt(0).toUpperCase() + role_name.slice(1)

            return {
                username,
                name: formatName(l_name, m_name, f_name),
                role,
                action: 'None'
            }
        })

        const createUserLink = h(Link, { href: route('admin.create_user') })

        return {
            userTableColumns,
            userTableData,
            createUserLink,
        }
    }
}
</script>

<template>
    <n-layout>
        <n-layout-header>
            <n-page-header title="Students" @back="backLink()" style="overflow: hidden;" />
        </n-layout-header>
        <n-layout-content content-style="padding: 24px;">
            <n-data-table :single-line="false" :bordered="false" :columns="studentsTableColumns"
                :data="studentsTableData" />
        </n-layout-content>
    </n-layout>
</template>

<script>
import { Inertia } from '@inertiajs/inertia'
import {
    NLayout,
    NLayoutContent,
    NLayoutHeader,
    NPageHeader,
    NDataTable,
} from 'naive-ui'
import { formatName } from '@/utils'
import Layout from '@/Components/Layouts/InstructorLayout.vue'

export default {
    layout: Layout,
    components: {
        NLayout,
        NLayoutContent,
        NPageHeader,
        NLayoutHeader,
        NDataTable,
    },
    props: {
        classroom_id: String,
        students: Array,
    },
    setup({ classroom_id, students }) {
        const studentsTableColumns = [
            {
                title: 'ID',
                key: 'id',
            },
            {
                title: 'Name',
                key: 'name',
            }
        ]
        const studentsTableData = students.map((val) => {
            const { username } = val
            const { l_name, m_name, f_name } = val.profile

            return {
                id: username,
                name: formatName(l_name, m_name, f_name),
            }
        })

        function backLink() {
            Inertia.get(route('instructor.classroom', {
                classroom_id,
            }))
        }

        return {
            studentsTableColumns,
            studentsTableData,
            backLink,
        }
    }
}
</script>

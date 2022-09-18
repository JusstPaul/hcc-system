<template>
    <n-layout>
        <n-layout-header>
            <n-page-header title="Create Classroom" @back="() => backLink()" style="overflow: hidden;" />
        </n-layout-header>
        <n-layout-content content-style="padding: 24px;">
            <n-form :model="classroomForm" label-placement="left" require-mark-placement="right-hanging"
                label-width="120" style="max-width: 400px;">
                <n-form-item label="Instructor" path="instructor" required>
                    <n-select v-model:value="classroomForm.instructor" :options="instructorSelect" />
                </n-form-item>
                <n-form-item label-placement="top" label="Select students">
                    <n-data-table :columns="studentsTableColumns" :data="studentsData" :row-key="studentSelectRowKey"
                        @update:checked-row-keys="handleStudentSelect" />
                </n-form-item>
            </n-form>
        </n-layout-content>
    </n-layout>
</template>

<script>
import { Inertia } from '@inertiajs/inertia'
import {
    NLayout,
    NLayoutContent,
    NLayoutHeader,
    NH2,
    NButton,
    NPageHeader,
    NForm,
    NFormItem,
    NInput,
    NInputNumber,
    NSelect,
    NDataTable,
} from 'naive-ui'
import { useForm } from '@inertiajs/inertia-vue3'
import Layout from '@/Components/Layouts/AdminLayout.vue'
import { formatName } from '@/utils'

export default {
    layout: Layout,
    components: {
        NLayout,
        NLayoutContent,
        NLayoutHeader,
        NH2,
        NButton,
        NPageHeader,
        NForm,
        NFormItem,
        NInput,
        NInputNumber,
        NSelect,
        NDataTable,
    },
    props: {
        school_year: Object,
        instructors: Array,
        students: Array,
    },
    setup(props) {
        const classroomForm = useForm({
            instructor: '',
        })
        const instructorSelect = props.instructors.map((val) => {
            const { _id } = val
            const { l_name, m_name, f_name } = val.profile

            return {
                label: formatName(l_name, m_name, f_name),
                value: _id,
            }
        })
        const studentsTableColumns = [
            {
                type: 'selection',
            },
            {
                title: 'Student ID',
                key: 'username',
            },
            {
                title: 'Name',
                key: 'name',
            },
        ]
        const studentsData = props.students.map((val) => {
            const { username } = val
            const { l_name, m_name, f_name } = val.profile

            return {
                username,
                name: formatName(l_name, m_name, f_name),
            }
        })

        function studentSelectRowKey(row) {
            return row.address
        }

        function handleStudentSelect(rowKeys) {
            console.log(rowKeys)
        }


        function backLink() {
            Inertia.get(route('admin.classrooms'))
        }

        return {
            backLink,
            handleStudentSelect,
            studentSelectRowKey,
            classroomForm,
            instructorSelect,
            studentsTableColumns,
            studentsData,
        }
    }
}
</script>



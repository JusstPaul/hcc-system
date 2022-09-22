<template>
    <n-layout>
        <n-layout-header>
            <n-page-header title="Create Classroom" :subtitle="`School Year: ${showCurrentSchoolYear}`"
                @back="() => backLink()" style="overflow: hidden;" />
        </n-layout-header>
        <n-layout-content content-style="padding: 24px;">

            <!-- Format the time before submitting for validation. -->
            <n-form @submit.prevent="() => classroomForm.transform((data) => ({
                ...data,
                timeStart: formatTime(data.timeStart),
                timeEnd: formatTime(data.timeEnd),
            })).post(route('post.admin.create_classroom'))" :model="classroomForm" label-placement="left"
                require-mark-placement="right-hanging" label-width="120" style="max-width: 400px;">
                <n-form-item label="Section" path="section" required>
                    <n-input v-model:value="classroomForm.section" />
                </n-form-item>
                <n-form-item label="Day" path="day" required>
                    <n-select v-model:value="classroomForm.day" :options="daySelect" />
                </n-form-item>
                <n-form-item label="Room" path="room" required>
                    <n-input v-model:value="classroomForm.room" />
                </n-form-item>
                <n-form-item label="Time Start" path="timeStart" required>
                    <n-time-picker v-model:value="classroomForm.timeStart" format="h:mm a" />
                </n-form-item>
                <n-form-item label="Time End" path="timeEnd" required>
                    <n-time-picker v-model:value="classroomForm.timeEnd" format="h:mm a" />
                </n-form-item>
                <n-form-item label="Instructor" path="instructor" required>
                    <n-select v-model:value="classroomForm.instructor" :options="instructorSelect" />
                </n-form-item>
                <n-form-item label-placement="top" label="Select students" required>
                    <n-data-table :columns="studentsTableColumns" :data="studentsData" :rowKey="studentsRowKey"
                        @update:checked-row-keys="selectStudent" />
                </n-form-item>
                <n-form-item>
                    <n-button type="primary" attr-type="submit" :loading="classroomForm.processing" :style="{
                        marginRight: 0,
                        marginLeft: 'auto'
                    }">Submit</n-button>
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
    NTimePicker,
} from 'naive-ui'
import { useForm } from '@inertiajs/inertia-vue3'
import { formatName, formatSchoolYear, formatTime } from '@/utils'
import Layout from '@/Components/Layouts/AdminLayout.vue'

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
        NTimePicker,
    },
    props: {
        school_year: Object,
        instructors: Array,
        students: Array,
    },
    setup(props) {
        const showCurrentSchoolYear = formatSchoolYear(props.school_year)

        const classroomForm = useForm({
            section: '',
            day: '',
            room: '',
            timeStart: null,
            timeEnd: null,
            instructor: '',
            students: []
        })

        const daySelect = [
            {
                label: 'MWF',
                value: 'mwf',
            },
            {
                label: 'TTh',
                value: 'tth',
            },
            {
                label: 'Sat',
                value: 'sat',
            },
        ]

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
                key: '_id'
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
            const { username, _id } = val
            const { l_name, m_name, f_name } = val.profile

            return {
                username,
                name: formatName(l_name, m_name, f_name),
                _id
            }
        })
        function studentsRowKey(row) {
            return row._id
        }
        function selectStudent(rowKeys) {
            classroomForm.students = rowKeys
        }

        function backLink() {
            Inertia.get(route('admin.classrooms'))
        }

        return {
            formatTime,
            backLink,
            studentsRowKey,
            selectStudent,
            showCurrentSchoolYear,
            classroomForm,
            daySelect,
            instructorSelect,
            studentsTableColumns,
            studentsData,
        }
    }
}
</script>



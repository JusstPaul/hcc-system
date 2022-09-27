<template>
    <n-layout>
        <n-layout-header>
            <n-page-header :title="`School Year: ${showCurrentSchoolYear}`">
                <template #subtitle>
                    <n-popconfirm @positive-click="generateSchoolYear()">
                        <template #activator>
                            <n-button type="warning">Generate New</n-button>
                        </template>
                        Generate new School Year? {{ previewSchoolYear() }}
                    </n-popconfirm>
                </template>
                <n-space>
                    <n-button :disabled="!has_instructors" type="primary" @click.prevent="visitCreateClassroom()">New
                        Classroom</n-button>
                    <n-tag v-if="!has_instructors" :bordered="false">No Instructors registered</n-tag>
                </n-space>
            </n-page-header>
            <n-layout-content content-style="padding: 24px;">
                <n-data-table :single-line="false" :bordered="false" :columns="classroomTableColumns"
                    :data="classroomData" />
            </n-layout-content>
        </n-layout-header>
    </n-layout>
</template>

<script>
import dayjs from 'dayjs'
import { Inertia } from '@inertiajs/inertia'
import {
    NLayout,
    NLayoutContent,
    NLayoutHeader,
    NH3,
    NButton,
    NIcon,
    NDataTable,
    NPageHeader,
    NPopconfirm,
    NTag,
    NSpace,
} from 'naive-ui'
import { formatSchoolYear } from '@/utils'
import Layout from '@/Components/Layouts/AdminLayout.vue'

export default {
    layout: Layout,
    components: {
        NLayout,
        NLayoutContent,
        NLayoutHeader,
        NH3,
        NButton,
        NIcon,
        NDataTable,
        NPageHeader,
        NPopconfirm,
        NTag,
        NSpace,
    },
    props: {
        school_year: Object,
        classrooms: Array,
        has_instructors: Boolean,
    },
    setup({ classrooms, school_year }) {

        const classroomTableColumns = [
            {
                title: 'Adviser',
                key: 'adviser',
            },
            {
                title: 'Room',
                key: 'room',
            },
            {
                title: 'Time',
                key: 'time',
            },
            {
                title: 'Days',
                key: 'days'
            },
            {
                title: 'Action',
                key: 'action'
            }
        ]
        const classroomData = classrooms.map((val) => ({
            'adviser': 'TODO',
            'room': val.room,
            'time': val.time_start,
            'days': val.day,
            'action': 'TODO',
        }))


        const showCurrentSchoolYear = formatSchoolYear(school_year)

        function previewSchoolYear() {
            return `${dayjs().year()} to ${dayjs().add(1, 'y').year()}`;
        }

        function generateSchoolYear() {
            Inertia.post(route('post.admin.school_year'), undefined, {
                onSuccess: () => location.reload(),
            })
        }

        function visitCreateClassroom() {
            Inertia.get(route('admin.create_classroom'));
        }

        return {
            classroomTableColumns,
            classroomData,
            showCurrentSchoolYear,
            previewSchoolYear,
            generateSchoolYear,
            visitCreateClassroom,
        }
    }
}
</script>


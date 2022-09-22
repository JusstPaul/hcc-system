<template>
    <n-grid x-gap="12" y-gap="24" cols="1 s:2 l:4" responsive="screen">
        <n-grid-item v-for="classroom in classrooms">
            <n-card size="small">
                <template #header>
                    <n-h4 @click="visitClassroom(classroom._id)" style="margin-bottom: 0;">Section</n-h4>
                </template>
                <div style="text-transform: uppercase;">
                    {{ classroom.time_start }} to {{ classroom.time_end }} {{ classroom.day }}
                </div>
            </n-card>
        </n-grid-item>
    </n-grid>
</template>

<script>
import { Inertia } from '@inertiajs/inertia';
import {
    NGrid,
    NGridItem,
    NCard,
    NH4,
} from 'naive-ui';
import Layout from '@/Components/Layouts/InstructorLayout.vue'

export default {
    layout: Layout,
    components: {
        NGrid,
        NGridItem,
        NCard,
        NH4,
    },
    props: {
        classrooms: Array,
    },
    setup() {
        function visitClassroom(id) {
            Inertia.get(route('instructor.classroom', {
                classroom_id: id
            }))
        }

        return {
            visitClassroom,
        }
    }
}
</script>

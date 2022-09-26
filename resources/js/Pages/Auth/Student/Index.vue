<template>
    <n-space vertical>
        <n-card v-for="(activity, _index) in activities" :key="activity._id.$oid">
            <template #header>
                <n-button @click="visitActivity(activity._id.$oid)" text class="underline-hover">
                    <n-h4 style="margin-bottom: 0;">{{ activity.title }}</n-h4>
                </n-button>
            </template>
        </n-card>
    </n-space>
</template>

<script>
import { Inertia } from '@inertiajs/inertia'
import {
    NSpace,
    NCard,
    NEllipsis,
    NH4,
    NButton,
} from 'naive-ui'
import Layout from '@/Components/Layouts/StudentLayout.vue'

export default {
    layout: Layout,
    components: {
        NSpace,
        NCard,
        NEllipsis,
        NH4,
        NButton,
    },
    props: {
        student_id: String,
        activities: Array,
    }, setup(props) {
        const { student_id } = props

        function visitActivity(_id) {
            Inertia.get(route('student.activity', {
                student_id,
                activity_id: _id,
            }))
        }

        return {
            visitActivity,
        }
    }
}
</script>


<template>
    <template v-if="joined_class">
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
    <template v-else>
        <n-empty size="huge" style="margin: 4rem;">
            <n-space vertical align="center" :size="0">
                You haven't joined a classroom yet.
                <br />
                Please contact your instructor for notification.
            </n-space>
        </n-empty>
    </template>
</template>

<script>
import { Inertia } from '@inertiajs/inertia'
import {
    NSpace,
    NCard,
    NEllipsis,
    NH4,
    NButton,
    NEmpty,
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
        NEmpty,
    },
    props: {
        student_id: String,
        joined_class: Boolean,
        activities: Array,
    }, setup({ student_id }) {

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


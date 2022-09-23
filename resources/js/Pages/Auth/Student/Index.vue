<template>
    <n-space vertical>
        <n-card v-for="(activity, index) in activities" :key="activity._id.$oid" :title="activity.title"></n-card>
    </n-space>
    {{ activities }}
</template>

<script>
import { NSpace, NCard } from 'naive-ui'
import Layout from '@/Components/Layouts/StudentLayout.vue'

export default {
    layout: Layout,
    components: {
        NSpace,
        NCard,
    },
    props: {
        student_id: String,
        activities: Array,
    }
}
</script>


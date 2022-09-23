<template>

</template>

<script>
import { useForm } from '@inertiajs/inertia-vue3';
import {
    NSpace,
    NCard,
    NProgress,
    NImage,
    NImageGroup,
    NGrid,
    NGridItem,
} from 'naive-ui'
import Layout from '@/Components/Layouts/StudentLayout.vue'

export default {
    layout: Layout,
    components: {
        NSpace,
        NCard,
        NProgress,
        NImage,
        NImageGroup,
        NGrid,
        NGridItem,
    },
    props: {
        student_id: String,
        activity: Object,
    },
    setup(props) {
        const { activity } = props
        console.log(activity.questions)

        const answerForm = useForm({
            'answers': activity.questions.map(() => null)
        })

        return {
            answerForm,
        }
    }
}
</script>

<style scoped>
.comparator-container {
    height: fit-content;
    position: relative;
}

.comparator-top,
.comparator-bottom {
    position: absolute;
    top: 0;
    left: 0;
}
</style>

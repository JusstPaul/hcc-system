<template>
    <n-form :model="answerForm" label-placement="left" require-mark-placement="right-hanging" label-width="120"
        style="max-width: 1080px;">
        <n-form-item v-for="({ id, value, ...props }, index) in answerForm.answers" :key="id"
            :path="`answerForm.answers[${index}]`">
            <n-card>
                <!-- Handwriting Comparator -->
                <template v-if="matchQuestion(index, 6)">
                    <n-layout>
                        <n-layout-content>
                            <n-image-group>
                                <n-space>
                                    <n-image :src="props.files[0]" />
                                </n-space>
                            </n-image-group>
                        </n-layout-content>
                    </n-layout>
                </template>
            </n-card>
        </n-form-item>
    </n-form>
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
    NForm,
    NFormItem,
    NInput,
    NLayout,
    NLayoutContent,
} from 'naive-ui'
import Layout from '@/Components/Layouts/StudentLayout.vue'
import { QUESTION_TYPES } from '@/constants';

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
        NForm,
        NFormItem,
        NInput,
        NLayout,
        NLayoutContent,
    },
    props: {
        student_id: String,
        activity: Object,
    },
    setup(props) {
        const { activity } = props

        const answerForm = useForm({
            'answers': activity.questions.map((val) => {
                if (val.type === QUESTION_TYPES[5]) {
                    return {
                        id: val.id,
                        value: [],
                        files: val.value,
                        progress: {
                            current: 1,
                            total: val.value.length, // NOTE: Needs further testing
                        },
                    }
                }

                return {
                    id: val.id,
                    value: null,
                }
            })
        })

        function matchQuestion(questionIndex, typeIndex) {
            return activity.questions[questionIndex].type === QUESTION_TYPES[typeIndex]
        }

        return {
            answerForm,
            matchQuestion,
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

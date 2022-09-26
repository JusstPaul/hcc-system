<template>
    <n-form :model="answerForm" label-placement="left" require-mark-placement="right-hanging" label-width="120"
        style="max-width: 1080px;">

        <n-alert title="Instructions">
            {{ generalDirection }}
        </n-alert>

        <template v-for="(val, index) in answerForm.answers" :key="index">
            <n-form-item v-for="({ id, value, ...props }, idx) in val" :key="id"
                :path="`answerForm.answers[${index}][${idx}]`" style="margin-top: 2rem;">
                <n-card>
                    {{ questions[index][idx].instruction }}

                    <n-layout-content content-style="margin-top: 1rem; padding-left: 12px; padding-right: 12px;">
                        <!-- Question -->
                        <template v-if="matchQuestion(index, idx, 0)">
                            <n-input v-model:value="answerForm.answers[index][idx].value" />
                        </template>

                        <!-- True or False -->
                        <template v-if="matchQuestion(index, idx, 1)">
                            <n-radio-group v-model:value="answerForm.answers[index][idx].value"
                                :name="`activity-${id}`">
                                <n-space>
                                    <n-radio label="True" value="true" />
                                    <n-radio label="False" value="false" />
                                </n-space>
                            </n-radio-group>
                        </template>

                        <!-- Multiple Choice -->
                        <template v-if="matchQuestion(index, idx, 2)">
                            <n-radio-group v-model:value="answerForm.answers[index][idx].value"
                                :name="`activity-${id}`">
                                <n-space>
                                    <n-radio v-for="(val, index) in questions[index][idx].value" :key="val" :value="val"
                                        :label="val" />
                                </n-space>
                            </n-radio-group>
                        </template>

                        <!-- Essay -->
                        <template v-if="matchQuestion(index, idx, 3)">
                            <n-input v-model:value="answerForm.answers[index][idx].value" type="textarea" />
                        </template>

                        <!-- Handwriting Comparator -->
                        <template v-if="matchQuestion(index, idx, 4)">
                            TODO
                        </template>
                    </n-layout-content>
                </n-card>
            </n-form-item>
        </template>
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
    NRadio,
    NRadioGroup,
    NAlert,
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
        NRadio,
        NRadioGroup,
        NAlert,
    },
    props: {
        student_id: String,
        activity: Object,
    },
    setup({ activity }) {
        console.log(activity)
        const { questions, general_directions, } = activity

        const answerForm = useForm({
            'answers': questions.map((val) => {
                return val.map((v) => {
                    if (v.type === QUESTION_TYPES[5]) {
                        return {
                            id: v.id,
                            value: [],
                            files: v.value,
                            progress: {
                                current: 1,
                                total: v.value.length, // NOTE: Needs further testing
                            },
                        }
                    }

                    return {
                        id: v.id,
                        value: null,
                    }
                })
            })
        })

        function matchQuestion(parentIndex, childIndex, typeIndex) {
            return activity.questions[parentIndex][childIndex].type === QUESTION_TYPES[typeIndex]
        }

        return {
            answerForm,
            matchQuestion,
            questions,
            generalDirection: general_directions,
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

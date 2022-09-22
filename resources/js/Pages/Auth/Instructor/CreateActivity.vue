<template>
    <n-layout>
        <n-layout-header>
            <n-page-header title="Create Activity" @back="() => backLink()" style="overflow: hidden;" />
        </n-layout-header>
        <n-layout-content content-style="padding: 24px;">
            <n-form label-placement="left" require-mark-placement="right-hanging" label-width="120"
                :model="activityForm" style="max-width: 500px;">
                <n-form-item label="Title" path="title" required>
                    <n-input v-model:value="activityForm.title" />
                </n-form-item>
                <n-form-item label="Deadline" path="deadline" required>
                    <n-date-picker v-model:value="activityForm.deadline" type="datetime" format="MM-dd-yyyy - hh:mm a"
                        clearable />
                </n-form-item>
                <n-form-item label="Lock after deadline" path="lockAfterEnd">
                    <n-switch v-model:value="activityForm.lockAfterEnd" />
                </n-form-item>
                <n-form-item label="General Directions" path="generalDirections">
                    <n-input v-model:value="activityForm.generalDirections" type="textarea" />
                </n-form-item>

                <n-divider />

                <n-form-item v-for="({id, type, ...props}, index) in activityForm.questions" :key="id"
                    :path="`questions[$index]`">
                    <template v-if="type === QUESTION_TYPES[0]">
                    </template>
                </n-form-item>
                <n-form-item>
                    <n-layout>
                        <n-space justify="center" item-style="width: 100%; padding: 1.5rem;">
                            <n-button @click="() => addQuestion()" attr-type="button" block>Add Question</n-button>
                        </n-space>
                    </n-layout>
                </n-form-item>

                <n-form-item>
                    <n-button type="primary" attr-type="submit" :loading="activityForm.processing" :style="{
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
import { useForm } from '@inertiajs/inertia-vue3'
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
    NInputGroup,
    NInputNumber,
    NSelect,
    NDatePicker,
    NSwitch,
    NSpace,
    NDivider,
} from 'naive-ui'
import { nanoid } from 'nanoid'

import Layout from '@/Components/Layouts/InstructorLayout.vue'
import { QUESTION_TYPES } from '@/constants'

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
        NInputGroup,
        NInputNumber,
        NSelect,
        NDatePicker,
        NSwitch,
        NSpace,
        NDivider,
    },
    props: {
        classroom_id: String,
    },
    setup(props) {
        const { classroom_id } = props

        function backLink() {
            Inertia.get(route('instructor.classroom', {
                classroom_id,
            }))
        }

        const activityForm = useForm({
            title: '',
            deadline: null,
            lockAfterEnd: false,
            generalDirections: '',
            questions: [],
            target: [
                {
                    type: 'classroom',
                    value: classroom_id,
                },
            ]
        })

        function addQuestion() {
            activityForm.questions.push({
                id: nanoid(10),
                type: QUESTION_TYPES[0],
                value: '',
                answer: '',
            })
        }

        return {
            backLink,
            activityForm,
            addQuestion,
            QUESTION_TYPES,
        }
    }
}
</script>


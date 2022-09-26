<template>
    <n-layout>
        <n-layout-header>
            <n-page-header title="Create Task" @back="() => backLink()" style="overflow: hidden;" />
        </n-layout-header>
        <n-layout-content content-style="padding: 24px;">
            <n-form @submit.prevent="() => activityForm.transform((data) => ({
                ...data,
                start: data.start ? data.start : dayjs().millisecond(),
            })).post(route('post.instructor.create_activity', {
                classroom_id,
            }), {
                _method: 'PUT',
            })" label-placement="left" require-mark-placement="right-hanging" label-width="120" :model="activityForm"
                style="max-width: 500px;">
                <n-form-item label="Title" path="title" required>
                    <n-input v-model:value="activityForm.title" />
                </n-form-item>
                <n-form-item label="Start" path="start">
                    <template v-if="activityForm.start == null" #feedback>
                        <n-tag type="warning" style="margin-bottom: 0.5rem;">
                            Leaving time to start blank immediatetly starts the task.
                        </n-tag>
                    </template>
                    <n-date-picker v-model:value="activityForm.start" type="datetime" format="MM-dd-yyyy - hh:mm a"
                        clearable />
                </n-form-item>
                <n-form-item label="Deadline" path="deadline" required>
                    <n-date-picker v-model:value="activityForm.deadline" type="datetime" format="MM-dd-yyyy - hh:mm a"
                        clearable />
                </n-form-item>
                <n-form-item label="Lock after deadline" path="lockAfterEnd">
                    <n-switch v-model:value="activityForm.lockAfterEnd" />
                </n-form-item>
                <n-form-item label="General Instruction" path="generalDirections">
                    <n-input v-model:value="activityForm.generalDirections" type="textarea" />
                </n-form-item>

                <template v-for="(val, index) in activityForm.questions" :key="index">
                    <n-divider />

                    <n-form-item v-for="({ id, type }, idx) in val" :key="id" :path="`questions[${index}]`">
                        <n-card closable @close="() => removeQuestion(index, idx)">
                            <template #header>
                                <n-select :options="questionOptions"
                                    v-model:value="activityForm.questions[index][idx].type" />
                            </template>

                            <n-input v-model:value="activityForm.questions[index][idx].instruction" type="textarea"
                                placeholder="Instructions" />

                            <n-layout-content content-style="margin-top: 1rem; margin-left: 1rem;">
                                <!-- True or False -->
                                <template v-if="type === QUESTION_TYPES[2]">
                                    <n-dynamic-input v-model:value="activityForm.questions[index][idx].value"
                                        :min="2" />
                                </template>

                                <!-- Handwriting Comparator -->
                                <template v-if="type === QUESTION_TYPES[4]">
                                    <n-upload list-type="image-card" :name="`upload-${id}`" multiple :min="7" :max="11"
                                        @file-list="(fileList) => setImgList(fileList, index, idx)" />
                                </template>
                            </n-layout-content>
                        </n-card>
                    </n-form-item>

                    <n-form-item>
                        <n-layout>
                            <n-space justify="center" item-style="width: 100%;">
                                <n-button @click="addQuestion(index)" attry-type="button" dashed block>Add Question
                                </n-button>
                            </n-space>
                        </n-layout>
                    </n-form-item>
                </template>
                <n-form-item>
                    <n-layout>
                        <n-space justify="center" item-style="width: 100%;">
                            <n-button @click="() => addSection()" :disabled="isAddSectionDisabled()" attr-type="button"
                                dashed block>Add Section</n-button>
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
    <n-modal v-model:show="showPreviewRef" preset="card" stype="width: 600px;" title="Show Uploaded Image">
        <img :src="previewImgRef" style="width: 100%;" />
    </n-modal>
</template>

<script>
import dayjs from 'dayjs'
import { ref } from 'vue'
import { Inertia } from '@inertiajs/inertia'
import { useForm } from '@inertiajs/inertia-vue3'
import {
    NLayout,
    NLayoutContent,
    NLayoutHeader,
    NH2,
    NButton,
    NButtonGroup,
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
    NCard,
    NModal,
    NUpload,
    NTag,
    NDynamicInput,
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
        NButtonGroup,
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
        NCard,
        NModal,
        NUpload,
        NTag,
        NDynamicInput,
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
            start: null,
            deadline: null,
            lockAfterEnd: false,
            generalDirections: '',
            questions: [[]],
            target: [
                {
                    type: 'classroom',
                    value: classroom_id,
                },
            ]
        })

        const questionOptions = QUESTION_TYPES.map((val) => ({
            label: val,
            value: val,
        }))

        function addQuestion(parentIndex) {
            activityForm.questions[parentIndex].push({
                id: nanoid(10),
                type: QUESTION_TYPES[0],
                instruction: '',
                value: '', // Either string or FileList
                answer: '',
            })
            console.log(activityForm.questions)
        }

        function addSection() {
            if (activityForm.questions[activityForm.questions.length - 1].length !== 0) {
                activityForm.questions.push([])
            }
        }
        function isAddSectionDisabled() {
            return activityForm.questions[activityForm.questions.length - 1].length === 0
        }

        function removeQuestion(parentIndex, childIndex) {
            activityForm.questions[parentIndex].splice(childIndex, 1)
        }

        const showPreviewRef = ref(false)
        const previewImgRef = ref('')
        function handleImgPreview(file) {
            const { url } = file
            previewImgRef.value = url
            showPreviewRef.value = true
        }
        function setImgList(fileList, parentIndex, childIndex) {
            activityForm.questions[parentIndex][childIndex].value = fileList
        }

        return {
            dayjs,
            backLink,
            activityForm,
            questionOptions,
            addQuestion,
            addSection,
            isAddSectionDisabled,
            removeQuestion,
            QUESTION_TYPES,
            showPreviewRef,
            previewImgRef,
            handleImgPreview,
            setImgList,
        }
    }
}
</script>


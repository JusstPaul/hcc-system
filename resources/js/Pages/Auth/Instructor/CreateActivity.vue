<template>
    <n-layout>
        <n-layout-header>
            <n-page-header title="Create Activity" @back="() => backLink()" style="overflow: hidden;" />
        </n-layout-header>
        <n-layout-content content-style="padding: 24px;">
            <n-form @submit.prevent="() => activityForm.post(route('post.instructor.create_activity', {
                classroom_id,
            }), {
                _method: 'PUT',
            })" label-placement="left" require-mark-placement="right-hanging" label-width="120" :model="activityForm"
                style="max-width: 500px;">
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

                <n-form-item v-for="({ id, type }, index) in activityForm.questions" :key="id"
                    :path="`questions[${index}]`">
                    <n-card closable @close="() => removeQuestion(id)">
                        <template #header>
                            <n-select :options="questionOptions" v-model:value="activityForm.questions[index].type" />
                        </template>
                        <template v-if="type === QUESTION_TYPES[5]">
                            <n-upload :max="11" :multiple="true" list-type="image-card" :show-preview-button="true"
                                @preview="handleImgPreview" @change="({ fileList }) => setImgList(fileList, index)"
                                :name="`comparator-${id}`" />
                        </template>
                    </n-card>
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
    <n-modal v-model:show="showPreviewRef" preset="card" stype="width: 600px;" title="Show Uploaded Image">
        <img :src="previewImgRef" style="width: 100%;" />
    </n-modal>
</template>

<script>
import { ref } from 'vue'
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
    NCard,
    NModal,
    NUpload,
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
        NCard,
        NModal,
        NUpload,
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

        const questionOptions = QUESTION_TYPES.map((val) => ({
            label: val,
            value: val,
        }))

        function addQuestion() {
            activityForm.questions.push({
                id: nanoid(10),
                type: QUESTION_TYPES[0],
                value: '', // Either string or FileList
                answer: '',
            })
        }

        function removeQuestion(targetId) {
            const index = activityForm.questions.findIndex(({ id }) => id === targetId)
            activityForm.questions.splice(index, 1)
        }

        const showPreviewRef = ref(false)
        const previewImgRef = ref('')
        function handleImgPreview(file) {
            const { url } = file
            previewImgRef.value = url
            showPreviewRef.value = true
        }
        function setImgList(fileList, index) {
            activityForm.questions[index].value = fileList
        }

        return {
            backLink,
            activityForm,
            questionOptions,
            addQuestion,
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


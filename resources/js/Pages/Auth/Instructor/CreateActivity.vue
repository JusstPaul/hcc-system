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
          <n-date-picker v-model:value="activityForm.start" type="datetime" format="MM-dd-yyyy - hh:mm a" clearable />
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

        <template v-for="({ id, type, values }, index) in activityForm.questions" :key="id">

          <n-divider />

          <n-form-item label="Type" :path="`activityForm.questions[${index}].type`">
            <n-select :options="questionOptions" v-model:value="activityForm.questions[index].type" />
          </n-form-item>
          <n-form-item label="Section Instruction">
            <n-input type="textarea" v-model:value="activityForm.questions[index].instruction" />
          </n-form-item>

          <n-form-item v-for="({ id: childId }, idx) in values" :key="childId">
            <n-card closable @close="() => removeQuestion(index, idx)">
              <template #header>
                <n-input v-model:value="activityForm.questions[index].values[idx].score" :allow-input="allowNumberOnly"
                  placeholder="Score" style="margin-bottom: 1rem;" />
              </template>

              <n-input v-model:value="activityForm.questions[index].values[idx].instruction"
                placeholder="Instructions" />

              <n-layout>
                <n-layout-content content-style="margin-top: 1rem; margin-left: 1rem;">

                  <template v-if="type === QUESTION_TYPES[2]">
                    <n-dynamic-input v-model:value="activityForm.questions[index].values[idx].content" :min="2" />
                  </template>

                  <template v-if="type === QUESTION_TYPES[4]">
                    <div style="margin-bottom: 1.5rem;">
                      <span>Questioned</span>
                      <n-upload list-type="image-card" :name="`upload-${id}-${childId}-questioned`"
                        @change="({ fileList }) => setQuestionedImg(fileList, index, idx)" />
                    </div>

                    <div>
                      <span>Samples</span>
                      <n-upload list-type="image-card" :name="`upload-${id}-${childId}-samples`" multiple :min="6"
                        :max="10" @change="({fileList}) => setSamplesImgList(fileList, index, idx)" />
                    </div>

                  </template>

                </n-layout-content>
              </n-layout>

              <div style="margin-top: 1rem;">
                <template v-if="type === QUESTION_TYPES[1]">
                  <n-space vertical :size="1">
                    <span>Answer</span>
                    <n-radio-group v-model:value="activityForm.questions[index].values[idx].answer"
                      :name="`answer-${id}-${childId}`">
                      <n-space>
                        <n-radio :value="true" label="True" />
                        <n-radio :value="false" label="False" />
                      </n-space>
                    </n-radio-group>
                  </n-space>
                </template>
                <template v-if="type === QUESTION_TYPES[2]">
                  <n-select v-model:value="activityForm.questions[index].values[idx].answer"
                    :options="choicesToOptions(activityForm.questions[index].values[index].content)"
                    placeholder="Answer" />
                </template>
                <template v-if="type !== QUESTION_TYPES[4] && type !== QUESTION_TYPES[1] && type !== QUESTION_TYPES[2]">
                  <n-input v-model:value="activityForm.questions[index].values[idx].answer" placeholder="Answer" />
                </template>
              </div>

            </n-card>
          </n-form-item>

          <n-form-item>
            <n-layout>
              <n-space justify="center">
                <n-button @click="() => addQuestion(index)" attry-type="button" type="primary" tertiary block>Add
                  Question
                </n-button>
                <n-button @click="() => removeSection(index)" attr-type="button" type="error" tertiary block>Remove
                  Question
                </n-button>
              </n-space>
            </n-layout>
          </n-form-item>
        </template>

        <n-form-item>
          <n-layout>
            <n-space justify="center" item-style="width: 100%;">
              <n-button @click="() => addSection()" attr-type="button" dashed block>
                Add Section</n-button>
            </n-space>
          </n-layout>
        </n-form-item>

        <n-form-item>
          <n-button type="primary" attr-type="submit" :loading="activityForm.processing" :style="{
              marginRight: 0,
              marginLeft: 'auto'
          }">
            Post Task
          </n-button>
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
import { isString, first } from 'lodash'
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
  NRadio,
  NRadioGroup,
} from 'naive-ui'
import { nanoid } from 'nanoid'

import { allowNumberOnly } from '@/utils'
import { QUESTION_TYPES } from '@/constants'
import Layout from '@/Components/Layouts/InstructorLayout.vue'

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
    NRadio,
    NRadioGroup,
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
      questions: [{
        id: nanoid(),
        type: QUESTION_TYPES[0],
        instruction: '',
        values: []
      }],
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

    function addQuestion(index) {
      const score = activityForm.questions[index].values.length === 0 ?
        '1' :
        activityForm.questions[index].values[activityForm.questions[index].values.length - 1].score

      let answer = null;
      if (activityForm.questions[index].type === QUESTION_TYPES[1]) {
        answer = true;
      }

      activityForm.questions[index].values.push({
        id: nanoid(10),
        instruction: '',
        content: null,
        score: score,
        answer: answer,
      })
    }

    function addSection() {
      activityForm.questions.push({
        id: nanoid(10),
        type: QUESTION_TYPES[0],
        instruction: '',
        values: []
      })
    }
    function removeSection(index) {
      activityForm.questions.splice(index, 1)
    }

    function choicesToOptions(choices) {
      if (choices == null) {
        return []
      }
      return choices.map((val) => ({
        label: val,
        value: val,
      }))
    }
    const trueOrFalseOptions = [
      {
        label: 'True',
        value: 'True',
      },
      {
        label: 'False',
        value: 'False',
      },
    ]

    function removeQuestion(parentIndex, childIndex) {
      activityForm.questions[parentIndex].values.splice(childIndex, 1)
    }

    const showPreviewRef = ref(false)
    const previewImgRef = ref('')
    function handleImgPreview(file) {
      const { url } = file
      previewImgRef.value = url
      showPreviewRef.value = true
    }
    function setSamplesImgList(fileList, parentIndex, childIndex) {
      const content = activityForm.questions[parentIndex].values[childIndex].content
      if (content == null || isString(content)) {
        activityForm.questions[parentIndex].values[childIndex].content = {
          samples: fileList,
          questioned: null,
        }
      } else {
        activityForm.questions[parentIndex].values[childIndex].content.samples = fileList
      }
    }

    function setQuestionedImg(fileList, parentIndex, childIndex) {
      const content = activityForm.questions[parentIndex].values[childIndex].content
      if (content == null || isString(content)) {
        activityForm.questions[parentIndex].values[childIndex].content = {
          samples: null,
          questioned: first(fileList),
        }
      } else {
        activityForm.questions[parentIndex].values[childIndex].content.questioned = first(fileList)
      }
    }

    return {
      dayjs,
      allowNumberOnly,
      backLink,
      activityForm,
      questionOptions,
      addQuestion,
      addSection,
      removeSection,
      removeQuestion,
      QUESTION_TYPES,
      showPreviewRef,
      previewImgRef,
      handleImgPreview,
      setSamplesImgList,
      setQuestionedImg,
      choicesToOptions,
      trueOrFalseOptions,
    }
  }
}
</script>

<!--
<template lang="pug">
n-layout
  n-layout-header
    n-page-header.overflow-hidden(
      title="Create Task",
      @back="() => backLink()",
    )
</template>
-->

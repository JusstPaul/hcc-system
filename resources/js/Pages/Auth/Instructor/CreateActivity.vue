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
  NGrid,
  NGridItem,
  useNotification,
} from 'naive-ui'
import { QuillEditor } from '@vueup/vue-quill'
import { nanoid } from 'nanoid'

import { allowNumberOnly } from '@/utils'
import {
  pXS,
  wMax,
  mxAuto,
  mbHalfRem,
  wFull,
  mtHalfRem,
  mlAuto,
  mr,
} from '@/styles'
import { QUESTION_TYPES } from '@/constants'
import Layout from '@/Components/Layouts/InstructorLayout.vue'

export default {
  layout: Layout,
  components: {
    NLayout,
    NLayoutContent,
    NLayoutHeader,
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
    NGrid,
    NGridItem,
    QuillEditor,
  },
  props: {
    classroom_id: String,
  },
  setup(props) {
    const { classroom_id } = props

    const notif = useNotification()

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
          samples: [],
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
      pXS,
      wMax,
      mxAuto,
      mbHalfRem,
      mtHalfRem,
      wFull,
      mlAuto,
      mr,
      notif,
    }
  }
}
</script>

<template lang="pug">
n-layout
  n-layout-header
    n-page-header.overflow-hidden(
      title="Create Task",
      @back="() => backLink()",
    )
  n-layout-content(:content-style="pXS")
    n-space.w-full(justify="center", :item-style="wFull")
      n-form(
        require-mark-placement="right-hanging",
        label-width="120",
        :style=`{
          ...wFull,
          ...wMax(500),
          ...mxAuto,
        }`,
        :model="activityForm",
        @submit.prevent=`() => activityForm.transform((data) => ({
          ...data,
          start: data.start ? data.start : dayjs().valueOf(),
        })).post(route('post.instructor.create_activity', {
          classroom_id
        }), {
          _method: 'put',
          onError: () => notif.error({
            title: 'An Error Occured',
            content: 'Failed to post activity'
          })
        })`
      )
        n-form-item(
          required,
          label="Title",
          path="title"
        )
          n-input(v-model:value="activityForm.title")

        n-form-item(
          label="Start",
          path="start"
        )
          n-date-picker.w-full(
            clearable,
            v-model:value="activityForm.start",
            type="datetime",
            format="MM-dd-yyyy - hh:mm a"
          )
            template(#footer, v-if="activityForm.start == null")
              n-tag(type="warning", :style="mbHalfRem")
                |Leaving this blank immediatetly starts the task

        n-form-item(required, label="Deadline", path="deadline")
          n-date-picker.w-full(
            clearable,
            v-model:value="activityForm.deadline",
            type="datetime",
            format="MM-dd-yyyy - hh:mm a"
          )

        n-form-item(
          required,
          label="Lock after deadline",
          path="lockAfterEnd"
        )
          n-switch(v-model:value="activityForm.lockAfterEnd")

        n-form-item(
          required, 
          label="Generation Instructions",
          path="generalDirections"
        )
          div(:style="{...wFull}")
            quill-editor(
              theme="snow",
              toolbar="minimal",
              v-model:content="activityForm.generalDirections",
              placeholder="Enter instructions"
            )

        for section, index in activityForm.questions
          template(:key="section.id")
            n-divider

            n-form-item(
              label="Type",
              :path="`questions[${index}].type`"
            )
              n-select(
                :options="questionOptions",
                v-model:value="section.type"
              )

            n-form-item(
              label="Instruction",
              :path="`questions[${index}].instruction`"
            )
              div(:style="{...wFull}")
                quill-editor(
                  theme="snow",
                  toolbar="minimal",
                  v-model:content="section.instruction",
                  placeholder="Enter instructions"
                )

            for question, idx in section.values
              n-form-item(:key="question.id")
                n-card(closable, @close="() => removeQuestion(index, idx)")
                  template(#header)
                    n-form-item(label="Score", :show-feedback="false")
                      n-input(
                        v-model:value="question.score",
                        :allow-input="allowNumberOnly",
                        placeholder="Score"
                      )
                  n-form-item(label="Instruction")
                    n-input(
                      v-model:value="question.instruction",
                      placeholder="Instruction"
                    )

                  n-layout
                    n-layout-content(:content-style="mtHalfRem")
                      if section.type === QUESTION_TYPES[2]
                        n-dynamic-input(
                          v-model:value="question.content"
                          :min="2"
                        )

                      if section.type === QUESTION_TYPES[4]
                        n-form-item(
                          label="Questioned",
                        )
                          n-upload(
                            list-type="image-card",
                            :name="`upload-${section.id}-${question.id}-questioned`",
                            @change="({ fileList }) => setQuestionedImg(fileList, index, idx)"
                          )
                        n-form-item(
                          label="Samples",
                        )
                          n-upload(
                            multiple,
                            list-type="image-card",
                            :name="`upload-${section.id}-${question.id}-samples`",
                            :min="6",
                            :max="10",
                            @change="({ fileList }) => setSamplesImgList(fileList, index, idx)"
                          )

                    if section.type !== QUESTION_TYPES[4]
                      n-form-item(label="Answer")
                        if section.type === QUESTION_TYPES[1] 
                          n-radio-group(
                            v-model:value="question.answer",
                            :name="`answer-${section.id}-${question.id}`"
                          )

                        if section.type === QUESTION_TYPES[2]
                          n-select(
                            v-model:value="question.answer",
                            :options="choicesToOptions(question.content)",
                            placeholder="Select Answer"
                          )

                        if section.type !== QUESTION_TYPES[1] && section.type !== QUESTION_TYPES[2]
                          n-input(v-model:value="question.answer")

            n-grid(:cols="2", :x-gap="5")
              n-grid-item
                n-button(
                  dashed,
                  block,
                  type="warning",
                  attr-type="button",
                  @click="() => addQuestion(index)",
                ) Add Question
              n-grid-item
                n-button(
                  dashed,
                  block,
                  type="error",
                  attr-type="button",
                  @click="() => removeSection(index)"
                ) Remove Section

        n-form-item
          div.w-full
            n-button(
              dashed,
              block,
              type="primary",
              attr-type="button",
              @click="() => addSection()"
            ) Add Section

        n-form-item
          n-button(
            type="primary",
            attr-type="submit",
            :loading="activityForm.processing",
            :style="{...mlAuto, ...mr(0)}"
          ) Post
</template>

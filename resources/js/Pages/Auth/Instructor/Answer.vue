<script>
import { computed, } from 'vue'
import { useAsyncState } from '@vueuse/core'
import { isUndefined } from 'lodash'
import { Inertia } from '@inertiajs/inertia'
import { useForm, usePage } from '@inertiajs/inertia-vue3'
import {
  NLayout,
  NLayoutContent,
  NLayoutHeader,
  NButton,
  NButtonGroup,
  NPageHeader,
  NTime,
  NAlert,
  NCard,
  NForm,
  NFormItem,
  NInput,
  NSpace,
  NDivider,
  NInputNumber,
  NImage,
} from 'naive-ui'
import {
  pXS,
  mxHalfRem,
  wFull,
  wMax,
  mxAuto,
  mlAuto,
  mr,
} from '@/styles'
import { convertDeltaContent, requestFile } from '@/utils'
import { QUESTION_TYPES } from '@/constants'
import Layout from '@/Components/Layouts/InstructorLayout.vue'

async function keyToJpeg(token, key) {
  const response = await requestFile(token, key)
  const url = URL.createObjectURL(response)
  return url
}

export default {
  layout: Layout,
  components: {
    NLayout,
    NLayoutContent,
    NLayoutHeader,
    NButton,
    NButtonGroup,
    NPageHeader,
    NTime,
    NAlert,
    NCard,
    NForm,
    NFormItem,
    NInput,
    NSpace,
    NDivider,
    NInputNumber,
    NImage,
  },
  props: {
    classroom_id: String,
    activity: Object,
  },
  setup({ classroom_id, activity }) {

    const token = usePage().props.value.user.token

    const { questions, title, general_directions, } = activity
    const deadline = parseInt(activity.deadline)
    const answers = activity.answers.answers
    const checks = activity.answers.checks

    const checkForm = useForm({
      'checks': isUndefined(checks) ? questions.map((section, index) => section.values.map(({
        id,
        score,
      }, idx) => {
        let extra;
        if (questions[index].type === QUESTION_TYPES[4]) {
          extra = answers[index].values[idx].value.map(({
            fileContent,
            description,
            id
          }) => {
            const f = useAsyncState(keyToJpeg(token, fileContent))
            return {
              file: f,
              description,
              id,
            }
          })
        }

        return {
          id,
          score: 0,
          total: score,
          comment: '',
          extra,
        }
      })) : checks,
    })

    function isComparator(index) {
      return questions[index].type === QUESTION_TYPES[4]
    }

    function backLink() {
      Inertia.get(route('instructor.activity.submits', {
        classroom_id: classroom_id,
        activity_id: activity._id,
      }))
    }

    function removeExtra(data) {
      const checks = data.checks.map((v) => v.map(({ id, score, total, comment }) => ({
        id,
        score,
        total,
        comment,
      })))

      return {
        ...data,
        checks: checks,
      }
    }

    return {
      backLink,
      title,
      deadline,
      generalDirections: computed(() => convertDeltaContent(general_directions)),
      questions,
      answers,
      classroom_id,
      activity_id: activity._id,
      answer_id: activity.answers._id.$oid,
      mxHalfRem,
      pXS,
      wFull,
      wMax,
      mxAuto,
      checkForm,
      convertDeltaContent,
      isComparator,
      mlAuto,
      mr,
      removeExtra,
    }
  }
}
</script>

<template lang="pug">
n-layout
  n-layout-header
    n-page-header.overflow-hidden(
      :title="title",
      @back="() => backLink()"
    )
      template(#subtitle)
        span(:style="mxHalfRem") Deadline:
        n-time(:time="deadline", format="MM/dd/yyyy hh:mm a")
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
        :model="checkForm",
        @submit.prevent=`() => checkForm.transform((data) => removeExtra(data)).post(route('post.instructor.activity.submits.answer', {
          classroom_id,
          activity_id,
          answer_id 
        }))`
      )
        n-form-item(label="General Instructions")
          n-alert.w-full
            div(v-html="generalDirections")

        for checks, section in checkForm.checks
          template(:key="section")
            n-form-item
              n-divider

            n-form-item(label="Instructions")
              n-alert.w-full
                div(v-html="convertDeltaContent(questions[section].instruction)")

            for item, question in checks
              n-card(:key="item.id")
                n-form-item(label="Score")
                  n-input-number.w-full(
                    v-model:value="item.score",
                    :min="0",
                    :max="item.total",
                  )
                    template(#suffix) / {{ item.total }}
                n-form-item(label="Question")
                  n-alert.w-full
                    |{{ questions[section].values[question].instruction }}

                n-form-item(label="Answer", :show-feedback="false")
                  if isComparator(section)
                    for comparator in item.extra
                      n-form-item.w-full(:key="comparator.id")
                        div.w-full(ref="parent")
                          if (comparator.file.isLoading)
                            div Loading...
                          else
                            n-image.w-full(
                              :src="comparator.file.state",
                              object-fit="scale-down",
                              :width="450"
                            )
                          n-alert.w-full
                            |{{ comparator.description }}
                  else
                    n-alert.w-full
                      |{{ answers[section].values[question].value }}

        n-form-item
          n-button(
            type="primary",
            attr-type="submit",
            :loading="checkForm.processing",
            :style="{...mlAuto, ...mr(0)}"
          ) Submit
</template>

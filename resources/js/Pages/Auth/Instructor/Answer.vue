<script>
import { computed } from 'vue'
import { useAsyncState } from '@vueuse/core'
import { isUndefined } from 'lodash'
import { stringify as romanStringify } from 'roman-numerals-convert'
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
  NList,
  NListItem,
  NText,
} from 'naive-ui'
import { QuillEditor } from '@vueup/vue-quill'
import { pXS, mxHalfRem, wFull, wMax, mxAuto, mlAuto, mr } from '@/styles'
import { convertDeltaContent, keyToJpeg } from '@/utils'
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
    NList,
    NListItem,
    NText,
    QuillEditor,
  },
  props: {
    classroom_id: String,
    activity: Object,
  },
  setup({ classroom_id, activity }) {
    const token = usePage().props.value.user.token

    const { questions, title, general_directions } = activity
    const deadline = parseInt(activity.deadline)
    const answers = activity.answers.answers
    const checks = activity.answers.checks

    const checkForm = useForm({
      checks: questions.map((section, index) =>
        section.values.map(({ id, score }, idx) => {
          let extra
          if (questions[index].type === QUESTION_TYPES[4]) {
            const snapshots = answers[index].values[idx].value.snapshots.map(
              ({ fileContent, description, id }) => {
                const f = useAsyncState(keyToJpeg(token, fileContent))
                return {
                  file: f,
                  description,
                  id,
                }
              },
            )

            extra = {
              snapshots,
              conclusion: answers[index].values[idx].value.conclusion,
              conclusionType: answers[index].values[idx].value.conclusionType,
            }
          }

          if (!isUndefined(checks)) {
            return {
              ...checks[index][idx],
              extra,
            }
          }

          return {
            id,
            score: 0,
            total: score,
            comment: null,
            extra,
          }
        }),
      ),
    })


    function isComparator(index) {
      return questions[index].type === QUESTION_TYPES[4]
    }

    function isEssay(index) {
      return questions[index].type === QUESTION_TYPES[3]
    }

    function backLink() {
      Inertia.get(
        route('instructor.activity.submits', {
          classroom_id: classroom_id,
          activity_id: activity._id,
        }),
      )
    }

    function removeExtra(data) {
      const checks = data.checks.map((v) =>
        v.map(({ id, score, total, comment }) => ({
          id,
          score,
          total,
          comment,
        })),
      )

      return {
        ...data,
        checks: checks,
      }
    }

    return {
      backLink,
      romanStringify,
      title,
      deadline,
      generalDirections: computed(() =>
        convertDeltaContent(general_directions),
      ),
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
      isEssay,
      mlAuto,
      mr,
      removeExtra,
    }
  },
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
          ...wMax(768),
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
          n-alert.w-full(:show-icon="false")
            div(v-html="generalDirections")

        for checks, section in checkForm.checks
          template(:key="section")
            n-form-item
              n-divider

            n-form-item(:label="`${romanStringify(section + 1)}. ${questions[section].type}`")
              n-alert.w-full(:show-icon="false")
                div(v-html="convertDeltaContent(questions[section].instruction)")

            for item, question in checks
              template(:key="item.id")
                n-card
                  n-form-item(label="Score")
                    n-input-number.w-full(
                      v-model:value="item.score",
                      :min="0",
                      :max="item.total",
                    )
                      template(#suffix) / {{ item.total }}
                  n-form-item(label="Question")
                    n-alert.w-full(:show-icon="false")
                      |{{ questions[section].values[question].instruction }}

                  n-form-item(label="Answer", :show-feedback="false")
                    if isComparator(section)
                      n-space.w-full(vertical)
                        if item.extra
                          for comparator in item.extra.snapshots
                            n-form-item.w-full(:key="comparator.id")
                              div.w-full(ref="parent")
                                if (comparator.file.isLoading)
                                  div Loading...
                                else
                                  n-image.w-full.mx-auto(
                                    :src="comparator.file.state",
                                    object-fit="scale-down",
                                    :width="450"
                                    :style=`{
                                    ...mxAuto
                                    }`
                                  )
                                n-alert.w-full(:show-icon="false")
                                  div(v-html="convertDeltaContent(comparator.description)")
                          n-form-item.w-full
                            n-alert.w-full(:show-icon="false")
                              template(#header)
                                span Overall conclusion: {{ item.extra.conclusionType }}
                              div(v-html="convertDeltaContent(item.extra.conclusion)")
                          n-form-item.w-full
                            n-alert.w-full(:show-icon="false", title="Legend")
                              n-list
                                n-list-item
                                  n-text(strong=true, style="margin-right: 1rem") 1
                                  n-text Pen pressure
                                n-list-item
                                  n-text(strong=true, style="margin-right: 1rem") 2
                                  n-text Patching
                                n-list-item
                                  n-text(strong=true, style="margin-right: 1rem") 3
                                  n-text Tremor                              
                                n-list-item
                                  n-text(strong=true, style="margin-right: 1rem") 4
                                  n-text Stroke                             
                                n-list-item
                                  n-text(strong=true, style="margin-right: 1rem") 5
                                  n-text Diacritic                           
                                n-list-item
                                  n-text(strong=true, style="margin-right: 1rem") 6
                                  n-text Baseline                          
                                n-list-item
                                  n-text(strong=true, style="margin-right: 1rem") 7
                                  n-text Alignment
                                n-list-item
                                  n-text(strong=true, style="margin-right: 1rem") 8
                                  n-text Line quality
                                n-list-item
                                  n-text(strong=true, style="margin-right: 1rem") 9
                                  n-text Lateral spacing
                                n-list-item
                                  n-text(strong=true, style="margin-right: 1rem") 10
                                  n-text Natural variation
                                n-list-item
                                  n-text(strong=true, style="margin-right: 1rem") 11
                                  n-text Rhythm
                                n-list-item
                                  n-text(strong=true, style="margin-right: 1rem") 12
                                  n-text Proportion
                                n-list-item
                                  n-text(strong=true, style="margin-right: 1rem") 13
                                  n-text Slant                              
                                n-list-item
                                  n-text(strong=true, style="margin-right: 1rem") 14
                                  n-text Hiatus                              
                                n-list-item
                                  n-text(strong=true, style="margin-right: 1rem") 15
                                  n-text Pen lift                             
                                n-list-item
                                  n-text(strong=true, style="margin-right: 1rem") 16
                                  n-text Ligature                            
                                n-list-item
                                  n-text(strong=true, style="margin-right: 1rem") 17
                                  n-text Retracing
                                n-list-item
                                  n-text(strong=true, style="margin-right: 1rem") 18
                                  n-text Writing habit
                                n-list-item
                                  n-text(strong=true, style="margin-right: 1rem") 19
                                  n-text Hook or Trough
                                n-list-item
                                  n-text(strong=true, style="margin-right: 1rem") 20
                                  n-text Hump
                                n-list-item
                                  n-text(strong=true, style="margin-right: 1rem") 21
                                  n-text Diacritic
                                n-list-item
                                  n-text(strong=true, style="margin-right: 1rem") 22
                                  n-text Eye loop or Eyelet
                                n-list-item
                                  n-text(strong=true, style="margin-right: 1rem") 23
                                  n-text Blunt Ending or Beginning  
                                n-list-item
                                  n-text(strong=true, style="margin-right: 1rem") 24
                                  n-text Buckle Knot                                   
                                n-list-item
                                  n-text(strong=true, style="margin-right: 1rem") 25
                                  n-text Arch or Arch                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                       
                    else
                      if isEssay(section)
                        n-alert.w-full(:show-icon="false")
                          div(v-html="convertDeltaContent(answers[section].values[question].value)")
                      else
                        n-alert.w-full(:show-icon="false")
                          |{{ answers[section].values[question].value }}

                  n-form-item(label="Comment")
                    .w-full
                      quill-editor(
                        theme="snow",
                        toolbar="minimal",
                        v-model:content="item.comment"
                        placeholder="Comment"
                      )

        n-form-item
          n-button(
            type="primary",
            attr-type="submit",
            :loading="checkForm.processing",
            :style="{...mlAuto, ...mr(0)}"
          ) Submit
</template>

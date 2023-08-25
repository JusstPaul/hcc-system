<script>
import { computed, ref } from 'vue'
import { useAsyncState } from '@vueuse/core'
import { usePage } from '@inertiajs/inertia-vue3'
import { Inertia } from '@inertiajs/inertia'
import {
  NLayout,
  NLayoutContent,
  NLayoutHeader,
  NPageHeader,
  NTime,
  NSpace,
  NFormItem,
  NAlert,
  NCard,
  NDivider,
  NImage,
} from 'naive-ui'
import { stringify as romanStringify } from 'roman-numerals-convert'
import { mxHalfRem, pXS, wFull, wMax, mxAuto } from '@/styles'
import { DATE_FORMAT, QUESTION_TYPES } from '@/constants'
import { convertDeltaContent, keyToJpeg } from '@/utils'
import Layout from '@/Components/Layouts/StudentLayout.vue'

export default {
  layout: Layout,
  components: {
    NLayout,
    NLayoutContent,
    NLayoutHeader,
    NPageHeader,
    NTime,
    NSpace,
    NFormItem,
    NAlert,
    NCard,
    NDivider,
    NImage,
  },
  props: {
    answer: Object,
    activity: Object,
  },
  setup({ answer, activity }) {
    console.log(answer)
    console.log(activity)

    const token = computed(() => usePage().props.value.user.token)

    const snapshots = ref(new Map([]))
    //function addOrGetSnaphots
    function backLink() {
      Inertia.get('/')
    }

    function isComparator(index) {
      return activity.questions[index].type === QUESTION_TYPES[4]
    }

    function isEssay(index) {
      return activity.questions[index].type === QUESTION_TYPES[3]
    }

    return {
      backLink,
      mxHalfRem,
      pXS,
      wFull,
      wMax,
      mxAuto,
      DATE_FORMAT,
      generalDirections: computed(() =>
        convertDeltaContent(activity.general_directions),
      ),
      convertDeltaContent,
      romanStringify,
      isComparator,
      isEssay,
      snapshots,
      handleSnapshots: (id, files) => {
        if (snapshots.value.has(id)) {
          return snapshots.value.get(id)
        }

        const _files = files.map(({ fileContent }) => {
          return useAsyncState(keyToJpeg(token, fileContent))
        })

        snapshots.value.set(id, _files)
        return snapshots.value.get(id)
      },
        questions: activity.questions,
        answer,
    }
  },
}
</script>

<template lang="pug">
n-layout
  n-layout-header
    n-page-header.overflow-hidden(
      :title="activity.title",
      @back="() => backLink()"
    )
      template(#subtitle)
        span(:style="mxHalfRem") Deadline:
        n-time(:time="parseInt(activity.deadline)", :format="`${DATE_FORMAT}`")
  n-layout-content(:content-style="pXS")
    n-space.w-full(justify="center", :item-style="wFull")
      div(:style=`{
        ...wFull,
        ...wMax(768),
        ...mxAuto
      }`)
        n-form-item(label="General Instructions")
          n-alert.w-full(:show-icon="false")
            div(v-html="generalDirections")

        for checks, section in answer.checks
          template(:key="section")
            n-form-item
              n-divider

            n-form-item(:label="`${romanStringify(section + 1)}. ${questions[section].type}`")
              n-alert.w-full(:show-icon="false")
                div(v-html="convertDeltaContent(questions[section].instruction)")

            for item, question in checks
              n-card(:key="item.id")
                n-form-item(:label="`Score: ${item.score} / ${item.total}`")
                n-form-item(label="Question")
                  n-alert.w-full(:show-icon="false")
                    | {{ questions[section].values[question].instruction }}

                n-form-item(label="Answer", :show-feedback="false")
                  if isComparator(section)
                    n-space(vertical)
                      for snapshot, snap_index in handleSnapshots(item.id, answer.answers[section].values[question].value.snapshots)
                        n-form-item.w-full(:key="snap_index")
                          .w-full
                            if snapshot.isLoading
                              div Loading...
                            else
                              n-image.w-full.mx-auto(
                                :src="snapshot.state",
                                object-fit="scale-down",
                                :width="450",
                                :style="mxAuto"
                              )
                            n-alert(:show-icon="false")
                              div(v-html="convertDeltaContent(answer.answers[section].values[question].value.snapshots[snap_index].description)")

                      n-form-item.w-full
                        n-alert.w-full(:show-icon="false")
                          template(#header)
                            span Overall conclusion: {{ answer.answers[section].values[question].value.conclusionType }}
                          div(v-html="convertDeltaContent(answer.answers[section].values[question].value.conclusion)")

                  else
                    if isEssay(section)
                      n-alert.w-full(:show-icon="false")
                        div(v-html="convertDeltaContent(answer.answers[section].values[question].value)")
                    else
                      n-alert.w-full(:show-icon="false")
                        | {{ answer.answers[section].values[question].value }}

                n-form-item(label="Comment")
                  .w-full
                    n-alert(:show-icon="false")
                      div(v-html="convertDeltaContent(item.comment)")

</template>

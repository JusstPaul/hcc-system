<script>
import dayjs from 'dayjs'
import { computed } from 'vue'
import { isUndefined } from 'lodash'
import { Inertia } from '@inertiajs/inertia'
import { useForm, usePage } from '@inertiajs/inertia-vue3'
import {
  NLayout,
  NLayoutHeader,
  NLayoutContent,
  NForm,
  NFormItem,
  NUpload,
  NUploadTrigger,
  NUploadFileList,
  NSpace,
  NButton,
  NIcon,
  NAlert,
  NCard,
  NTime,
  NButtonGroup,
  NTabs,
  NTabPane,
  NStatistic,
} from 'naive-ui'
import { QuillEditor } from '@vueup/vue-quill'
import { Upload as UploadIcon } from '@vicons/tabler'
import { wMax, wFull, mxAuto, mtHalfRem } from '@/styles'
import { getFileName, convertDeltaContent, downloadFile } from '@/utils'
import Layout from '@/Components/Layouts/InstructorLayout.vue'

export default {
  layout: Layout,
  components: {
    NLayout,
    NLayoutHeader,
    NLayoutContent,
    NForm,
    NFormItem,
    NUpload,
    NUploadTrigger,
    NUploadFileList,
    NSpace,
    NButton,
    NButtonGroup,
    NIcon,
    NAlert,
    NCard,
    NTime,
    NTabs,
    NTabPane,
    NStatistic,
    UploadIcon,
    QuillEditor,
  },
  props: {
    classroom_id: String,
    announcements: Object,
    activities: Array,
  },
  setup({ activities, classroom_id }) {
    const { tab } = route().params

    const token = usePage().props.value.user.token
    const reverseActivites = activities.reverse()

    const anForm = useForm({
      content: '',
      fileContents: [],
    })

    function handleFormUpload(fileList) {
      anForm.fileContents = fileList
    }

    // TODO: Move to utils
    function strDateToMil(date) {
      return dayjs(date).valueOf()
    }

    function getNumberOfSubmits(val) {
      if (isUndefined(val)) {
        return 0
      }
      return val.length
    }

    return {
      tab: computed(() => (isUndefined(tab) ? 'announcements' : tab)),
      wMax,
      wFull,
      mxAuto,
      mtHalfRem,
      anForm,
      handleFormUpload,
      convertDeltaContent,
      Inertia,
      strDateToMil,
      downloadFile,
      getFileName,
      activities,
      getNumberOfSubmits,
      reverseActivites
    }
  },
}
</script>

<template lang="pug">
n-layout
  n-layout-content
    n-tabs(type="line", :default-value="tab")
      n-tab-pane(
        name="announcements",
        tab="Announcements",
      )
        n-form(
          :model="anForm"
          label-placement="left",
          require-mark-placement="right-hanging",
          label-width="120",
          :style=`{
            ...wMax(500),
            ...mxAuto,
          }`,
          @submit.prevent=`anForm.post(route('post.instructor.create_announcement', {
            classroom_id
          }), {
            _method: 'put',
            onSuccess: () => Inertia.get(route('instructor.classroom', {
              classroom_id
            }), {
              only: []
            })
          })`
        )
          n-form-item(path="content")
            div(:style="{...wFull, ...wMax(800)}")
              quill-editor(
                theme="snow",
                toolbar="minimal",
                placeholder="Create Announcement"
                v-model:content="anForm.content"
              )

          n-form-item(path="fileContents")
            n-upload(
              abstract,
              multiple,
              @change="({ fileList }) => handleFormUpload(fileList)"
            )
              .w-full
                n-space(justify="end")
                  n-upload-trigger(abstract, #="{ handleClick }")
                    n-button(
                      secondary,
                      attr-type="button",
                      @click="handleClick"
                    )
                      template(#icon)
                        n-icon
                          upload-icon
                      |Upload
                  n-button(attr-type="submit", type="primary") Post
                n-alert(
                  v-if="anForm.fileContents.length !== 0",
                  title="Upload Files",
                  :style="mtHalfRem"
                )
                  n-upload-file-list
        n-space(justify="center", :item-style="wFull")
          n-space(vertical, :item-style="wFull")
            for val in announcements
              n-card(:key="val._id", :style="{...wFull, ...wMax(800), ...mxAuto}")
                n-time(:time="strDateToMil(val.created_at)")
                n-alert
                  div(v-html="convertDeltaContent(val.content)")
                template(#footer)
                  if val.fileContents.length !== 0
                    div Attachments:
                    n-space(vertical)
                      for file in val.fileContents
                        n-button(quaternary, @click="() => downloadFile(token, file)") {{ getFileName(file) }}
      n-tab-pane(name="activities", tab="Activities")
        n-space(vertical, :item-style="wFull")
          for act in reverseActivites
            n-card(:key="act._id", :style="{...wFull, ...wMax(800), ...mxAuto}")
              template(#header)
                i-link.link.cursor-pointer(
                  :href=`route('instructor.activity.submits', {
                    classroom_id,
                    activity_id: act._id,
                  })`
                ) {{ act.title }}
              template(#header-extra)
                n-time(:time="strDateToMil(act.created_at)")
              n-statistic(label="Number of submits", :value="getNumberOfSubmits(act.answers)")
</template>

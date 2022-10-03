<script>
import dayjs from 'dayjs'
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
import { saveAs } from 'file-saver'
import {
  wMax,
  wFull,
  mxAuto,
  mtHalfRem,
} from '@/styles'
import { requestFile, convertDeltaContent } from '@/utils'
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
    const token = usePage().props.value.user.token

    const anForm = useForm({
      content: '',
      fileContents: []
    })

    function handleFormUpload(fileList) {
      anForm.fileContents = fileList
    }

    // TODO: Move to utils
    function strDateToMil(date) {
      return dayjs(date).valueOf()
    }

    function downloadFile(file) {
      requestFile(token, file, (url) => {
        saveAs(url)
      })
    }

    function getFileName(file) {
      const arr = file.split('/')
      const len = arr.length

      return arr[len - 1]
    }

    function getNumberOfSubmits(val) {
      if (isUndefined(val)) {
        return 0
      }
      return val.length
    }

    function viewSubmits(activity_id) {
      Inertia.get(route('instructor.activity.submits', {
        classroom_id,
        activity_id,
      }))
    }

    return {
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
      viewSubmits,
    }
  },
}
</script>

<template lang="pug">
n-layout
  n-layout-content
    n-tabs(type="line")
      n-tab-pane(name="announcements", tab="Announcements")
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
                        n-button(quaternary, @click="downloadFile(file)") {{ getFileName(file) }}
      n-tab-pane(name="activities", tab="Activities")
        n-space(vertical, :item-style="wFull")
          for act in activities
            n-card(:key="act._id", :style="{...wFull, ...wMax(800), ...mxAuto}")
              template(#header)
                n-button(
                  text,
                  @click="() => viewSubmits(act._id)"
                ) {{ act.title }}
              template(#header-extra)
                n-time(:tile="strDateToMil(act.created_at)")
              n-statistic(label="Number of submits", :value="getNumberOfSubmits(act.answers)")
</template>


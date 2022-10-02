<script>
import dayjs from 'dayjs'
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
} from 'naive-ui'
import Quill from 'quill'
import { QuillEditor } from '@vueup/vue-quill'
import { Upload as UploadIcon } from '@vicons/tabler'
import { saveAs } from 'file-saver'
import {
  pXS,
  wMax,
  wFull,
  mxAuto,
  mtHalfRem,
} from '@/styles'
import { requestFile } from '@/utils'
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
    UploadIcon,
    QuillEditor,
  },
  props: {
    classroom_id: String,
    announcements: Object,
  },
  setup({ }) {

    const token = usePage().props.value.user.token

    const anForm = useForm({
      content: '',
      fileContents: []
    })

    function handleFormUpload(fileList) {
      anForm.fileContents = fileList
    }

    function convertContent(delta) {
      const cont = document.createElement('div');
      (new Quill(cont)).setContents(delta);
      return cont.getElementsByClassName('ql-editor')[0].innerHTML
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

    return {
      pXS,
      wMax,
      wFull,
      mxAuto,
      mtHalfRem,
      anForm,
      handleFormUpload,
      convertContent,
      Inertia,
      strDateToMil,
      downloadFile,
      getFileName,
    }
  },
}
</script>

<template lang="pug">
n-layout
  n-layout-content(:content-style="pXS")
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
              div(v-html="convertContent(val.content)")
            template(#footer)
              if val.fileContents.length !== 0
                div Attachments:
                n-space(vertical)
                  for file in val.fileContents
                    n-button(quaternary, @click="downloadFile(file)") {{ getFileName(file) }}
</template>


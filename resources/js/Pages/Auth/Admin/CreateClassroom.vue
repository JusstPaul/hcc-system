<script>
import dayjs from 'dayjs'
import { ref, reactive } from 'vue'
import { Inertia } from '@inertiajs/inertia'
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
  NInputNumber,
  NSelect,
  NDataTable,
  NTimePicker,
  NTime,
  NSpace,
  useNotification,
} from 'naive-ui'
import { useForm } from '@inertiajs/inertia-vue3'
import { pXS, wFull, wMax, mxAuto, mlAuto, mr } from '@/styles'
import { formatName, formatSchoolYear, formatTime } from '@/utils'
import Layout from '@/Components/Layouts/AdminLayout.vue'

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
    NInputNumber,
    NSelect,
    NDataTable,
    NTimePicker,
    NTime,
    NSpace,
  },
  props: {
    school_year: Object,
    instructors: Array,
    students: Array,
  },
  setup({ school_year, instructors, students }) {
    const notification = useNotification()

    const showCurrentSchoolYear = formatSchoolYear(school_year)

    const daySelect = [
      {
        label: 'MW',
        value: 'mw',
      },
      {
        label: 'TTh',
        value: 'tth',
      },
      {
        label: 'FS',
        value: 'fs',
      },
    ]

    const classroomForm = useForm({
      section: '',
      day: daySelect[0].value,
      room: '',
      timeStart: null,
      timeEnd: null,
      instructor: null,
      students: []
    })

    function generateTimeEnd(value) {
      classroomForm.timeEnd = dayjs(value).add(dayjs.duration({
        hours: 1,
        minutes: 30,
      })).valueOf()
    }

    const instructorSelect = instructors.map((val) => {
      const { _id } = val
      const { l_name, m_name, f_name } = val.profile

      return {
        label: formatName(l_name, m_name, f_name),
        value: _id,
      }
    })

    const studentSearch = ref('')
    const studentsTableColumns = reactive([
      {
        type: 'selection',
        key: '_id'
      },
      {
        title: 'Student ID',
        key: 'username',
      },
      {
        title: 'Name',
        key: 'name',
      },
    ])
    function studentsData() {
      if (studentSearch.value === '') {
        return students.map((val) => {
          const { username, _id } = val
          const { l_name, m_name, f_name } = val.profile

          return {
            username,
            name: formatName(l_name, m_name, f_name),
            _id
          }
        })
      }

      return students.filter((val) => {
        const searchRegex = new RegExp(studentSearch.value, 'ig')
        const { username } = val
        const { l_name, m_name, f_name } = val.profile

        return username.match(searchRegex) != null || formatName(l_name, m_name, f_name).match(searchRegex) != null
      }).map((val) => {
        const { username, _id } = val
        const { l_name, m_name, f_name } = val.profile

        return {
          username,
          name: formatName(l_name, m_name, f_name),
          _id
        }
      })
    }

    function studentsRowKey(row) {
      return row._id
    }
    function selectStudent(rowKeys) {
      classroomForm.students = rowKeys
    }

    function createUserError(error) {
      console.error(error)
      notification.error({
        title: 'Failed to create classroom',
        content: 'Please check entered data'
      })
    }

    function backLink() {
      Inertia.get(route('admin.classrooms'))
    }

    return {
      pXS,
      wFull,
      wMax,
      mxAuto,
      mlAuto,
      mr,
      wFull,
      formatTime,
      createUserError,
      backLink,
      studentsRowKey,
      selectStudent,
      showCurrentSchoolYear,
      classroomForm,
      daySelect,
      generateTimeEnd,
      instructorSelect,
      studentSearch,
      studentsTableColumns,
      studentsData,
    }
  }
}
</script>

<style scoped>
.time-end-show {
  margin-left: 8px;
  margin-right: 8px;
}
</style>

<template lang="pug">
n-layout
  n-layout-header
    n-page-header.overflow-hidden(
      title="Create Classroom",
      :subtitle="`School Year: ${showCurrentSchoolYear}`"
      @back.prevent="() => backLink()"
    )
  n-layout-content(:content-style="pXS")
    n-space.w-full(justify="center", :item-style="wFull")
      n-form(
        @submit.prevent=`classroomForm.transform((data) => ({
          ...data,
          timeStart: formatTime(data.timeStart),
          timeEnd: formatTime(data.timeEnd),
            })).post(route('post.admin.create_classroom'), {
          onError: (error) => createUserError(error),
        })`,
        :model="classroomForm",
        label-placement="left",
        require-mark-placement="right-hanging",
        label-width="120",
        :style=`{
          ...wMax(500),
          ...mxAuto,
        }`
      )
        n-form-item(label="Section" path="section" required)
          n-input(v-model:value="classroomForm.section")

        n-form-item(label="Day" path="day" required)
          n-select(v-model:value="classroomForm.day", :options="daySelect")

        n-form-item(label="Room" path="room" required)
          n-input(v-model:value="classroomForm.room")

        n-form-item(label="Time" path="time" required)
          n-time-picker(
            v-model:value="classroomForm.timeStart",
            format="h:mm a",
            @confirm="(value) => generateTimeEnd(value)"
          )
          if classroomForm.timeEnd
            span.time-end-show to
            n-time(:tile="classroomForm.timeEnd" format="h:mm a")

        n-form-item(label="Instructor" path="instructor" required)
          n-select(
            v-model:value="classroomForm.instructor",
            :options="instructorSelect",
            placeholder="Select Instructor"
          )

        n-form-item(
          required,,
          label="Select students",
          label-placement="top"
        )
          n-space(vertical, :style="wFull")
            n-input(
              placeholder="Search students",
              v-model:value="studentSearch"
            )
            //- TODO: Pagination
            n-data-table(
              :columns="studentsTableColumns",
              :data="studentsData()",
              :rowKey="studentsRowKey",
              @update:checked-row-keys="selectStudent"
            )

        n-form-item
          n-button(
            type="primary",
            attr-type="submit",
            :loading="classroomForm.processing",
            :style="{...mlAuto, ...mr(0)}"
          ) Create
</template>

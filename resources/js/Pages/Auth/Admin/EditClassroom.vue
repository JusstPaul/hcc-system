<script>
import dayjs from 'dayjs'
import { ref } from 'vue'
import {
  NLayout,
  NLayoutHeader,
  NLayoutContent,
  NPageHeader,
  NButton,
  NInput,
  NSelect,
  NForm,
  NFormItem,
  NSpace,
  NDataTable,
  NCheckbox,
  NTimePicker,
  NTime,
  NTag,
  useNotification,
} from 'naive-ui'
import { Inertia } from '@inertiajs/inertia'
import { useForm } from '@inertiajs/inertia-vue3'
import { pXS, wFull, wMax, mxAuto, tCaps, mlAuto, mr } from '@/styles'
import { formatName, formatTime } from '@/utils'
import Layout from '@/Components/Layouts/AdminLayout.vue'

export default {
  layout: Layout,
  components: {
    NLayout,
    NLayoutHeader,
    NLayoutContent,
    NPageHeader,
    NButton,
    NInput,
    NSelect,
    NForm,
    NFormItem,
    NSpace,
    NDataTable,
    NCheckbox,
    NTimePicker,
    NTime,
    NTag,
  },
  props: {
    classroom: Object,
    added_students: Array,
    instructors: Array,
    available_students: Array,
  },
  setup({ classroom, added_students, instructors, available_students }) {
    const notif = useNotification()

    function backLink() {
      Inertia.get(route('admin.classrooms'))
    }

    const { section, day, room, time_start, time_end, instructor_id } =
      classroom
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

    const dateNow = dayjs().format('YYYY/MM/DD')
    function toTimestamp(time) {
      return dayjs(`${dateNow} ${time}`, 'YYYY/MM/DD hh:mm A').valueOf()
    }

    const instructorSelect = instructors.map((val) => {
      const { _id } = val
      const { l_name, m_name, f_name } = val.profile

      return {
        label: formatName(l_name, m_name, f_name),
        value: _id,
      }
    })

    const classroomForm = useForm({
      section,
      day,
      room,
      timeStart: toTimestamp(time_start),
      timeEnd: toTimestamp(time_end),
      instructor: instructor_id,
      studentsToRemove: [],
      studentsToAdd: [],
    })

    function generateTimeEnd(value) {
      classroomForm.timeEnd = dayjs(value)
        .add(
          dayjs.duration({
            hours: 1,
            minutes: 30,
          }),
        )
        .valueOf()
    }

    const studentsTableColumns = [
      {
        type: 'selection',
        key: '_id',
      },
      {
        title: 'Student ID',
        key: 'username',
      },
      {
        title: 'Name',
        key: 'name',
      },
    ]
    function studentsRowKey(row) {
      return row._id
    }

    const addedStudentSearch = ref('')
    function addedStudentsData() {
      if (addedStudentSearch === '') {
        return added_students.map((val) => {
          const { username, _id } = val
          const { l_name, m_name, f_name } = val.profile

          return {
            username,
            name: formatName(l_name, m_name, f_name),
            _id,
          }
        })
      }

      return added_students
        .filter((val) => {
          const searchRegex = new RegExp(addedStudentSearch.value, 'ig')
          const { username } = val
          const { l_name, m_name, f_name } = val.profile

          return (
            username.match(searchRegex) != null ||
            formatName(l_name, m_name, f_name).match(searchRegex) != null
          )
        })
        .map((val) => {
          const { username, _id } = val
          const { l_name, m_name, f_name } = val.profile

          return {
            username,
            name: formatName(l_name, m_name, f_name),
            _id,
          }
        })
    }
    function selectAddedStudent(rowKeys) {
      classroomForm.studentsToRemove = rowKeys
    }

    const availableStudentSearch = ref('')
    function availableStudentsData() {
      if (availableStudentSearch === '') {
        return available_students.map((val) => {
          const { username, _id } = val
          const { l_name, m_name, f_name } = val.profile

          return {
            username,
            name: formatName(l_name, m_name, f_name),
            _id,
          }
        })
      }

      return available_students
        .filter((val) => {
          const searchRegex = new RegExp(availableStudentSearch.value, 'ig')
          const { username } = val
          const { l_name, m_name, f_name } = val.profile

          return (
            username.match(searchRegex) != null ||
            formatName(l_name, m_name, f_name).match(searchRegex) != null
          )
        })
        .map((val) => {
          const { username, _id } = val
          const { l_name, m_name, f_name } = val.profile

          return {
            username,
            name: formatName(l_name, m_name, f_name),
            _id,
          }
        })
    }
    function selectAvailableStudent(rowKeys) {
      classroomForm.studentsToAdd = rowKeys
    }

    function onError(errors) {
      console.error(errors)
      notif.error({
        title: 'Failed to update classroom',
        content: 'Please check the input field inputs properly',
        duration: 5000,
      })
    }

    return {
      backLink,
      daySelect,
      classroomForm,
      classroom_id: classroom._id,
      generateTimeEnd,
      instructorSelect,
      studentsTableColumns,
      addedStudentSearch,
      addedStudentsData,
      studentsRowKey,
      selectAddedStudent,
      availableStudentSearch,
      availableStudentsData,
      selectAvailableStudent,
      formatTime,
      onError,
      pXS,
      wFull,
      wMax,
      mxAuto,
      tCaps,
      mlAuto,
      mr,
    }
  },
}
</script>

<template lang="pug">
n-layout
  n-layout-header
    n-page-header(title="Edit Classroom", @back="() => backLink()")
  n-layout-content(:content-style="pXS")
    n-space.w-full(justify="center", :item-style="wFull")
      n-form(
        require-mark-placement="right-hanging",
        label-width="120",
        :model="classroomForm",
        :style=`{
          ...wMax(500),
          ...mxAuto,
        }`,
        @submit.prevent=`() => classroomForm.transform((data) => ({
          ...data,
          timeStart: formatTime(data.timeStart),
          timeEnd: formatTime(data.timeEnd),
        })).post(route('post.admin.edit_classroom', {
            classroom_id,
        }), {
          onError: (errors) => onError(errors)
        })`
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
            n-time(:time="classroomForm.timeEnd" format="h:mm a")

        n-form-item(label="Instructor" path="instructor" required)
          n-select(
            v-model:value="classroomForm.instructor",
            :options="instructorSelect",
            placeholder="Select Instructor"
          )

        n-form-item(
          required,
          label="Students Joined",
          label-placement="top"
        )
          n-space(vertical, :style="wFull")
            n-input(
              placeholder="Search students",
              v-model:value="addedStudentSearch"
            )
            n-tag(type="warning") Select students to remove
            //- TODO: Pagination
            n-data-table(
              :columns="studentsTableColumns",
              :data="addedStudentsData()",
              :rowKey="studentsRowKey",
              @update:checked-row-keys="selectAddedStudent"
            )

        n-form-item(
          required,
          label="Add Students",
          label-placement="top"
        )
          n-space(vertical, :style="wFull")
            n-input(
              placeholder="Search students",
              v-model:value="availableStudentSearch"
            )
            //- TODO: Pagination
            n-data-table(
              :columns="studentsTableColumns",
              :data="availableStudentsData()",
              :rowKey="studentsRowKey",
              @update:checked-row-keys="selectAvailableStudent"
            )

        n-form-item
          n-button(
            type="primary",
            attr-type="submit",
            :loading="classroomForm.processing",
            :style="{...mlAuto, ...mr(0)}"
          ) Update
</template>

<style scoped>
.time-end-show {
  margin-left: 8px;
  margin-right: 8px;
}
</style>

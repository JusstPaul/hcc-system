<template>
  <n-layout>
    <n-layout-header>
      <n-page-header :title="`School Year: ${showCurrentSchoolYear}`">
        <template #subtitle>
          <n-popconfirm @positive-click="generateSchoolYear()">
            <template #activator>
              <n-button type="warning">Generate New</n-button>
            </template>
            Generate new School Year? {{ previewSchoolYear() }}
          </n-popconfirm>
        </template>
        <n-space>
          <n-button :disabled="!has_instructors" type="primary" @click.prevent="visitCreateClassroom()">New
            Classroom</n-button>
          <n-tag v-if="!has_instructors" :bordered="false">No Instructors registered</n-tag>
        </n-space>
      </n-page-header>
      <n-layout-content content-style="padding: 24px;">
        <n-data-table :single-line="false" :bordered="false" :columns="classroomTableColumns" :data="classroomData" />
      </n-layout-content>
    </n-layout-header>
  </n-layout>
  <n-modal v-model:show="showConfirmDeleteModal" preset="dialog"
    :content="`Are you sure you want to delete classroom ${confirmDeleteSection}?`" positive-text="Confirm"
    negative-text="Cancel" @positive-click="confirmDeleteSectionPositive" />
</template>

<script>
import dayjs from 'dayjs'
import { h, ref } from 'vue'
import { Inertia } from '@inertiajs/inertia'
import {
  NLayout,
  NLayoutContent,
  NLayoutHeader,
  NH3,
  NButton,
  NIcon,
  NDataTable,
  NPageHeader,
  NPopconfirm,
  NTag,
  NSpace,
  NModal,
} from 'naive-ui'
import { Trash as TrashIcon } from '@vicons/tabler'
import { formatSchoolYear, formatName, formatTime } from '@/utils'
import Layout from '@/Components/Layouts/AdminLayout.vue'

export default {
  layout: Layout,
  components: {
    NLayout,
    NLayoutContent,
    NLayoutHeader,
    NH3,
    NButton,
    NIcon,
    NDataTable,
    NPageHeader,
    NPopconfirm,
    NTag,
    NSpace,
    NModal,
  },
  props: {
    school_year: Object,
    classrooms: Array,
    has_instructors: Boolean,
  },
  setup({ classrooms, school_year }) {

    const showConfirmDeleteModal = ref(false)
    const confirmDeleteSection = ref('')
    const confirmDeleteSectionId = ref('')
    function confirmDelete(row) {
      confirmDeleteSection.value = row.section
      confirmDeleteSectionId.value = row.key
      showConfirmDeleteModal.value = true
    }
    function confirmDeleteSectionPositive() {
      Inertia.post(route('post.delete_classroom', {
        classroom_id: confirmDeleteSectionId.value,
      }), undefined, {
        onFinish: () => location.reload(),
        preserveScroll: true,
      })
    }

    const classroomTableColumns = [
      {
        title: 'Section',
        key: 'section',
      },
      {
        title: 'Instructor',
        key: 'instructor',
      },
      {
        title: 'Room',
        key: 'room',
      },
      {
        title: 'Time',
        key: 'time',
      },
      {
        title: 'Days',
        key: 'days'
      },
      {
        title: 'Action',
        key: 'action',
        render(row) {
          return h(NSpace, {}, {
            default: () => [
              h(NButton, {
                quaternary: true,
                type: 'error',
                class: 'btn-icon',
                onClick: () => confirmDelete(row),
              }, {
                default: () => h(NIcon, {}, {
                  default: () => h(TrashIcon)
                })
              })
            ]
          })
        }
      }
    ]
    console.log(classrooms)
    const classroomData = classrooms.map(({ _id, section, instructor, room, time_start, time_end, day }) => ({
      'key': _id,
      'section': section,
      'instructor': formatName(instructor.profile.l_name, instructor.profile.m_name, instructor.profile.f_name),
      'room': room,
      'time': `${time_start} to ${time_end}`,
      'days': day.toUpperCase(),
    }))


    const showCurrentSchoolYear = formatSchoolYear(school_year)

    function previewSchoolYear() {
      return `${dayjs().year()} to ${dayjs().add(1, 'y').year()}`;
    }

    function generateSchoolYear() {
      Inertia.post(route('post.admin.school_year'), undefined, {
        onSuccess: () => location.reload(),
      })
    }

    function visitCreateClassroom() {
      Inertia.get(route('admin.create_classroom'));
    }

    return {
      showConfirmDeleteModal,
      confirmDeleteSection,
      confirmDeleteSectionPositive,
      classroomTableColumns,
      classroomData,
      showCurrentSchoolYear,
      previewSchoolYear,
      generateSchoolYear,
      visitCreateClassroom,
    }
  }
}
</script>


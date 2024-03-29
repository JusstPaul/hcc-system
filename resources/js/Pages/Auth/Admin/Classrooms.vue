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
  NInput,
} from 'naive-ui'
import { Trash as TrashIcon, Edit as EditIcon } from '@vicons/tabler'
import { pXS } from '@/styles'
import { formatSchoolYear, formatName } from '@/utils'
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
    NInput,
  },
  props: {
    school_year: Object,
    classrooms: Array,
    has_instructors: Boolean,
  },
  setup({ classrooms, school_year }) {
    const searchContent = ref('')

    const showConfirmDeleteModal = ref(false)
    const confirmDeleteSection = ref('')
    const confirmDeleteSectionId = ref('')
    function confirmDelete(row) {
      confirmDeleteSection.value = row.section
      confirmDeleteSectionId.value = row.key
      showConfirmDeleteModal.value = true
    }
    function confirmDeleteSectionPositive() {
      Inertia.post(
        route('post.delete_classroom', {
          classroom_id: confirmDeleteSectionId.value,
        }),
        undefined,
        {
          onFinish: () => location.reload(),
          preserveScroll: true,
        },
      )
    }

    function editClassroom(id) {
      Inertia.get(
        route('admin.edit_classroom', {
          classroom_id: id,
        }),
      )
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
        key: 'days',
      },
      {
        title: 'Action',
        key: 'action',
        render(row) {
          return h(
            NSpace,
            {},
            {
              default: () => [
                h(
                  NButton,
                  {
                    quaternary: true,
                    type: 'primary',
                    class: 'btn-icon',
                    onClick: () => editClassroom(row.key),
                  },
                  {
                    default: () =>
                      h(
                        NIcon,
                        {},
                        {
                          default: () => h(EditIcon),
                        },
                      ),
                  },
                ),
                h(
                  NButton,
                  {
                    quaternary: true,
                    type: 'error',
                    class: 'btn-icon',
                    onClick: () => confirmDelete(row),
                  },
                  {
                    default: () =>
                      h(
                        NIcon,
                        {},
                        {
                          default: () => h(TrashIcon),
                        },
                      ),
                  },
                ),
              ],
            },
          )
        },
      },
    ]
    const classroomData = () => {
      if (searchContent.value === '')
        return classrooms.map(
          ({ _id, section, instructor, room, time_start, time_end, day }) => ({
            key: _id,
            section: section,
            instructor: formatName(
              instructor.profile.l_name,
              instructor.profile.m_name,
              instructor.profile.f_name,
            ),
            room: room,
            time: `${time_start} to ${time_end}`,
            days: day.toUpperCase(),
          }),
        )

      const searchRegex = new RegExp(searchContent.value, 'ig')
      return classrooms
        .filter(({ section, instructor, room, time_start, time_end, day }) => {
          const name = formatName(
            instructor.profile.l_name,
            instructor.profile.m_name,
            instructor.profile.f_name,
          )

          return !(
            section.match(searchRegex) === null &&
            name.match(searchRegex) === null &&
            room.match(searchRegex) === null &&
            time_start.match(searchRegex) === null &&
            time_end.match(searchRegex) === null &&
            day.match(searchRegex) === null
          )
        })
        .map(
          ({ _id, section, instructor, room, time_start, time_end, day }) => ({
            key: _id,
            section: section,
            instructor: formatName(
              instructor.profile.l_name,
              instructor.profile.m_name,
              instructor.profile.f_name,
            ),
            room: room,
            time: `${time_start} to ${time_end}`,
            days: day.toUpperCase(),
          }),
        )
    }

    const showCurrentSchoolYear = formatSchoolYear(school_year)

    function previewSchoolYear() {
      return `${dayjs().year()} to ${dayjs().add(1, 'y').year()}`
    }

    function generateSchoolYear() {
      Inertia.post(route('post.admin.school_year'), undefined, {
        onSuccess: () => location.reload(),
      })
    }

    function visitCreateClassroom() {
      Inertia.get(route('admin.create_classroom'))
    }

    return {
      pXS,
      showConfirmDeleteModal,
      confirmDeleteSection,
      confirmDeleteSectionPositive,
      classroomTableColumns,
      classroomData,
      showCurrentSchoolYear,
      previewSchoolYear,
      generateSchoolYear,
      visitCreateClassroom,
      searchContent,
    }
  },
}
</script>

<template lang="pug">
n-layout
  n-layout-header
    n-page-header(:title="`School Year: ${showCurrentSchoolYear}`")
      template(#subtitle)
        n-popconfirm(@positive-click.prevent="() => generateSchoolYear()")
          template(#activator)
            n-button(type="warning") Generate New
          |Generate New School Year? {{previewSchoolYear()}}
          if school_year != null
            br
            |Doing so will archive the current School Year
      template(#extra)
        n-input(placeholder="Search", v-model:value="searchContent")
      n-space(align="baseline")
        n-button(
          :disabled="!has_instructors || school_year == null",
          type="primary",
          @click.prevent="() => visitCreateClassroom()"
        ) New Classroom
        n-tag(
          v-if="school_year == null",
          :bordered="false",
          type="warning"
        )
          |No School Year generated
        n-tag(
          v-if="!has_instructors",
          :bordered="false",
          type="warning"
        )
          |No Instructor registered

  n-layout-content(:content-style="pXS")
    n-data-table(
      :single-line="false",
      :bordered="false",
      :columns="classroomTableColumns",
      :data="classroomData()",
      :pagination=`{
        pageSize: 10,
        showQuickJumper: true
      }`
    )
n-modal(
  v-model:show="showConfirmDeleteModal",
  preset="dialog",
  :content="`Are you sure you want to delete classroom ${confirmDeleteSection}?`",
  negative-text="Cancel",
  positive-text="Confirm",
  @positive-click="() => confirmDeleteSectionPositive()"
)
</template>

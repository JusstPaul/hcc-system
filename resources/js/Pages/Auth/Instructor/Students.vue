<script>
import { ref, h } from 'vue'
import { Inertia } from '@inertiajs/inertia'
import mobile from 'is-mobile'
import {
  NLayout,
  NLayoutContent,
  NLayoutHeader,
  NPageHeader,
  NDataTable,
  NInput,
  NInputGroup,
  NScrollbar,
  NSpace,
  NButton,
  NIcon,
  NModal,
} from 'naive-ui'
import { CircleMinus as CircleMinusIcon } from '@vicons/tabler'
import { formatName } from '@/utils'
import Layout from '@/Components/Layouts/InstructorLayout.vue'

export default {
  layout: Layout,
  components: {
    NLayout,
    NLayoutContent,
    NPageHeader,
    NLayoutHeader,
    NDataTable,
    NInput,
    NInputGroup,
    NScrollbar,
    NButton,
    NModal,
  },
  props: {
    classroom_id: String,
    students: Array,
  },
  setup({ classroom_id, students }) {
    const searchContent = ref('')

    const showConfirmRemoveModal = ref(false)
    const confirmRemoveStudent = ref('')
    const confirmRemoveStudentId = ref('')
    function confirmRemove(row) {
      console.log(row)

      confirmRemoveStudent.value = row.name
      confirmRemoveStudentId.value = row.key
      showConfirmRemoveModal.value = true
    }

    function confirmRemoveStudentPositive() {
      Inertia.post(
        route('post.instructor.students_remove', {
          classroom_id,
          student_id: confirmRemoveStudentId.value,
        }),
        undefined,
        {
          onFinish: () => location.reload(),
          preserveScroll: true,
        },
      )
    }

    const studentsTableColumns = [
      {
        title: 'ID',
        key: 'id',
      },
      {
        title: 'Name',
        key: 'name',
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
                    type: 'error',
                    class: 'btn-icon',
                  },
                  {
                    default: () =>
                      h(
                        NIcon,
                        {
                          onClick: () => confirmRemove(row),
                        },
                        {
                          default: () => h(CircleMinusIcon),
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
    const studentsTableData = () => {
      if (searchContent.value === '')
        return students.map((val) => {
          const { username } = val
          const { l_name, m_name, f_name } = val.profile

          return {
            key: val._id,
            id: username,
            name: formatName(l_name, m_name, f_name),
          }
        })

      const searchRegex = new RegExp(searchContent.value, 'ig')
      return students
        .filter(({ profile, username }) => {
          const { l_name, m_name, f_name } = profile
          const name = formatName(l_name, m_name, f_name)

          return !(
            name.match(searchRegex) === null &&
            username.match(searchRegex) === null
          )
        })
        .map((val) => {
          const { username } = val
          const { l_name, m_name, f_name } = val.profile

          return {
            id: username,
            name: formatName(l_name, m_name, f_name),
            showConfirmRemoveModal,
            confirmRemoveStudent,
            confirmRemoveStudentId,
          }
        })
    }

    function backLink() {
      Inertia.get(
        route('instructor.classroom', {
          classroom_id,
        }),
      )
    }

    return {
      studentsTableColumns,
      studentsTableData,
      backLink,
      searchContent,
      mobile,
      showConfirmRemoveModal,
      confirmRemoveStudent,
      confirmRemoveStudentPositive,
    }
  },
}
</script>

<template lang="pug">
n-layout
  n-layout-header
    n-page-header(
      title="Students",
      @back="backLink()",
      style="overflow: hidden"
    )
      template(#extra)
        n-input-group
          n-input(placeholder="Search", v-model:value="searchContent")
  n-layout-content(
    content-style="padding: 24px;"
  )
    n-scroll-bar(:x-scrollable="true")
      n-data-table(
        :single-line="false",
        :bordered="false",
        :columns="studentsTableColumns",
        :data="studentsTableData()",
        :pagination=`{
          pageSize: 10,
          simple: mobile()
        }`
      )

n-modal(
  v-model:show="showConfirmRemoveModal",
  preset="dialog",
  :content="`Are you sure you want to remove student ${confirmRemoveStudent}?`",
  negative-text="Cancel",
  positive-text="Confirm",
  @positive-click="() => confirmRemoveStudentPositive()"
)
</template>

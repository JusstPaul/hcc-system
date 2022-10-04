<script>
import dayjs from 'dayjs'
import { h } from 'vue'
import { isUndefined } from 'lodash'
import { isNumeric } from 'lodash-contrib'
import { Inertia } from '@inertiajs/inertia'
import {
  NLayout,
  NLayoutContent,
  NLayoutHeader,
  NButton,
  NButtonGroup,
  NPageHeader,
  NTime,
  NDataTable,
} from 'naive-ui'
import {
  pXS,
  mxHalfRem,
} from '@/styles'
import { formatName } from '@/utils'
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
    NDataTable,
  },
  props: {
    classroom_id: String,
    activity: Object,
    submits: Object,
  },
  setup({ activity, submits, classroom_id }) {
    const submitsColumns = [
      {
        title: 'Student ID',
        key: 'username',
      },
      {
        title: 'Name',
        key: 'name',
      },
      {
        title: 'Submitted At',
        key: 'created_at',
      },
      {
        title: 'Score',
        key: 'score',
      },
      {
        title: 'Is Late',
        key: 'is_late',
      },
      {
        title: 'Action',
        key: 'action',
        render(row) {
          return h(NButton, {
            secondary: true,
            class: 'w-full',
            type: 'primary',
            onClick: () => {
              Inertia.get(route('instructor.activity.submits.answer', {
                classroom_id,
                activity_id: activity._id,
                answer_id: row.key,
              }))
            }
          }, {
            default: () => 'View'
          })
        }
      }
    ]

    const submitsData = submits.map((val) => {
      const { profile, username, created_at } = val.student
      const { l_name, m_name, f_name } = profile

      const name = formatName(l_name, m_name, f_name)
      const create = dayjs(created_at)

      const deadMill = parseInt(activity.deadline)
      const deadline = dayjs(deadMill)

      console.log(val.answers.checks)
      function getScore() {
        let score_acc = 0
        let total_acc = 0

        val.answers.checks.flat().map(({ score, total }) => {
          score_acc += parseInt(score)
          total_acc += parseInt(total)
        })

        return `${score_acc}/${total_acc}`
      }

      const is_late = create.valueOf() > deadline.valueOf()
      const score = isUndefined(val.answers.checks) ? 'Unchecked' : getScore()

      return {
        key: val.answer_id,
        username,
        name,
        score,
        is_late: is_late ? 'Yes' : 'No',
        created_at: create.format('MM/DD/YYYY hh:mm A'),
      }
    })


    function backLink() {
      Inertia.get(route('instructor.classroom', {
        classroom_id
      }))
    }

    return {
      backLink,
      title: activity.title,
      deadline: parseInt(activity.deadline),
      start: parseInt(activity.start),
      submitsColumns,
      submitsData,
      mxHalfRem,
      pXS,
    }
  }
}
</script>

<template lang="pug">
n-layout-header
  n-page-header.overflow-hidden(
    :title="title",
    @back="() => backLink()"
  )
    template(#subtitle)
      n-time(:time="start", format="MM/dd/yyyy hh:mm a")
      span(:style="mxHalfRem") to
      n-time(:time="deadline", format="MM/dd/yyyy hh:mm a")
  n-layout-content(:content-style="pXS")
    n-data-table(
      :single-line="false",
      :bordered="false",
      :columns="submitsColumns",
      :data="submitsData"
    )
</template>

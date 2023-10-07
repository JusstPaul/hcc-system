<script>
import mobile from 'is-mobile'
import {
  NLayout,
  NLayoutHeader,
  NPageHeader,
  NLayoutContent,
  NDataTable,
} from 'naive-ui'
import { pXS } from '@/styles'
import { formatName } from '@/utils'
import Layout from '@/Components/Layouts/StudentLayout.vue'

export default {
  layout: Layout,
  components: {
    NLayout,
    NLayoutHeader,
    NPageHeader,
    NLayoutContent,
    NDataTable,
  },
  props: {
    students: Array,
  },
  setup({ students }) {
    const studentsTableColumns = [
      {
        title: 'ID',
        key: 'id',
      },
      {
        title: 'Name',
        key: 'name',
      },
    ]
    const studentsTableData = students.map((val) => {
      const { username } = val
      const { l_name, m_name, f_name } = val.profile

      return {
        id: username,
        name: formatName(l_name, m_name, f_name),
      }
    })

    return {
      pXS,
      studentsTableColumns,
      studentsTableData,
      mobile,
    }
  },
}
</script>

<template lang="pug">
n-layout
  n-layout-header
    n-page-header.overflow-hidden(title="Students")
  n-layout-content(:content-style="pXS")
    n-data-table(
      :single-line="false",
      :bordered="false",
      :columns="studentsTableColumns",
      :data="studentsTableData",
      :pagination=`{
        pageSize: 10,
        simple: mobile()
      }`
    )
</template>

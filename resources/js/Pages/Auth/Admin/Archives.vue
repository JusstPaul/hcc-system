<script>
import {
  NLayout,
  NLayoutContent,
  NLayoutHeader,
  NPageHeader,
  NDataTable,
  NSpace,
  NInputGroup,
  NInput,
} from 'naive-ui'
import { pXS } from '@/styles'
import Layout from '@/Components/Layouts/AdminLayout.vue'

export default {
  layout: Layout,
  components: {
    NLayout,
    NLayoutContent,
    NLayoutHeader,
    NPageHeader,
    NDataTable,
    NSpace,
    NInputGroup,
    NInput,
  },
  props: {
    school_years: Array,
  },
  setup({ school_years }) {
    console.log(school_years)

    const archiveTableColumns = [
      {
        title: 'School Year',
        key: 'schoolyear',
      },
    ]

    const archiveTableData = () => {
      return school_years.map((val) => {
        return {
          key: val._id,
          schoolyear: `${val.start} to ${val.end}`,
        }
      })
    }

    return {
      archiveTableColumns,
      archiveTableData,
      pXS,
    }
  },
}
</script>

<template lang="pug">
n-layout
  n-layout-header
    n-page-header(title="Archives")
  n-layout-content(:content-style="pXS")
    n-data-table(
      :single-line="false",
      :bordered="false",
      :columns="archiveTableColumns"
      :data="archiveTableData()",
      :pagination=`{
        pageSize: 10,
        showQuickJumper: true
      }`
    )
</template>

<script>
import { Inertia } from '@inertiajs/inertia'
import {
  NLayout,
  NLayoutContent,
  NLayoutHeader,
  NPageHeader,
  NH2,
  NButton,
  NIcon,
  NDataTable,
} from 'naive-ui'
import { UserPlus } from '@vicons/tabler'
import { formatName } from '@/utils'
import { pXS } from '@/styles'
import Layout from '@/Components/Layouts/AdminLayout.vue'

export default {
  layout: Layout,
  components: {
    NLayout,
    NLayoutContent,
    NLayoutHeader,
    NPageHeader,
    NH2,
    NButton,
    NIcon,
    NDataTable,
    UserPlusIcon: UserPlus,
  },
  props: {
    users: Array,
  },
  setup(props) {
    const userTableColumns = [
      {
        title: 'Username',
        key: 'username'
      },
      {
        title: 'Name',
        key: 'name'
      },
      {
        title: 'Role',
        key: 'role'
      },
      {
        title: 'Action',
        key: 'action'
      }
    ];
    const userTableData = props.users.map((val) => {
      const { username, role_name } = val
      const { l_name, m_name, f_name } = val.profile
      const role = role_name.charAt(0).toUpperCase() + role_name.slice(1)

      return {
        username,
        name: formatName(l_name, m_name, f_name),
        role,
        action: 'None'
      }
    })

    function createUserLink() {
      Inertia.get(route('admin.create_user'))
    }

    return {
      userTableColumns,
      userTableData,
      createUserLink,
      pXS
    }
  }
}
</script>

<template lang="pug">
n-layout
  n-layout-header
    n-page-header(title="Users")
      template(#subtitle)
        n-button(type="primary", @click="() => createUserLink()")
          template(#icon)
            n-icon
              user-plus-icon
          |New User
  n-layout-content(:content-style="pXS")
    n-data-table(
      :single-line="false",
      :bordered="false",
      :columns="userTableColumns",
      :data="userTableData",
    )
</template>

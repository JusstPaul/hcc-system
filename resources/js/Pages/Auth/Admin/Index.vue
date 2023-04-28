<script>
import { h, ref } from 'vue'
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
  NSpace,
  NModal,
  NInputGroup,
  NInput,
} from 'naive-ui'
import { UserPlus, Trash as TrashIcon, Edit as EditIcon } from '@vicons/tabler'
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
    NModal,
    NInputGroup,
    NInput,
    UserPlusIcon: UserPlus,
  },
  props: {
    users: Array,
    roles: Array,
  },
  setup(props) {
    const searchContent = ref('')

    const showConfirmDeleteModal = ref(false)
    const confirmDeleteUser = ref('')
    const confirmDeleteUserId = ref('')
    function confirmDelete(row) {
      confirmDeleteUser.value = row.name
      confirmDeleteUserId.value = row.key
      showConfirmDeleteModal.value = true
    }
    function confirmDeleteUserPositive() {
      Inertia.post(
        route('post.admin.delete_user', {
          user_id: confirmDeleteUserId.value,
        }),
        undefined,
        {
          onFinish: () => location.reload(),
          preserveScroll: true,
        },
      )
    }

    function editUser(row) {
      Inertia.get(
        route('admin.edit_user', {
          user_id: row.key,
        }),
      )
    }

    const userTableColumns = [
      {
        title: 'Username',
        key: 'username',
      },
      {
        title: 'Name',
        key: 'name',
      },
      {
        title: 'Role',
        key: 'role',
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
                  },
                  {
                    default: () =>
                      h(
                        NIcon,
                        {
                          onClick: () => editUser(row),
                        },
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

    const userTableData = () => {
      if (searchContent.value === '')
        return props.users.map((val) => {
          const { username, role_name } = val
          const { l_name, m_name, f_name } = val.profile
          const role = props.roles.filter((item) => {
            return item._id === val.role_ids[0]
          })[0].name

          return {
            key: val._id,
            username,
            name: formatName(l_name, m_name, f_name),
            role,
            action: 'None',
          }
        })

      const searchRegex = new RegExp(searchContent.value, 'ig')
      return props.users
        .filter((val) => {
          const { l_name, m_name, f_name } = val.profile
          const name = formatName(l_name, m_name, f_name)

          return !(
            name.match(searchRegex) === null &&
            val.username.match(searchRegex) === null
          )
        })
        .map((val) => {
          const { username, role_name } = val
          const { l_name, m_name, f_name } = val.profile
          const role = props.roles.filter((item) => {
            return item._id === val.role_ids[0]
          })[0].name

          return {
            key: val._id,
            username,
            name: formatName(l_name, m_name, f_name),
            role,
            action: 'None',
          }
        })
    }

    function createUserLink() {
      Inertia.get(route('admin.create_user'))
    }

    return {
      userTableColumns,
      userTableData,
      createUserLink,
      showConfirmDeleteModal,
      confirmDeleteUser,
      confirmDeleteUserPositive,
      pXS,
      users: props.users,
      jumpTo: Inertia.get,
      searchContent,
    }
  },
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
      template(#extra)
        n-input-group
          n-input(placeholder="Search", v-model:value="searchContent")
  n-layout-content(:content-style="pXS")
    n-data-table(
      :single-line="false",
      :bordered="false",
      :columns="userTableColumns",
      :data="userTableData()",
      :pagination=`{
        pageSize: 10,
        showQuickJumper: true
      }`
    )
n-modal(
  v-model:show="showConfirmDeleteModal",
  preset="dialog",
  :content="`Are you sure you want to delete user ${confirmDeleteUser}?`",
  negative-text="Cancel",
  positive-text="Confirm",
  @positive-click="() => confirmDeleteUserPositive()"
)
</template>

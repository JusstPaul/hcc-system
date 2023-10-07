<script>
import { computed } from 'vue'
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
  useNotification,
} from 'naive-ui'
import { Inertia } from '@inertiajs/inertia'
import { useForm } from '@inertiajs/inertia-vue3'
import { pXS, wFull, wMax, mxAuto, tCaps, mlAuto, mr } from '@/styles'
import { allowNumberOnly } from '@/utils'
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
  },
  props: {
    user_edit: Object,
    roles: Object,
  },
  setup({ user_edit, roles }) {
    const { username, role, _id } = user_edit
    const { l_name, m_name, f_name, details } = user_edit.profile

    const notif = useNotification()

    const userForm = useForm({
      username,
      role,
      lName: l_name,
      mName: m_name,
      fName: f_name,
      //* HACK this is a string for some reason
      details: JSON.parse(details),
    })

    const roleOptions = computed(() =>
      roles.map((value) => ({
        label: value,
        value,
      })),
    )

    function backLink() {
      Inertia.get(route('admin.index'))
    }

    function onError(errors) {
      console.error(errors)
      notif.error({
        title: 'Failed to update user',
        content: 'Please check the field inputs properly',
        duration: 5000,
      })
    }

    return {
      backLink,
      userForm,
      roleOptions,
      user_id: _id,
      onError,
      pXS,
      wFull,
      wMax,
      mxAuto,
      tCaps,
      mlAuto,
      mr,
      allowNumberOnly,
    }
  },
}
</script>

<template lang="pug">
n-layout
  n-layout-header
    n-page-header(title="Edit User", @back="() => backLink()")
  n-layout-content(:content-style="pXS")
    n-space.w-full(justify="center", :item-style="wFull")
      n-form(
        require-mark-placement="right-hanging",
        label-width="120",
        :model="userForm",
        :style=`{
          ...wMax(500),
          ...mxAuto,
        }`,
        @submit.prevent=`() => userForm.post(route('post.admin.update_user', {
            user_id
        }), {
          onError: (errors) => onError(errors)
        })`
      )

        n-form-item(
          required,
          label="Username",
          path="username"
        )
          n-input(v-model:value="userForm.username")

        n-form-item(
          required,
          label="Role",
          path="role",
          :style="tCaps"
        )
          n-select(
            v-model:value="userForm.role",
            :options="roleOptions"
          )

        n-form-item(
          required,
          label="Last Name",
          path="lName"
        )
          n-input(v-model:value="userForm.lName")
        n-form-item(
          required,
          label="Middle Name",
          path="mName",
        )
          n-input(v-model:value="userForm.mName")
        n-form-item(
          required,
          label="First Name",
          path="fName",
        )
          n-input(v-model:value="userForm.fName")

          // Additional fields
        n-form-item(
          v-if="userForm.role === 'instructor' || userForm.role === 'student'"
          required,
          label="Contact Number",
          path="details.contact",
          :maxlength="11",
        )
          n-input(
            v-model:value="userForm.details.contact",
            :allow-input="allowNumberOnly"
          )

        n-form-item(
          v-if="userForm.role === 'instructor' || userForm.role === 'student'"
          required,
          label="Email",
          path="details.email"
        )
          n-input(v-model:value="userForm.details.email")

        n-form-item(
          v-if="userForm.role === 'student'"
          required,
          label="Contact Person",
          path="details.contactPerson"
        )
          n-input(v-model:value="userForm.details.contactPerson")

        n-form-item(
          v-if="userForm.role === 'student'"
          required,
          label="Contact Number",
          path="details.contactPersonContact"
        )
          n-input(
            v-model:value="userForm.details.contactPersonContact",
            :allow-input="allowNumberOnly"
          )

        n-form-item
          n-button(
            type="primary",
            attr-type="submit",
            :loading="userForm.processing",
            :style="{...mlAuto, ...mr(0)}"
          ) Update
</template>

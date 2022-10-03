<script>
import {
  NLayout,
  NLayoutHeader,
  NLayoutContent,
  NForm,
  NFormItem,
  NInput,
  NSpace,
  NButton,
  NSwitch,
  NPageHeader,
  NDataTable,
  useNotification,
} from 'naive-ui'
import { usePage, useForm } from '@inertiajs/inertia-vue3'
import { pXS, wFull, wMax, mxAuto, mlAuto, mr } from '@/styles'

export default {
  components: {
    NLayout,
    NLayoutHeader,
    NLayoutContent,
    NForm,
    NFormItem,
    NInput,
    NSpace,
    NButton,
    NSwitch,
    NPageHeader,
    NDataTable,
  },
  props: {
    profile: Object,
  },
  setup({ profile }) {
    const user = usePage().props.value.user

    const notif = useNotification()

    function notifyError() {
      notif.error({
        title: 'Error',
        content: 'Failed to change Profile',
      })
    }

    const { l_name, m_name, f_name } = profile
    const profileForm = useForm({
      lName: l_name,
      mName: m_name,
      fName: f_name,
      changePassword: false,
      oPassword: '',
      nPassword: '',
    })

    const columns = [
      {
        title: 'Username',
        key: 'username'
      },
      {
        title: 'Role',
        key: 'role'
      }
    ]

    const data = [
      {
        username: user.username,
        role: user.role
      }
    ]

    return {
      pXS,
      wFull,
      wMax,
      mxAuto,
      mlAuto,
      mr,
      profileForm,
      id: user._id,
      columns,
      data,
      notifyError,
    }
  }
}
</script>

<template lang="pug">
n-layout
  n-layout-header
    n-page-header(title="Profile")
  n-layout-content(content-style="pXS")
    n-space.w-full(justify="center", :item-style="wFull")
      n-form(
        @submit.prevent=`() => profileForm.post(route('post.profile.update', {
          id,
        }), {
          onError: () => notifyError(),
        })`,
        :model="profileForm"
        require-mark-placement="right-hanging",
        label-width="120",
        :style=`{
          ...wMax(500),
          ...mxAuto,
        }`
      )

        n-form-item
          n-data-table(:columns="columns", :data="data", :single-line="false")

        n-form-item(
          required,
          label="Last Name",
          path="lName"
        )
          n-input(v-model:value="profileForm.lName")

        n-form-item(
          required,
          label="First Name",
          path="fName"
        )
          n-input(v-model:value="profileForm.fName")

        n-form-item(
          required,
          label="Middle Name",
          path="mName"
          placeholder=""
        )
          n-input(v-model:value="profileForm.mName")

        if role === 'instructor' || role === 'student'
          div Instructor and Student

        if role === 'student'
          div Student Only

        n-form-item(label="Change Password")
          n-switch(v-model:value="profileForm.changePassword")

        if profileForm.changePassword
          n-form-item(label="Current Password", path="oPassword")
            n-input(
              v-model:value="profileForm.oPassword"
              type="password",
              show-password-on="click"
            )
          n-form-item(label="New Password", path="nPassword")
            n-input(
              v-model:value="profileForm.nPassword"
              type="password",
              show-password-on="click"
            )

        n-form-item
          n-button(
            type="primary",
            attr-type="submit",
            :loading="profileForm.processing",
            :style="{...mlAuto, ...mr(0)}"
          ) Submit
</template>

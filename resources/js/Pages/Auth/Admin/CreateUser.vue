<script>
import { Inertia } from '@inertiajs/inertia'
import {
  NLayout,
  NLayoutContent,
  NLayoutHeader,
  NH2,
  NButton,
  NPageHeader,
  NForm,
  NFormItem,
  NInput,
  NInputNumber,
  NSelect,
  NSpace,
} from 'naive-ui'
import { useForm } from '@inertiajs/inertia-vue3'
import { allowNumberOnly } from '@/utils'
import {
  pXS,
  wMax,
  wFull,
  tCaps,
  mxAuto,
  mlAuto,
  mr,
} from '@/styles'
import Layout from '@/Components/Layouts/AdminLayout.vue'

export default {
  layout: Layout,
  components: {
    NLayout,
    NLayoutContent,
    NLayoutHeader,
    NH2,
    NButton,
    NPageHeader,
    NForm,
    NFormItem,
    NInput,
    NInputNumber,
    NSelect,
    NSpace,
  },
  props: {
    roles: Array
  },
  setup({ roles }) {
    const roleOptions = roles.map((value) => ({
      label: value,
      value
    }))

    const userForm = useForm({
      username: '',
      role: roles[0],
      lName: '',
      mName: '',
      fName: '',
      details: {
        contact: null,
        email: '',
        contactPerson: '',
        contactPersonContact: null,
      },
    })

    function backLink() {
      Inertia.get(route('admin.index'))
    }

    return {
      allowNumberOnly,
      userForm,
      backLink,
      roleOptions,
      pXS,
      wMax,
      wFull,
      tCaps,
      mxAuto,
      mlAuto,
      mr,
    }
  }
}
</script>

<template lang="pug">
n-layout
  n-layout-header
    n-page-header.overflow-hidden(
      title="Create New User",
      @back="() => backLink()",
    )
    n-layout-content(:content-style="pXS")
      n-space.w-full(justify="center", :item-style="wFull")
        n-form(
          @submit.prevent="() => userForm.post(route('post.admin.create_user'))",
          require-mark-placement="right-hanging",
          label-width="120",
          :model="userForm",
          :style=`{
            ...wMax(500),
            ...mxAuto,
          }`
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
            path="lName",
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
            path="details.contact"
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
            ) Submit
</template>

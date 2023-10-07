<script>
import { computed, ref } from 'vue'
import { useAsyncState } from '@vueuse/core'
import { isUndefined, isString } from 'lodash'
import { Inertia } from '@inertiajs/inertia'
import { UserCircle as UserCircleIcon } from '@vicons/tabler'
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
  NCard,
  NAvatar,
  NIcon,
  NUpload,
  NH2,
  useNotification,
} from 'naive-ui'
import { usePage, useForm } from '@inertiajs/inertia-vue3'
import { pXS, wFull, wMax, mxAuto, mlAuto, mr } from '@/styles'
import { allowNumberOnly, keyToJpeg } from '@/utils'

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
    NCard,
    NAvatar,
    NIcon,
    NH2,
    NUpload,
    UserCircleIcon,
  },
  props: {
    profile: Object,
  },
  setup({ profile }) {
    const user = usePage().props.value.user

    const notif = useNotification()

    function notifyError(errors) {
      notif.error({
        title: 'Error',
        content: 'Failed to change Profile',
        duration: 5000,
      })
      console.error(errors)
    }

    const { l_name, m_name, f_name, details, avatar } = profile
    const profileForm = useForm({
      avatar: avatar,
      lName: l_name,
      mName: m_name,
      fName: f_name,
      changePassword: false,
      oPassword: '',
      nPassword: '',
      details: details,
    })

    const avatarIsKey = ref(isString(avatar))
    const avatarURL = ref(avatarIsKey && useAsyncState(keyToJpeg(user.token, avatar)))

    function setAvatar(file) {
      avatarIsKey.value = false
      profileForm.avatar = file
    }

    function viewFile(file) {
      if (isString(file)) {
        avatarIsKey.value = true
        avatarURL.value = useAsyncState(keyToJpeg(user.token, file))
      } else {
        return URL.createObjectURL(file.file)
      }
    }

    return {
      pXS,
      wFull,
      wMax,
      mxAuto,
      mlAuto,
      mr,
      profileForm,
      id: user._id,
      notifyError,
      role: user.role,
      username: user.username,
      allowNumberOnly,
      Inertia,
      hasAvatar: computed(() => !(isUndefined(profileForm.avatar) || profileForm.avatar == null)),
      setAvatar,
      viewFile,
      avatarIsKey,
      avatarURL,
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
          _method: 'put',
          onError: (errors) => notifyError(errors),
          onSuccess: () => Inertia.get('/')
        })`,
        :model="profileForm"
        require-mark-placement="right-hanging",
        label-width="120",
        :style=`{
          ...wMax(500),
          ...mxAuto,
        }`
      )

        n-form-item(:show-feedback="false", :show-label="false")
          n-space.w-full.text-center(vertical)
            n-space.w-full(justify="center")
              if !hasAvatar
                n-avatar(round, :size="100")
                  n-icon 
                    user-circle-icon
              else
                if avatarIsKey
                  if avatarURL.isLoading
                    n-avatar(round, :size="100")
                      n-icon 
                        user-circle-icon
                  else
                    n-avatar(
                      round,
                      object-fit="scale-down",
                      :size="100",
                      :src="avatarURL.state"
                    )
                else
                  n-avatar(
                    round,
                    object-fit="scale-down",
                    :size="100",
                    :src="viewFile(profileForm.avatar)"
                  )
        n-form-item.text-center(:show-label="false")
          .text-center.w-full
            n-h2 {{ username }}
            span Role: {{ role }}

        n-form-item(:show-label="false")
          n-upload(@change="({ file }) => setAvatar(file)", :max="1")
            n-button Change Avatar

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
        )
          n-input(v-model:value="profileForm.mName")

        if role === 'instructor' || role === 'student'
          n-form-item(
            required,
            label="Contact Number",
            path="details.contact"
          )
            n-input(
              v-model:value="profileForm.details.contact",
              :allow-input="allowNumberOnly"
              :maxlength="11",
            )

          n-form-item(
            required,
            label="Email",
            path="details.email",
          )
            n-input(v-model:value="profileForm.details.email")

        if role === 'student'
          n-form-item(
            required,
            label="Contact Person",
            path="details.contactPerson"
          )
            n-input(v-model:value="profileForm.details.contactPerson")

          n-form-item(
            required,
            label="Contact Number",
            path="details.contactPersonContact"
          )
            n-input(
              v-model:value="profileForm.details.contactPersonContact",
              :allow-input="allowNumberOnly"
            )

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

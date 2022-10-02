<script>
import { useForm } from '@inertiajs/inertia-vue3';
import {
  NForm,
  NFormItem,
  NInput,
  NButton,
  useNotification,
} from 'naive-ui'
import Layout from '@/Components/Layouts/BaseLayout.vue'

export default {
  layout: Layout,
  components: {
    NForm,
    NFormItem,
    NInput,
    NButton,
  },
  setup() {
    const loginForm = useForm({
      username: '',
      password: '',
      remember: false,
    });

    const notification = useNotification()

    function notifyLoginError() {
      notification.error({
        title: 'Login Failed',
        content: 'Invalid user credentials',
      })
    }

    return { loginForm, notifyLoginError }
  }
}
</script>

<style scoped>
.content {
  padding: 8rem;
  display: flex;
  justify-content: center;
}

.form-content {
  width: 100%;
  max-width: 320px;
}
</style>

<template lang="pug">
.content
  .form-content
    n-form(
      @submit.prevent=`() => loginForm.post(route('post.login'), {
        onError: () => notifyLoginError(),
      })`,
      :model="loginForm"
    )
      // Username field
      n-form-item(
        required,
        label="Username",
        path="username"
      )
        n-input(v-model:value="loginForm.username")

      // Password field
      n-form-item(
        required,
        label="Password",
        path="password"
      )
        n-input(
          v-model:value="loginForm.password",
          type="password",
          show-password-on="click"
        )

      // Submit button
      n-form-item
        n-button(
          block,
          type="primary",
          attr-type="submit",
          :loading="loginForm.processing"
      ) Login
</template>

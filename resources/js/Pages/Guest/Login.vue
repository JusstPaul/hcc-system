<template>
  <n-notification-provider placement="top">
    <div class="content">
      <div class="form-content">
        <n-form @submit.prevent="loginForm.post(route('post.login'), {
          onError: () => notifyLoginError()
        })" :model="loginForm">
          <n-form-item label="Username" :required="true" path="username">
            <n-input v-model:value="loginForm.username" />
          </n-form-item>
          <n-form-item label="Password" :required="true" path="password">
            <n-input type="password" show-password-on="click" v-model:value="loginForm.password" />
          </n-form-item>
          <n-form-item>
            <n-button type="primary" block attr-type="submit" :loading="loginForm.processing">Login</n-button>
          </n-form-item>
        </n-form>
      </div>
    </div>
  </n-notification-provider>
</template>


<script>
import { useForm } from '@inertiajs/inertia-vue3';
import {
  NForm,
  NFormItem,
  NInput,
  NButton,
  NNotificationProvider,
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
    NNotificationProvider,
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

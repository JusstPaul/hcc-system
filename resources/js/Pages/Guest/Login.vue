<template>
    <div class="content">
        <div class="form-content">
            <n-form @submit.prevent="loginForm.post(route('post.login'))" :model="loginForm">
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
</template>


<script>
import { useForm } from '@inertiajs/inertia-vue3';
import { NForm, NFormItem, NInput, NButton } from 'naive-ui'
import Layout from '@/Components/Layouts/BaseLayout.vue'

export default {
    layout: Layout,
    components: {
        NForm,
        NFormItem,
        NInput,
        NButton
    },
    setup() {
        const loginForm = useForm({
            username: '',
            password: '',
            remember: false,
        });

        return { loginForm }
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

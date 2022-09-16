<template>
    <div style="padding: 8rem;">
        <van-row justify="center">
            <van-col span="8">
                <van-form @submit="loginForm.post('/login')">
                    <van-cell-group inset>
                        <van-field v-model="loginForm.username" name="username" label="Username"
                            :rules="[{ required: true, message: 'Username field is required' }]" />
                        <van-field v-model="loginForm.password" name="password" label="Password" type="password"
                            :rules="[{ required: true, message: 'Password field is required' }]" />
                    </van-cell-group>
                    <div style="margin: 16px;">
                        <van-button round block type="primary" native-type="submit">Login</van-button>
                    </div>
                </van-form>
            </van-col>
        </van-row>
    </div>
</template>

<script>
import { useForm } from '@inertiajs/inertia-vue3';

export default {
    components: {

    },
    setup() {
        const loginForm = useForm({
            username: '',
            password: '',
            remember: false,
        });

        return { loginForm };
    }
}
</script>

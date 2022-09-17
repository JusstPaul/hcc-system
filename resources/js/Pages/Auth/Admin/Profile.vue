<template>
    <n-layout>
        <n-layout-header>
            <n-h2>Profile</n-h2>
        </n-layout-header>
        <n-layout-content style="padding: 0 24px 0 24px;">
            <n-form @submit.prevent="profileForm.post(route('post.profile.update', {
                'id': profile._id
            }))" label-placement="left" require-mark-placement="right-hanging" label-width="120" :model="profileForm"
                style="max-width: 400px;">
                <n-form-item label="Username">
                    {{ user.username }}
                </n-form-item>
                <n-form-item label="Role">
                    {{ user.role }}
                </n-form-item>
                <n-form-item label="Last Name" path="lName" :required="true">
                    <n-input v-model:value="profileForm.lName" />
                </n-form-item>
                <n-form-item label="First Name" path="fName" :required="true">
                    <n-input v-model:value="profileForm.fName" />
                </n-form-item>
                <n-form-item label="Middle Name" path="mName">
                    <n-input v-model:value="profileForm.mName" />
                </n-form-item>
                <template v-if="profile.details">
                    <div>Has details</div>
                </template>
                <n-form-item label="Change Password">
                    <n-switch v-model:value="isPasswordChange" />
                </n-form-item>
                <template v-if="isPasswordChange">
                    <n-form-item label="Old Password" path="oPassword">
                        <n-input type="password" show-password-on="click" placeholder="••••••••"
                            v-model:value="profileForm.oPassword" />
                    </n-form-item>
                    <n-form-item label="New Password" path="nPassword">
                        <n-input type="password" show-password-on="click" placeholder="••••••••"
                            v-model:value="profileForm.nPassword" />
                    </n-form-item>
                </template>
                <n-form-item>
                    <n-button type="primary" attr-type="submit" :style="{
                        marginRight: 0,
                        marginLeft: 'auto'
                    }">Update</n-button>
                </n-form-item>
            </n-form>
        </n-layout-content>
    </n-layout>
</template>

<script>
import { computed, ref } from 'vue'
import { usePage, useForm } from '@inertiajs/inertia-vue3'
import {
    NLayout,
    NLayoutContent,
    NLayoutHeader,
    NH2,
    NForm,
    NFormItem,
    NInput,
    NButton,
    NSwitch,
} from 'naive-ui'
import Layout from '@/Components/Layouts/AdminLayout.vue'


export default {
    layout: Layout,
    components: {
        NLayout,
        NLayoutContent,
        NLayoutHeader,
        NH2,
        NForm,
        NFormItem,
        NInput,
        NButton,
        NSwitch,
    },
    props: {
        profile: Object,
    },
    setup(props) {
        const user = computed(() => usePage().props.value.user)

        const {
            l_name,
            m_name,
            f_name
        } = props.profile
        const profileForm = useForm({
            lName: l_name,
            mName: m_name,
            fName: f_name,
            oPassword: '',
            nPassword: '',
        })
        const isPasswordChange = ref(false)

        return {
            user,
            profileForm,
            isPasswordChange
        }
    }
}
</script>

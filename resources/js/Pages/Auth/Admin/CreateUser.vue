<template>
    <n-layout>
        <n-layout-header>
            <n-page-header title="Create User" @back="() => backLink()" style="overflow: hidden;" />
        </n-layout-header>
        <n-layout-content content-style="padding: 24px;">
            <n-form @submit.prevent="() => userForm.post(route('post.admin.create_user'))" label-placement="left"
                require-mark-placement="right-hanging" label-width="120" :model="userForm" style="max-width: 400px;">
                <n-form-item label="Username" path="username" required>
                    <n-input v-model:value="userForm.username" />
                </n-form-item>
                <n-form-item label="Role" path="role" required style="text-transform: capitalize;">
                    <n-select v-model:value="userForm.role" :options="roleOptions" />
                </n-form-item>
                <n-form-item label="Last Name" path="lName" required>
                    <n-input v-model:value="userForm.lName" />
                </n-form-item>
                <n-form-item label="Middle Name" path="mName">
                    <n-input v-model:value="userForm.mName" />
                </n-form-item>
                <n-form-item label="First Name" path="fName" required>
                    <n-input v-model:value="userForm.fName" />
                </n-form-item>

                <!-- HACK: Manually setting if guards for roles -->
                <n-form-item v-if="userForm.role === 'instructor' || userForm.role === 'student'" label="Contact"
                    path="details.contact" required>
                    <n-input v-model:value="userForm.details.contact" type="text" :allow-input="allowNumberOnly" />
                </n-form-item>
                <n-form-item v-if="userForm.role === 'instructor' || userForm.role === 'student'" label="Email"
                    path="details.email" required>
                    <n-input v-model:value="userForm.details.email" />
                </n-form-item>
                <n-form-item v-if="userForm.role === 'student'" label="Contact Person" path="details.contactPerson">
                    <n-input v-model:value="userForm.details.contactPerson" />
                </n-form-item>
                <n-form-item v-if="userForm.role === 'student'" label="Contact Person Mobile"
                    path="details.contactPersonContact">
                    <n-input v-model:value="userForm.details.contactPersonContact" type="text"
                        :allow-input="allowNumberOnly" />
                </n-form-item>
                <n-form-item>
                    <n-button type="primary" attr-type="submit" :loading="userForm.processing" :style="{
                        marginRight: 0,
                        marginLeft: 'auto'
                    }">Submit</n-button>
                </n-form-item>
            </n-form>
        </n-layout-content>
    </n-layout>
</template>

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
} from 'naive-ui'
import { useForm } from '@inertiajs/inertia-vue3'
import { allowNumberOnly } from '@/utils'
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
    },
    props: {
        roles: Array
    },
    setup(props) {
        const roleOptions = props.roles.map((value) => ({
            label: value,
            value
        }))

        const userForm = useForm({
            username: '',
            role: '',
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
            roleOptions
        }
    }
}
</script>



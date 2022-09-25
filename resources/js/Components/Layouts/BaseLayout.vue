<template>
    <n-notification-provider>
        <n-layout>
            <slot />
        </n-layout>
    </n-notification-provider>
</template>

<script>
import { NLayout, NNotificationProvider } from 'naive-ui'

export default {
    components: {
        NLayout,
        NNotificationProvider,
    },
}
</script>

<style scoped>

</style>

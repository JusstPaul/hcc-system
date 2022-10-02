<script>
import Profile from '@/Components/Profile.vue'
import Layout from '@/Components/Layouts/AdminLayout.vue'


export default {
  layout: Layout,
  components: {
    Profile,
  },
  props: {
    profile: Object,
  },
  setup({ profile }) {
    return {
      profile,
    }
  }
}
</script>

<template lang="pug">
profile(:profile="profile")
</template>

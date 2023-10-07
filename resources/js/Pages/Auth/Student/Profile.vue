<script>
import Profile from '@/Components/Profile.vue'
import Layout from '@/Components/Layouts/StudentLayout.vue'

export default {
  layout: Layout,
  components: {
    Profile,
  },
  props: {
    profile: Object,
  },
  setup() {
  }
}
</script>

<template lang="pug">
profile(:profile="profile")
</template>

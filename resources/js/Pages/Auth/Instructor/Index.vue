<script>
import { Inertia } from '@inertiajs/inertia'
import {
  NGrid,
  NGridItem,
  NCard,
  NH4,
  NButton,
} from 'naive-ui';
import Layout from '@/Components/Layouts/InstructorLayout.vue'

export default {
  layout: Layout,
  components: {
    NGrid,
    NGridItem,
    NCard,
    NH4,
    NButton,
  },
  props: {
    classrooms: Array,
  },
  setup() {
    function visitClassroom(id) {
      Inertia.get(route('instructor.classroom', {
        classroom_id: id
      }))
    }

    return {
      visitClassroom,
    }
  }
}
</script>

<template lang="pug">
n-grid(
  responsive="screen",
  :x-gap="12",
  :y-gap="24",
  :cols="`1 s:2 l:4`"
)
  for classroom in classrooms
    n-grid-item(:key="classroom._id")
      n-card(size="small")
        template(#header)
          i-link.link.underline-hover(:href=`route('instructor.classroom', {
            classroom_id: classroom._id,
          })`) {{ classroom.section }}
        .text-uppercase
          |{{ classroom.time_start }} to {{ classroom.time_end }} {{ classroom.day }}
</template>

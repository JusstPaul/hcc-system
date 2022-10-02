<script>
import { ref, h } from 'vue'
import { Link, usePage } from '@inertiajs/inertia-vue3'
import { NLayoutContent, NLayout, NLayoutSider, NLayoutFooter, NMenu, NButton, } from 'naive-ui'
import {
  Logout as LogoutIcon,
  School as SchoolIcon,
  ListDetails as ListDetailsIcon,
  Friends as FriendsIcon,
} from '@vicons/tabler'
import {
  hFull,
  pXS,
  ptXS,
} from '@/styles'
import { SIDER } from '@/constants'
import { logout, renderIcon } from '@/utils'
import Layout from './BaseLayout.vue'

export default {
  layout: Layout,
  components: {
    NLayoutContent,
    NMenu,
    NLayout,
    NLayoutSider,
    NLayoutFooter,
    NButton,
  },
  setup() {
    const _id = usePage().props.value.user._id

    function currentRouteKey() {
      switch (route().current()) {
        case 'student.index':
        case 'student.activity':
          return 'student-classroom'
        case 'student.students':
          return 'student-students'
        default:
          alert('StudentLayou: Invalid route')
          return ''
      }
    }

    const routes = [
      {
        label: () => h(Link, {
          href: route('student.index', {
            student_id: _id,
          })
        }, {
          default: () => 'Classroom',
        }),
        key: 'student-classroom',
        icon: renderIcon(SchoolIcon),
      },
      {
        label: () => h(Link, {
          href: route('student.students', {
            student_id: _id,
          }),
        }, {
          default: () => 'Students',
        }),
        key: 'student-students',
        icon: renderIcon(FriendsIcon)
      }
      /*
            {
              label: () => h('a', {}, {
                default: () => 'Progress'
              }),
              key: 'student-progress',
              icon: renderIcon(ListDetailsIcon),
            }
      */
    ]

    const footerRoutes = [
      {
        label: () => h('a', {
          onClick: () => logout(),
        }, {
          default: () => 'Logout',
        }),
        key: 'student-logout',
        icon: renderIcon(LogoutIcon),
      }
    ]

    return {
      collapsed: ref(false),
      logout,
      hFull,
      pXS,
      ptXS,
      SIDER,
      currentRouteKey,
      routes,
      footerRoutes,
    }
  }
}
</script>

<template lang="pug">
n-layout(has-sider, :style="hFull")
  n-layout-sider(
    bordered,
    show-trigger,
    collapse-mode="width"
    :collapsed-width="SIDER.COLLAPSED_WIDTH",
    :width="SIDER.WIDTH", 
    :collapsed="collapsed",
    @collapse="() => collapsed = true",
    @expand="() => collapsed = false"
  )
    n-layout.h-full
      n-layout-content(:content-style="ptXS")
        n-menu.pt-xs(
          :value="currentRouteKey()",
          :options="routes", 
          :collapsed="collapsed",
          :collapsed-width="SIDER.COLLAPSED_WIDTH",
          :collapsed-icon-size="SIDER.COLLAPSED_ICON_SIZE"
        )
      n-layout-footer.pt-xs(bordered, position="absolute")
        n-menu(
          :options="footerRoutes", 
          :collapsed="collapsed",
          :collapsed-width="SIDER.COLLAPSED_WIDTH",
          :collapsed-icon-size="SIDER.COLLAPSED_ICON_SIZE"
        )
  n-layout-content(:content-style="pXS")
    slot
</template>

<style scoped>
.content {
  padding: 1rem;
}
</style>


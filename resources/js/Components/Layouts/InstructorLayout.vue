<script>
import { h, ref } from 'vue'
import {
  NLayoutContent,
  NLayout,
  NLayoutSider,
  NLayoutFooter,
  NMenu,
  NButton,
} from 'naive-ui'
import {
  School as SchoolIcon,
  Friends as FriendsIcon,
  CalendarPlus as CalendarPlusIcon,
  Logout as LogoutIcon,
} from '@vicons/tabler'
import { Link } from '@inertiajs/inertia-vue3'
import { hFull } from '@/styles'
import { logout, renderIcon } from '@/utils'
import { pXS, ptXS } from '@/styles'
import { SIDER } from '@/constants'
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
    function currentRouteKey() {
      switch (route().current()) {
        case 'instructor.index':
          return 'instructor-index'
        case 'instructor.classroom':
          return 'instructor-classroom'
        case 'instructor.create_activity':
          return 'instructor-create_activity'
        case 'instructor.students':
          return 'instructor-students'
        default:
          alert('InstructorLayout: Invalid route')
          return ''
      }
    }

    function routes() {
      let defaultRoutes = [
        {
          label: () => h(Link, {
            href: route('instructor.index'),
          }, {
            default: () => 'Classrooms',
          }),
          key: 'instructor-index',
          icon: renderIcon(SchoolIcon)
        },
      ]

      switch (route().current()) {
        case 'instructor.index':
          return defaultRoutes;

        case 'instructor.create_activity':
        case 'instructor.classroom':
        case 'instructor.students':
          defaultRoutes.push.apply(defaultRoutes, [
            {
              key: "divider-1",
              type: "divider",
              props: {
                style: {
                  marginLeft: "32px"
                }
              }
            },
            {
              label: () => h(Link, {
                href: route('instructor.students', {
                  classroom_id: route().params.classroom_id,
                })
              }, {
                default: () => 'Students',
              }),
              key: 'classroom-students',
              icon: renderIcon(FriendsIcon)
            },
            {
              label: () => h(Link, {
                href: route('instructor.create_activity', {
                  classroom_id: route().params.classroom_id,
                })
              }, {
                default: () => 'Create Task'
              }),
              key: 'classroom-create_activity',
              icon: renderIcon(CalendarPlusIcon)
            },
          ])
          break;
        default:
          alert('InstructorLayout: Invalid route')
      }
      return defaultRoutes
    }

    const footerRoutes = [
      {
        label: () => h('a', {
          onClick: () => logout(),
        }, {
          default: () => 'Logout',
        }),
        key: 'admin-logout',
        icon: renderIcon(LogoutIcon),
      }
    ]

    return {
      logout,
      collapsed: ref(false),
      routes,
      footerRoutes,
      currentRouteKey,
      hFull,
      pXS,
      ptXS,
      SIDER,
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
          :options="routes()", 
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

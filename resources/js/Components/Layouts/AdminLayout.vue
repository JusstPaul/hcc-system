<script>
import { h, ref } from 'vue'
import { Link } from '@inertiajs/inertia-vue3'
import {
  NLayoutContent,
  NLayout,
  NLayoutSider,
  NLayoutFooter,
  NMenu,
  NButton,
  NNotificationProvider,
  NSpace,
} from 'naive-ui'
import {
  Users as UsersIcon,
  UserCircle as UserCircleIcon,
  School as SchoolIcon,
  Logout as LogoutIcon,
} from '@vicons/tabler'
import { pXS, ptXS } from '@/styles'
import { logout, renderIcon } from '@/utils'
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
    NNotificationProvider,
    NSpace,
  },
  setup() {
    const routes = [
      {
        label: () =>
          h(
            Link,
            {
              href: route('admin.index'),
            },
            {
              default: () => 'Users',
            },
          ),
        key: 'admin-index',
        icon: renderIcon(UsersIcon),
      },
      {
        label: () =>
          h(
            Link,
            {
              href: route('admin.classrooms'),
            },
            {
              default: () => 'Classrooms',
            },
          ),
        key: 'admin-classrooms',
        icon: renderIcon(SchoolIcon),
      },
    ]

    const footerRoutes = [
      {
        label: () =>
          h(
            Link,
            {
              href: route('admin.profile'),
            },
            {
              default: () => 'Profile',
            },
          ),
        key: 'admin-profile',
        icon: renderIcon(UserCircleIcon),
      },
      {
        label: () =>
          h(
            'a',
            {
              onClick: () => logout(),
            },
            {
              default: () => 'Logout',
            },
          ),
        key: 'admin-logout',
        icon: renderIcon(LogoutIcon),
      },
    ]
    function currentRouteKey() {
      switch (route().current()) {
        case 'admin.index':
        case 'admin.create_user':
        case 'admin.edit_user':
          return 'admin-index'
        case 'admin.classrooms':
        case 'admin.create_classroom':
          return 'admin-classrooms'
        case 'admin.profile':
          return 'admin-profile'
        default:
          alert('AdminLayout: Invalid route')
          return ''
      }
    }

    return {
      logout,
      routes,
      footerRoutes,
      currentRouteKey,
      collapsed: ref(false),
      pXS,
      ptXS,
      SIDER,
    }
  },
}
</script>

<template lang="pug">
n-layout.h-full(has-sider)
  n-layout-sider(
    bordered,
    show-trigger,
    collapse-mode="width",
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

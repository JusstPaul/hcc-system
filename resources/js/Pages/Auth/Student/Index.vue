<script>
import dayjs from 'dayjs'
import { computed } from 'vue'
import { isUndefined } from 'lodash'
import { Inertia } from '@inertiajs/inertia'
import {
  NSpace,
  NCard,
  NEllipsis,
  NH4,
  NButton,
  NEmpty,
  NLayout,
  NLayoutContent,
  NTabs,
  NTabPane,
  NAlert,
  NTime,
  NStatistic,
  NSteps,
  NStep,
  NIcon,
} from 'naive-ui'
import { DeviceDesktopOff as DeviceDesktopOffIcon } from '@vicons/tabler'
import { wFull, wMax, mxAuto } from '@/styles'
import { DATE_FORMAT } from '@/constants'
import { convertDeltaContent, downloadFile, getFileName } from '@/utils'
import Layout from '@/Components/Layouts/StudentLayout.vue'

export default {
  components: {
    NSpace,
    NCard,
    NEllipsis,
    NH4,
    NButton,
    NEmpty,
    NLayout,
    NLayoutContent,
    NTabs,
    NTabPane,
    NAlert,
    NTime,
    NStatistic,
    NSteps,
    NStep,
    NIcon,
    DeviceDesktopOffIcon,
    Layout,
  },
  props: {
    student_id: String,
    joined_class: Boolean,
    activities: Array,
    announcements: Object,
  },
  setup({ student_id }) {
    const { tab } = route().params

    function visitActivity(_id) {
      Inertia.get(
        route('student.activity', {
          student_id,
          activity_id: _id,
        }),
      )
    }

    function isActivityAnswered(ans) {
      if (isUndefined(ans)) {
        return false
      }

      return ans
        .map(({ student_id: s_id }) => s_id === student_id)
        .reduce((acc, val) => acc && val, true)
    }

    function findAnswer({ student_id: s_id }) {
      return s_id === student_id
    }

    /* Progress when submitted
     * 1 - Submitted
     * 3 - Score
     */
    function getProgress(ans) {
      const answer = ans.find(findAnswer)
      if (isUndefined(answer.checks)) {
        return 1
      }
      return 3
    }

    function getScore(ans) {
      const { checks } = ans.find(findAnswer)
      return checks.flat().reduce((acc, val) => acc + parseInt(val.score), 0)
    }

    function getTotal(ans) {
      const { checks } = ans.find(findAnswer)
      return checks.flat().reduce((acc, val) => acc + parseInt(val.total), 0)
    }

    return {
      DATE_FORMAT,
      dayjs,
      wFull,
      wMax,
      mxAuto,
      visitActivity,
      tab: computed(() => (isUndefined(tab) ? 'activities' : 'announcements')),
      isActivityAnswered,
      getProgress,
      getScore,
      getTotal,
      Inertia,
      convertDeltaContent,
      downloadFile,
      getFileName,
    }
  },
}
</script>

<template lang="pug">
layout(:hasClass="joined_class")
  n-layout.h-full
    n-layout-content.h-full
      if joined_class
        n-tabs(type="line", :default-value="tab")
          n-tab-pane(
            name="activities",
            tab="Activities"
          )
            n-space(justify="center", :item-style="wFull")
              n-space(vertical, :item-style="wFull")
                for activity in activities
                  n-card(
                    :key="activity._id",
                    :style="{...wFull, ...wMax(500), ...mxAuto}"
                  )
                    template(#header)
                      if !isActivityAnswered(activity.answers)
                        i-link.link.cursor-pointer(
                          :href=`route('student.activity', {
                            student_id,
                            activity_id: activity._id,
                          })`
                        ) {{ activity.title }}
                      else
                        span.link.cursor-pointer {{ activity.title }}
                    template(#header-extra)
                      n-time(
                        :time="dayjs(activity.created_at).valueOf()",
                        :format="`${DATE_FORMAT}`"
                      )
                    if isActivityAnswered(activity.answers)
                      n-space(justify="center")
                        n-steps(:current="getProgress(activity.answers)")
                          n-step(
                            title="Submitted",
                            description="You have submitted the task."
                          )
                          n-step(
                            title="Checked",
                            description="Your task has already been checked."
                          )
                          n-step(title="Score")
                            if getProgress(activity.answers) === 3
                              n-statistic(:value="getScore(activity.answers)")
                                template(#suffix)
                                  |/ {{ getTotal(activity.answers) }}
                    else
                      n-button(
                        block,
                        type="primary",
                        @click=`() => Inertia.get(route('student.activity', {
                          student_id,
                          activity_id: activity._id,
                        }))`
                      ) Open Task
          n-tab-pane(
            name="announcements",
            tab="Announcement"
          )
            n-space(justify="center", :item-style="wFull")
              n-space(vertical, :item-style="wFull")
                for announcement in announcements
                  n-card(
                    :key="announcement._id",
                    :style="{...wFull, ...wMax(500), ...mxAuto}"
                  )
                    n-space(justify="end")
                      n-time(
                        :time="dayjs(announcement.created_at).valueOf()",
                        :format="DATE_FORMAT"
                      )
                    n-alert.w-full
                      div(v-html="convertDeltaContent(announcement.content)")
                    template(#footer)
                      if announcement.fileContents.length !== 0
                        div Attachments:
                        n-space(vertical)
                          for file in announcement.fileContents
                            n-button(
                              quaternary,
                              block,
                              @click="downloadFile(file)"
                            ) {{ getFileName(file) }}
      else
        n-space.h-full(justify="center", :item-style="wFull")
          n-space.h-full(vertical, :item-style="wFull")
            n-empty.h-full(size="huge", description="Not registered to a classroom")
              template(#icon)
                n-icon
                  device-desktop-off-icon
</template>

<template>
  <n-form :model="answerForm" label-placement="left" require-mark-placement="right-hanging" label-width="120"
    style="max-width: 1080px;">

    <n-alert title="Instructions">
      {{ generalDirection }}
    </n-alert>

    <template v-for="({ id, values }, index) in answerForm.answers" :key="id">
      <n-form-item v-for="({ id: childId }, idx) in values" :key="childId"
        :path="`answerForm.answers[${index}].value[${idx}]`" style="margin-top: 2rem;">
        <n-card>
          {{ questions[index].values[idx].instruction }}

          <n-layout>
            <n-layout-content content-style="margin-top: 1rem; padding-left: 12px; padding-right: 12px;">
              <!-- Question -->
              <template v-if="matchQuestion(index, 0)">
                <n-input v-model:value="answerForm.answers[index].values[idx].value" />
              </template>

              <!-- True or false -->
              <template v-if="matchQuestion(index, 1)">
                <n-radio-group v-model:value="answerForm.answers[index].values[idx].value"
                  :name="`activity-${id}-${childId}`">
                  <n-space>
                    <n-radio label="True" value="true" />
                    <n-radio label="False" value="false" />
                  </n-space>
                </n-radio-group>
              </template>

              <!-- Multiple choice -->
              <template v-if="matchQuestion(index, 2)">
                <n-radio-group v-model:value="answerForm.answers[index].values[idx].value" :name="`activity-${id}`">
                  <n-space>
                    <n-radio v-for="(val, index) in questions[index].values[idx].content" :key="val" :value="val"
                      :label="val" />
                  </n-space>
                </n-radio-group>
              </template>

              <!-- Handwriting Comparator -->
              <template v-if="matchQuestion(index, 4)">
                <n-layout has-sider>
                  <n-layout-sider></n-layout-sider>
                  <n-layout-content>
                    <n-space></n-space>
                  </n-layout-content>
                </n-layout>
              </template>

            </n-layout-content>
          </n-layout>
        </n-card>
      </n-form-item>
    </template>

  </n-form>
</template>

<script>
import { useForm } from '@inertiajs/inertia-vue3';
import {
  NSpace,
  NCard,
  NProgress,
  NImage,
  NImageGroup,
  NGrid,
  NGridItem,
  NForm,
  NFormItem,
  NInput,
  NLayout,
  NLayoutContent,
  NLayoutSider,
  NRadio,
  NRadioGroup,
  NAlert,
} from 'naive-ui'
import Layout from '@/Components/Layouts/StudentLayout.vue'
import { QUESTION_TYPES } from '@/constants';

export default {
  layout: Layout,
  components: {
    NSpace,
    NCard,
    NProgress,
    NImage,
    NImageGroup,
    NGrid,
    NGridItem,
    NForm,
    NFormItem,
    NInput,
    NLayout,
    NLayoutContent,
    NLayoutSider,
    NRadio,
    NRadioGroup,
    NAlert,
  },
  props: {
    student_id: String,
    activity: Object,
  },
  setup({ activity }) {
    const { questions, general_directions, } = activity

    const answerForm = useForm({
      'answers': questions.map((val) => {
        if (val.type === QUESTION_TYPES[4]) {
          return {
            id: val.id,
            values: val.values.map(({ id, content }) => ({
              id,
              value: [],
              files: content,
              progress: {
                current: 1,
                total: content.samples.length - 1,
              }
            }))
          }

        }
        return {
          id: val.id,
          values: val.values.map(({ id }) => ({
            id,
            value: null,
          }))
        }
      })
    })

    function matchQuestion(index, typeIndex) {
      return activity.questions[index].type === QUESTION_TYPES[typeIndex]
    }

    return {
      answerForm,
      matchQuestion,
      questions,
      generalDirection: general_directions,
    }
  }
}
</script>

<style scoped>
.comparator-container {
  height: fit-content;
  position: relative;
}

.comparator-top,
.comparator-bottom {
  position: absolute;
  top: 0;
  left: 0;
}
</style>

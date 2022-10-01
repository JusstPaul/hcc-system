<template>
  <n-space vertical justify="center">
    <n-space>
      <n-form :model="answerForm" label-placement="left" require-mark-placement="right-hanging" label-width="120"
        style="max-width: 1080px;">

        <n-alert title="Instructions">
          {{ generalDirection }}
        </n-alert>

        <template v-for="({ id, values }, index) in answerForm.answers" :key="id">
          <n-form-item v-for="({ id: childId }, idx) in values" :key="childId"
            :path="`answerForm.answers[${index}].value[${idx}]`" style="margin-top: 2rem;">
            <n-card>
              <n-alert>
                {{ questions[index].values[idx].instruction }}
              </n-alert>

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
                    <n-space vertical>
                      <n-layout>
                        <n-layout-header style="margin-bottom: 0.5rem; padding-bottom: 0.5rem;" bordered>
                          <n-space align="center">
                            <div>
                              <n-button @click="snapshot(`hcc-${id}-${index}-${idx}`, index, idx)" attr-type="button"
                                type="primary">Snapshot</n-button>
                            </div>
                            <div>
                              <n-switch>
                                <template #checked>Right</template>
                                <template #unchecked>Left</template>
                              </n-switch>
                            </div>
                          </n-space>
                        </n-layout-header>
                        <n-layout has-sider>
                          <n-layout-sider bordered width="150">
                            <n-space vertical item-style="margin-right: 0.5rem;">
                              <div>
                                <span>Zoom</span>
                                <n-slider size="small" />
                              </div>
                              <div>
                                <span>Filters</span>
                                <n-select size="small" />
                              </div>
                              <div>
                                <span>Contrast</span>
                                <n-select size="small" />
                              </div>
                              <div>
                                <span>Brightness</span>
                                <n-select size="small" />
                              </div>
                              <div>
                                <span>Overlap</span>
                                <n-select size="small" />
                              </div>
                              <div>
                                <n-button @click="addImaginaryLine(index, idx, id, childId)" attr-type="button"
                                  type="primary">Add
                                  Imaginary Line</n-button>
                              </div>
                            </n-space>
                          </n-layout-sider>
                          <n-layout-content>
                            <n-space vertical>
                              <div :id="`hcc-${id}-${index}-${idx}`"
                                style="margin: 1rem; padding: 1rem; position: relative; min-width: 850px; max-width: 1080px; min-height: 250px;">
                                <div style="position: absolute; top: 0; left: 0; width: 100%; height: fit-content;">
                                  <n-image-group>
                                    <n-space justify="center">
                                      <n-space item-style="border-style: solid; border-width: 0.75px;">
                                        <n-image width="350" object-fit="contain"
                                          :src="answerForm.answers[index].values[idx].files.questioned" />
                                        <n-image width="350" object-fit="contain"
                                          :src="answerForm.answers[index].values[idx].files.questioned" />
                                      </n-space>
                                    </n-space>
                                  </n-image-group>
                                </div>
                                <svg :id="`hcc-${id}-${index}-${idx}-svg`"
                                  style="width: 100%; position: absolute; top: 0; left: 0;"></svg>
                              </div>
                              <div style="padding-left: 24px; padding-right: 24px;">
                                <n-space>
                                  <span>Characteristics: </span>

                                  <n-tooltip v-for="({ label, description }, i) in hccCharacteristics" :key="i"
                                    trigger="hover">
                                    <template #trigger>
                                      <n-button @click="hccAddCharacteristic(label, index, idx, id, childId)" text>{{
                                      label }}
                                      </n-button>
                                    </template>
                                    {{ description }}
                                  </n-tooltip>

                                </n-space>
                              </div>
                            </n-space>
                          </n-layout-content>
                        </n-layout>
                        <n-layout-content>
                          <n-card style="margin-top: 0.5rem;" closable
                            v-for="({ file, id: valId }, i) in answerForm.answers[index].values[idx].value" :key="valId"
                            :title="`Snapshot #${i + 1}`">
                            <n-space vertical>
                              <n-image :src="file" style="width: 100%" />
                              <n-input type="textarea" />
                            </n-space>
                          </n-card>
                        </n-layout-content>
                      </n-layout>
                    </n-space>
                  </template>

                </n-layout-content>
              </n-layout>
            </n-card>
          </n-form-item>
        </template>

      </n-form>

    </n-space>
  </n-space>
</template>

<script>
import * as d3 from 'd3'
import { drag } from 'd3-drag'
import { symbol, symbolTriangle } from 'd3-shape'
import { nanoid } from 'nanoid'
import { useForm, usePage } from '@inertiajs/inertia-vue3'
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
  NLayoutHeader,
  NRadio,
  NRadioGroup,
  NAlert,
  NSlider,
  NButton,
  NSwitch,
  NSelect,
  NTooltip,
} from 'naive-ui'
import { toPng } from 'html-to-image'
import Layout from '@/Components/Layouts/StudentLayout.vue'
import { QUESTION_TYPES } from '@/constants'
import { requestFile } from '@/utils'

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
    NLayoutHeader,
    NRadio,
    NRadioGroup,
    NAlert,
    NSlider,
    NButton,
    NSwitch,
    NSelect,
    NTooltip,
  },
  props: {
    student_id: String,
    activity: Object,
  },
  setup({ activity }) {
    const { questions, general_directions, } = activity

    const token = usePage().props.value.user.token

    const answerForm = useForm({
      'answers': questions.map((val, index) => {
        if (val.type === QUESTION_TYPES[4]) {
          return {
            id: val.id,
            values: val.values.map(({ id, content }, idx) => {
              requestFile(token, content.questioned, (res) => {
                answerForm.answers[index].values[idx].files.questioned = res
              })
              content.samples.map((v) => {
                requestFile(token, v, (res) => {
                  answerForm.answers[idx].values[idx].files.samples.push(res)
                })
              })

              return {
                id,
                state: {
                  svg: null,
                  zoom: {
                    left: 1,
                    right: 1,
                  },
                  filter: {
                    left: '',
                    right: '',
                  },
                  contrast: {
                    left: '',
                    right: '',
                  },
                  overlap: 1,
                },
                files: {
                  questioned: null,
                  samples: [],
                },
                value: [],
                progress: {
                  current: 0,
                  total: content.samples.length - 1,
                }
              }
            })
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

    function snapshot(ref, parentIndex, childIndex, idx = -1) {
      const node = document.getElementById(ref)

      toPng(node).then(function (url) {
        if (idx === -1) {
          answerForm.answers[parentIndex].values[childIndex].value.push({
            id: nanoid(10),
            file: url,
            description: '',
          })
          answerForm.answers[parentIndex].values[childIndex].progress.current += 1
        }
      }).catch((err) => {
        alert('Snapshot failed')
        console.error(err)
      })
    }

    function getParentDiv(parentIndex, childIndex) {
      const id = answerForm.answers[parentIndex].id
      return document.getElementById(`hcc-${id}-${parentIndex}-${childIndex}`)
    }

    function checkSVGState(parentIndex, childIndex) {
      if (answerForm.answers[parentIndex].values[childIndex].state.svg == null) {
        const id = answerForm.answers[parentIndex].id
        const parentDiv = getParentDiv(parentIndex, childIndex)

        answerForm.answers[parentIndex].values[childIndex].state.svg = d3.select(`#hcc-${id}-${parentIndex}-${childIndex}-svg`)
          .attr('width', parentDiv.clientWidth)
          .attr('height', parentDiv.clientHeight)
      }
    }

    function addImaginaryLine(parentIndex, childIndex, parentId, childId) {
      checkSVGState(parentIndex, childIndex)

      const parentDiv = getParentDiv(parentIndex, childIndex)
      const { clientWidth, clientHeight } = parentDiv

      const lineId = nanoid(5)

      answerForm.answers[parentIndex].values[childIndex].state.svg
        .append('line')
        .style('stroke', 'black')
        .style('stroke-width', 5)
        .style('stroke-dasharray', ('3', '3'))
        .attr('x1', clientWidth * 0.3)
        .attr('y1', clientHeight * 0.5)
        .attr('x2', clientWidth * 0.6)
        .attr('y2', clientHeight * 0.5)
        .attr('id', `line-${parentId}-${childId}-${lineId}`)
        .call(drag().on('start', () => {
          d3.select(`#line-${parentId}-${childId}-${lineId}`).classed('dragged', true)
        }).on('drag', ({ dx, dy }, _d) => {


          const line = d3.select(`#line-${parentId}-${childId}-${lineId}`)

          const x1 = parseInt(line.attr('x1')) + dx
          const x2 = parseInt(line.attr('x2')) + dx
          const y1 = parseInt(line.attr('y1')) + dy
          const y2 = parseInt(line.attr('y2')) + dy

          line.attr('x1', x1)
            .attr('x2', x2)
            .attr('y1', y1)
            .attr('y2', y2)
        }).on('end', () => {
          d3.select(`#line-${parentId}-${childId}-${lineId}`).classed('dragged', false)
        }))
    }

    const hccCharacteristics = [
      {
        label: 'NV',
        description: '',
      }
    ]

    function hccAddCharacteristic(label, parentIndex, childIndex, parentId, childId) {
      checkSVGState(parentIndex, childIndex)

      const parentDiv = getParentDiv(parentIndex, childIndex)
      const { clientWidth, clientHeight } = parentDiv

      const id = nanoid(5)

      const group = answerForm.answers[parentIndex].values[childIndex].state.svg
        .append('g')
      group.append('line')
        .attr('id', `hc-${parentId}-${childId}-${id}-line`)
        .attr('x1', clientWidth * 0.3)
        .attr('x2', clientWidth * 0.6)
        .attr('y1', clientHeight * 0.5)
        .attr('y2', clientHeight * 0.5)
        .style('stroke', 'black')

      group.append('text')
        .style('stroke', 'black')
        .style('font-size', '16px')
        .style('cursor', 'default')
        .attr('id', `hc-${parentId}-${childId}-${id}-text`)
        .attr('x', getLine().attr('x1') + 8)
        .attr('y', parseInt(getLine().attr('y1')) + 4)
        .text(label)
        .call(drag().on('start', () => {
          getLine().classed('dragged', true)
          getText().classed('dragged', true)
        }).on('drag', ({ dx, dy }, _id) => {
          // only move the text, cicle, and y1 for line
          const text = getText()
          const line = getLine()

          const textX = parseInt(text.attr('x')) + dx
          const textY = parseInt(text.attr('y')) + dy

          text.attr('x', textX)
            .attr('y', textY)

          line.attr('x1', lineX1)
            .attr('y1', lineY1)
        }).on('end', () => {
          getLine().classed('dragged', false)
          getText().classed('dragged', false)
        }))

      function getLine() {
        return d3.select(`#hc-${parentId}-${childId}-${id}-line`)
      }

      function getText() {
        return d3.select(`#hc-${parentId}-${childId}-${id}-text`)
      }

      // .call(drag().on('start', () => {
      //   d3.select(`#hcc-characteristic-${parentId}-${childId}-${id}-text`).classed('dragged', true)
      // }).on('drag', (event, _id) => {
      //   d3.select(`#hcc-characteristic-${parentId}-${childId}-${id}-text`)
      //     .attr('y', event.y)
      //     .attr('x', event.x)
      // })
      //   .on('end', () => {
      //     d3.select(`#hcc-characteristic-${parentId}-${childId}-${id}-text`).classed('dragged', false)
      //   }))
      // .append('text')
      // .style('stroke', 'black')
      // .style('font', '11px')
      // .style('cursor', 'default')
      // .attr('id', `hcc-characteristic-${parentId}-${childId}-${id}-text`)
      // .attr('x', clientWidth * 0.5)
      // .attr('y', clientHeight * 0.5)
      // .text(label)
    }

    return {
      answerForm,
      matchQuestion,
      questions,
      generalDirection: general_directions,
      snapshot,
      addImaginaryLine,
      hccCharacteristics,
      hccAddCharacteristic,
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

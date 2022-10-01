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
                              <n-switch v-model:value="answerForm.answers[index].values[idx].state.mode"
                                checked-value="r" unchecked-value="l">
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
                                <template v-if="checkModeL(index, idx)">
                                  <n-slider size="small"
                                    v-model:value="answerForm.answers[index].values[idx].state.zoom.left" :max="2"
                                    :min="1" :step="0.1" />
                                </template>
                                <template v-else>
                                  <n-slider size="small"
                                    v-model:value="answerForm.answers[index].values[idx].state.zoom.right" :max="2"
                                    :min="1" :step="0.1" />
                                </template>
                              </div>
                              <div>
                                <span>Filters</span>
                                <template v-if="checkModeL(index, idx)">
                                  <n-select size="small" :options="filters"
                                    v-model:value="answerForm.answers[index].values[idx].state.filter.left" />
                                </template>
                                <template v-else>
                                  <n-select size="small" :options="filters"
                                    v-model:value="answerForm.answers[index].values[idx].state.filter.right" />
                                </template>
                              </div>
                              <div>
                                <span>Opacity</span>
                                <template v-if="checkModeL(index, idx)">
                                  <n-slider size="small"
                                    v-model:value="answerForm.answers[index].values[idx].state.opacity.left" :max="1"
                                    :min="0.1" :step="0.1" />
                                </template>
                                <template v-else>
                                  <n-slider size="small"
                                    v-model:value="answerForm.answers[index].values[idx].state.opacity.right" :max="1"
                                    :min="0.1" :step="0.1" />
                                </template>
                              </div>
                              <div>
                                <span>Gap</span>
                                <n-slider size="small" v-model:value="answerForm.answers[index].values[idx].state.gap"
                                  :max="20" :min="0" :step="1" />
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
                                      <n-space item-style="border-style: solid; border-width: 0.75px;"
                                        :size="answerForm.answers[index].values[idx].state.gap">
                                        <div style="overflow: hidden;">
                                          <n-image width="350" object-fit="contain"
                                            :src="answerForm.answers[index].values[idx].files.questioned" :style="{
                                                transform: `scale(${answerForm.answers[index].values[idx].state.zoom.left})`,
                                              filter: filterToCSS(answerForm.answers[index].values[idx].state.filter.left),
                                              opacity: answerForm.answers[index].values[idx].state.opacity.left,
                                            }" />
                                        </div>
                                        <div style="overflow: hidden;">
                                          <n-image width="350" object-fit="contain"
                                            :src="answerForm.answers[index].values[idx].files.samples[answerForm.answers[index].values[idx].progress.current]"
                                            :style="{
                                                transform: `scale(${answerForm.answers[index].values[idx].state.zoom.right})`,
                                              filter: filterToCSS(answerForm.answers[index].values[idx].state.filter.right),
                                              opacity: answerForm.answers[index].values[idx].state.opacity.right,
                                            }" />
                                        </div>
                                      </n-space>
                                    </n-space>
                                  </n-image-group>
                                </div>
                                <svg :id="`hcc-${id}-${index}-${idx}-svg`" class="comparator-svg"></svg>
                              </div>
                              <div style="padding-left: 24px; padding-right: 24px;">
                                <n-space>
                                  <span>Characteristics: </span>

                                  <n-tooltip v-for="({ header, label, description }, i) in hccCharacteristics" :key="i"
                                    trigger="hover">
                                    <template #header>
                                      {{ header }}
                                    </template>
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
                          <n-card style="margin-top: 0.5rem;"
                            v-for="({ file, id: valId, }, i) in answerForm.answers[index].values[idx].value"
                            :key="valId" :title="`Snapshot #${i + 1}`">
                            <n-space vertical>
                              <n-image :src="file" style="width: 100%" />
                              <n-input type="textarea"
                                v-model:value="answerForm.answers[index].values[idx].value[i].description" />
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
                  mode: 'l', // l or r
                  zoom: {
                    left: 1,
                    right: 1,
                  },
                  filter: {
                    left: 0,
                    right: 0,
                  },
                  opacity: {
                    left: 1,
                    right: 1,
                  },
                  gap: 20,
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

    function checkModeL(parentIndex, childIndex) {
      return answerForm.answers[parentIndex].values[childIndex].state.mode === 'l'
    }

    const _filters = ['none', 'hue', 'sepia', 'saturate', 'grayscale']
    const filters = _filters.map((val, index) => ({
      label: val,
      value: index,
    }))

    function filterToCSS(index) {
      switch (_filters[index]) {
        case 'hue':
          return 'hue-rotate(180deg)'
        case 'sepia':
          return 'sepia(100%)'
        case 'saturate':
          return 'saturate(4)'
        case 'grayscale':
          return 'grayscale(100%)'
        case 'none':
        default:
          return 'none';
      }
    }

    function resetState(parentIndex, childIndex) {
      const id = answerForm.answers[parentIndex].id
      const parentDiv = getParentDiv(parentIndex, childIndex)

      const svg = d3.select(`#hcc-${id}-${parentIndex}-${childIndex}-svg`)
      svg.selectAll('*').remove()

      answerForm.answers[parentIndex].values[childIndex].state = {
        svg: null,
        mode: 'l', // l or r
        zoom: {
          left: 1,
          right: 1,
        },
        filter: {
          left: 0,
          right: 0,
        },
        opacity: {
          left: 1,
          right: 1,
        },
        gap: 20,
      }

    }

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
          resetState(parentIndex, childIndex)
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
        .on('dblclick', () => {
          d3.select(`#line-${parentId}-${childId}-${lineId}`).remove()
        })
        .call(d3.drag().on('start', () => {
          d3.select(`#line-${parentId}-${childId}-${lineId}`).classed('dragged', true)
        }).on('drag', () => {

          const line = d3.select(`#line-${parentId}-${childId}-${lineId}`)

          const x1 = parseInt(line.attr('x1')) + d3.event.dx
          const x2 = parseInt(line.attr('x2')) + d3.event.dx
          const y1 = parseInt(line.attr('y1')) + d3.event.dy
          const y2 = parseInt(line.attr('y2')) + d3.event.dy

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
        label: 'PP',
        header: 'Pen pressure',
        description: 'The average force with which the pen contracts the paper',
      },
    ]

    function hccAddCharacteristic(label, parentIndex, childIndex, parentId, childId) {
      checkSVGState(parentIndex, childIndex)

      const parentDiv = getParentDiv(parentIndex, childIndex)
      const { clientWidth, clientHeight } = parentDiv

      const id = nanoid(5)

      answerForm.answers[parentIndex].values[childIndex].state.svg
        .append('g')
        .attr('class', 'annotation-group')
        .attr('id', `ch-${parentId}-${childId}-${id}`)
        .on('dblclick', () => {
          d3.select(`#ch-${parentId}-${childId}-${id}`).remove()
        })
        .call(d3.annotation()
          .editMode(true)
          .notePadding(15)
          .type(d3.annotationCallout)
          .annotations([{
            note: {
              title: label,
              bgPadding: 10,
            },
            x: clientWidth * 0.5,
            y: clientHeight * 0.5,
            dx: 20,
            dy: 20,
            color: 'black',
          }]))
        .attr('stroke', 'black')
    }

    return {
      answerForm,
      matchQuestion,
      checkModeL,
      filters,
      filterToCSS,
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

.comparator-svg {
  width: 100%;
  position: absolute;
  top: 0;
  left: 0;
}
</style>


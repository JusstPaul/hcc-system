<script>
import { useAsyncState } from '@vueuse/core'
import { isUndefined } from 'lodash'
import { nanoid } from 'nanoid'
import { stringify as romanStringify } from 'roman-numerals-convert'
import { Inertia } from '@inertiajs/inertia'
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
  NSkeleton,
  NDivider,
  NPageHeader,
  NH3,
  NEmpty,
  NIcon,
} from 'naive-ui'
import { QuillEditor } from '@vueup/vue-quill'
import { CheckupList as CheckupListIcon } from '@vicons/tabler'
import { toBlob } from 'html-to-image'
import Layout from '@/Components/Layouts/StudentLayout.vue'
import { QUESTION_TYPES } from '@/constants'
import { requestFile, convertDeltaContent } from '@/utils'
import {
  wFull,
  wMax,
  mxAuto,
  pXS,
  mtTwoRem,
  mrHalfRem,
  mlAuto,
  mr,
} from '@/styles'

async function keyToJpeg(token, key) {
  const response = await requestFile(token, key)
  const url = URL.createObjectURL(response)
  return url
}

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
    NSkeleton,
    NDivider,
    NPageHeader,
    NH3,
    NProgress,
    NEmpty,
    NIcon,
    QuillEditor,
    CheckupListIcon,
  },
  props: {
    student_id: String,
    activity: Object,
  },
  setup({ activity, student_id }) {
    const { questions, general_directions } = activity

    const token = usePage().props.value.user.token

    const answerForm = useForm({
      answers: questions.map((val) => {
        if (val.type === QUESTION_TYPES[4]) {
          return {
            id: val.id,
            values: val.values.map(({ id, content }) => {
              const questioned = useAsyncState(
                keyToJpeg(token, content.questioned),
              )
              const samples = content.samples.map((val) => {
                return useAsyncState(keyToJpeg(token, val))
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
                  brightness: {
                    left: 1,
                    right: 1,
                  },
                  opacity: {
                    left: 1,
                    right: 1,
                  },
                  gap: 20,
                },
                files: {
                  questioned: questioned,
                  samples: samples,
                },
                value: [],
                progress: {
                  current: 0,
                  total: content.samples.length,
                },
              }
            }),
          }
        }
        return {
          id: val.id,
          values: val.values.map(({ id }) => ({
            id,
            value: null,
          })),
        }
      }),
    })

    function checkModel(parentIndex, childIndex) {
      return (
        answerForm.answers[parentIndex].values[childIndex].state.mode === 'l'
      )
    }

    function resetState(parentIndex, childIndex) {
      const id = answerForm.answers[parentIndex].id

      const svg = d3.select(`#hcc-${id}-${parentIndex}-${childIndex}-svg`)
      svg.selectAll('*').remove()

      answerForm.answers[parentIndex].values[childIndex].state = {
        svg: null,
        mode: 'l', // l or r
        zoom: {
          left: 1,
          right: 1,
        },
        brightness: {
          left: 1,
          right: 1,
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

      toBlob(node)
        .then(function (blob) {
          if (idx === -1) {
            const today = new Date().valueOf()
            const id = nanoid(12)
            const file = new File([blob], `hcc-${today}-${id}.png`, {
              type: 'image/png',
              lastModified: today,
            })

            answerForm.answers[parentIndex].values[childIndex].value.push({
              id: nanoid(10),
              file,
              description: '',
            })
            resetState(parentIndex, childIndex)
            answerForm.answers[parentIndex].values[
              childIndex
            ].progress.current += 1
          }
        })
        .catch((err) => {
          alert('Snapshot failed')
          console.error(err)
        })
    }

    function getParentDiv(parentIndex, childIndex) {
      const id = answerForm.answers[parentIndex].id
      return document.getElementById(`hcc-${id}-${parentIndex}-${childIndex}`)
    }

    function checkSVGState(parentIndex, childIndex) {
      if (
        answerForm.answers[parentIndex].values[childIndex].state.svg == null
      ) {
        const id = answerForm.answers[parentIndex].id
        const parentDiv = getParentDiv(parentIndex, childIndex)

        answerForm.answers[parentIndex].values[childIndex].state.svg = d3
          .select(`#hcc-${id}-${parentIndex}-${childIndex}-svg`)
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
        .call(
          d3
            .drag()
            .on('start', () => {
              d3.select(`#line-${parentId}-${childId}-${lineId}`).classed(
                'dragged',
                true,
              )
            })
            .on('drag', () => {
              const line = d3.select(`#line-${parentId}-${childId}-${lineId}`)

              const x1 = parseInt(line.attr('x1')) + d3.event.dx
              const x2 = parseInt(line.attr('x2')) + d3.event.dx
              const y1 = parseInt(line.attr('y1')) + d3.event.dy
              const y2 = parseInt(line.attr('y2')) + d3.event.dy

              line.attr('x1', x1).attr('x2', x2).attr('y1', y1).attr('y2', y2)
            })
            .on('end', () => {
              d3.select(`#line-${parentId}-${childId}-${lineId}`).classed(
                'dragged',
                false,
              )
            }),
        )
    }

    const hccCharacteristics = [
      {
        label: 'PP',
        header: 'Pen pressure',
        description: 'The average force with which the pen contracts the paper',
      },
      {
        label: 'PTCH',
        header: 'Patching',
        description:
          'The retouching or going back over a defective writing stroke.',
      },
      {
        label: 'TR',
        header: 'Tremor',
        description: 'The irregular shaky stroke.',
      },
      {
        label: 'STR',
        header: 'Stroke',
        description:
          'These are series of lines or curves within a single letter.',
      },
      {
        label: 'D',
        header: 'Diacritic',
        description:
          'It is a sign added to a letter or symbol to give it a particular phonetic value.',
      },
      {
        label: 'B',
        header: 'Baseline',
        description:
          'It is the ruled or imaginary line upon which the writing rests.',
      },
      {
        label: 'A',
        header: 'Alignment',
        description:
          'The relation of parts of the whole line of writing or line of individual letter in words to the baseline.',
      },
      {
        label: 'LQ',
        header: 'Line quality',
        description:
          'Refers to the overall character of the written strokes from the initial to the terminal.',
      },
      {
        label: 'B',
        header: 'Baseline',
        description:
          'It is the ruled or imaginary line upon which the writing rests.',
      },
      {
        label: 'LS',
        header: 'Lateral spacing',
        description:
          'The horizontal dimension of writing produced by the width of letters, the space between letters and words, and the width of margins.',
      },
      {
        label: 'NV',
        header: 'Natural variation',
        description:
          'The normal or usual deviation found in repeated specimen of any individual handwriting.',
      },
      {
        label: 'RHY',
        header: 'Rhythm',
        description:
          'The element of the writing movement marked by regular or periodic recurrences. It may be classed as smooth, intermittent, or jerky in its quality.',
      },
      {
        label: 'PR',
        header: 'Proportion',
        description: 'It is the relation of the tall and short letters.',
      },
      {
        label: 'SLNT',
        header: 'Slant',
        description:
          'The angle or inclination of the axis of letters relative to the baseline.',
      },
      {
        label: 'H',
        header: 'Hiatus',
        description:
          'The gap in writing stroke of a letter formed when the instrument leaves the paper. An opening, an interruption in the continuity of a line.',
      },
      {
        label: 'PL',
        header: 'Pen lift',
        description:
          'Is an interruption in stroke caused by removing or lifting the writing instrument from the paper.',
      },
      {
        label: 'L',
        header: 'Ligature',
        description:
          'It is a group of connected characters treated typographically as a single character, sometimes a stroke or bar connecting two letters.',
      },
      {
        label: 'RE',
        header: 'Retracing',
        description:
          'The stroke that goes back over another writing stroke.\nIn natural handwriting there may be many instances in which the pen doubles back over the same course but some retracing in fraudulent\nsignatures represents a reworking of a letter form or stroke.',
      },
      {
        label: 'WH',
        header: 'Writing habit',
        description:
          'It is persistently repeated element or detail of writing that occurs when the opportunity allows. ',
      },
    ]

    function hccAddCharacteristic(
      label,
      parentIndex,
      childIndex,
      parentId,
      childId,
    ) {
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
        .call(
          d3
            .annotation()
            .editMode(true)
            .notePadding(15)
            .type(d3.annotationCallout)
            .annotations([
              {
                note: {
                  title: label,
                  bgPadding: 10,
                },
                x: clientWidth * 0.5,
                y: clientHeight * 0.5,
                dx: 20,
                dy: 20,
                color: 'black',
              },
            ]),
        )
        .attr('stroke', 'black')
    }

    function transformAnswers(data) {
      const nAnswers = data.answers.map((section) => {
        const values = section.values.map((answer) => {
          if (!isUndefined(answer.state)) {
            const value = answer.value.map((val) => {
              const fileContent = val.file

              return {
                id: val.id,
                description: val.description,
                fileContent,
              }
            })

            return {
              id: answer.id,
              value,
            }
          }
          return answer
        })

        return {
          id: section.id,
          values,
        }
      })

      return {
        ...data,
        answers: nAnswers,
      }
    }

    function stateToCSS(state, direction) {
      return {
        transform: `scale(${state.zoom[direction]})`,
        opacity: state.opacity[direction],
        filter: `brightness(${state.brightness[direction]})`,
      }
    }

    return {
      answerForm,
      matchQuestion,
      checkModel,
      questions,
      generalDirection: general_directions,
      stateToCSS,
      snapshot,
      addImaginaryLine,
      hccCharacteristics,
      hccAddCharacteristic,
      convertDeltaContent,
      activity_id: activity._id,
      title: activity.title,
      student_id,
      transformAnswers,
      createObjectURL: URL.createObjectURL,
      wFull,
      wMax,
      mxAuto,
      pXS,
      mtTwoRem,
      mrHalfRem,
      mlAuto,
      mr,
      Inertia,
      romanStringify,
    }
  },
}
</script>

<template lang="pug">
n-layout
  n-layout-header
    n-page-header.overflow-hidden(
      :title="title",
      @back="() => Inertia.get('/')"
    )
  n-layout-content(:content-style="pXS")
    n-space.w-full(justify="center", :item-style="wFull")
      n-form(
        require-mark-placement="right-hanging",
        label-width="120",
        :style=`{
          ...wFull,
          ...wMax(1024),
          ...mxAuto,
        }`,
        :model="answerForm",
        @submit.prevent=`() => answerForm.transform((data) => transformAnswers(data))
          .post(route('post.student.activity', {
            student_id,
            activity_id,
          }), {
            _method: 'put',
          })`
      )
        n-form-item
          n-alert.w-full(title="General Instructions", :show-icon="false")
            div(v-html="convertDeltaContent(generalDirection)")

        for section, section_index in answerForm.answers
          n-form-item(:key="section.id")
            .w-full
              n-divider
              n-alert.w-full(
                :style="mtTwoRem",
                :title="`${romanStringify(section_index + 1)}. ${questions[section_index].type}`",
                :show-icon="false"
              )
                div(v-html="convertDeltaContent(questions[section_index].instruction)")

              for answer, answer_index in section.values
                n-form-item(
                  :key="answer.id",
                  :path="`answers[${section_index}].value[${answer_index}]`"
                )
                  n-card
                    if section.values.length == 1
                      n-alert(:show-icon="false")
                        |{{ questions[section_index].values[answer_index].instruction }}
                    else
                      n-alert(:show-icon="false")
                        |{{ answer_index + 1 }}.
                        | {{ questions[section_index].values[answer_index].instruction }}

                    n-form-item
                      .w-full
                        if matchQuestion(section_index, 0)
                          n-input(v-model:value="answer.value")

                        else if matchQuestion(section_index, 1)
                          n-radio-group(
                            v-model:value="answer.value",
                            :name="`activity-${section.id}-${answer.id}`"
                          )
                            n-space
                              n-radio(label="True", :value="true")
                              n-radio(label="False", :value="false")

                        else if matchQuestion(section_index, 2)
                          n-radio-group(
                            v-model:value="answer.value",
                            :name="`activity-${section.id}-${answer.id}`"
                          )
                            n-space
                              for choice in questions[section_index].values[answer_index].content
                                n-radio(
                                  :key="choice",
                                  :value="choice",
                                  :label="choice"
                                )

                        else if matchQuestion(section_index, 3)
                          //- Essay
                          .w-full
                            quill-editor(
                              theme="snow",
                              toolbar="minimal",
                              v-model:content="answer.value",
                              placeholder="Answer"
                            )

                        else if matchQuestion(section_index, 4)
                          //- Comparator
                          n-space(vertical)
                            div.w-full
                              n-layout
                                n-layout-header(bordered)
                                  n-form-item(:show-label="false")
                                    n-grid(:cols="2")
                                      n-grid-item
                                        n-space(align="center")
                                          div
                                            n-button(
                                              @click="snapshot(`hcc-${section.id}-${section_index}-${answer_index}`, section_index, answer_index)",
                                              attr-type="button",
                                              type="primary",
                                              :disabled="answer.progress.current >= answer.progress.total"
                                            ) Snapshot
                                          div
                                            n-switch(
                                              v-model:value="answer.state.mode",
                                              checked-value="r",
                                              unchecked-value="l",
                                              :disabled="answer.progress.current >= answer.progress.total"
                                            )
                                              template(#checked) Right
                                              template(#unchecked) Left
                                      n-grid-item
                                        n-progress(
                                          type="line",
                                          :percentage="Math.round((answer.progress.current / answer.progress.total) * 100)",
                                          :indicator-placement="'inside'"
                                        )
                                          span {{ answer.progress.current }} / {{ answer.progress.total }}
                                n-layout(has-sider)
                                  if !(answer.progress.current >= answer.progress.total)
                                    n-layout-sider(bordered, :width="150")
                                      n-space.pt-half(vertical, :size="[0,10]" :item-style="mrHalfRem")
                                        div
                                          span Zoom
                                          if checkModel(section_index, answer_index)
                                            n-slider(
                                              size="small",
                                              v-model:value="answer.state.zoom.left",
                                              :max="2",
                                              :min="1",
                                              :step="0.1"
                                            )
                                          else
                                            n-slider(
                                              size="small",
                                              v-model:value="answer.state.zoom.right",
                                              :max="2",
                                              :min="1",
                                              :step="0.1"
                                            )

                                        div
                                          span Brightness
                                          if checkModel(section_index, answer_index)
                                            n-slider(
                                              size="small",
                                              v-model:value="answer.state.brightness.left",
                                              :max="1",
                                              :min="0.1",
                                              :step="0.1"
                                            )
                                          else
                                            n-slider(
                                              size="small",
                                              v-model:value="answer.state.brightness.right",
                                              :max="1",
                                              :min="0.1",
                                              :step="0.1"
                                            )

                                        div
                                          span Opacity
                                          if checkModel(section_index, answer_index)
                                            n-slider(
                                              size="small",
                                              v-model:value="answer.state.opacity.left",
                                              :max="1",
                                              :min="0.1",
                                              :step="0.1"
                                            )
                                          else
                                            n-slider(
                                              size="small",
                                              v-model:value="answer.state.opacity.right",
                                              :max="1",
                                              :min="0.1",
                                              :step="0.1"
                                            )

                                        div
                                          span Gap
                                          n-slider(
                                            size="small",
                                            v-model:value="answer.state.gap",
                                            :max="20",
                                            :min="-20",
                                            :step="1"
                                          )

                                        div
                                          n-button(
                                            @click="addImaginaryLine(section_index, answer_index, section.id, answer.id)",
                                            attr-type="button",
                                            type="primary"
                                          ) Add Imaginary Line

                                  n-layout-content.w-full
                                    if !(answer.progress.current >= answer.progress.total)
                                      n-space.w-full.pt-half(vertical)
                                        .comparator-container(:id="`hcc-${section.id}-${section_index}-${answer_index}`")
                                          .comparator-images
                                             n-image-group.h-full.w-full
                                               n-grid.w-full.h-full(
                                                 :cols="2",
                                                 :x-gap="answer.state.gap",
                                               )
                                                 n-grid-item.comparator-images-item.overflow-hidden
                                                     n-h3.text-center(style="padding: 0; margin: 0;") Questioned Signature
                                                     if answer.files.questioned.isLoading
                                                       n-skeleton(height="100%", width="100%")
                                                     else
                                                       img.comparator-image(
                                                         :src="answer.files.questioned.state",
                                                         :style="stateToCSS(answer.state, 'left')"
                                                       )

                                                 n-grid-item.comparator-images-item.overflow-hidden
                                                   n-h3.text-center(style="padding: 0; margin: 0;") Standard Signature {{ answer.progress.current + 1 }}
                                                   if answer.files.samples[answer.progress.current].isLoading
                                                     n-skeleton(height="100%", width="100%")
                                                   else
                                                     img.comparator-image(
                                                       :src="answer.files.samples[answer.progress.current].state",
                                                       :style="stateToCSS(answer.state, 'right')"
                                                     )
                                          svg.comparator-svg(:id="`hcc-${section.id}-${section_index}-${answer_index}-svg`")

                                        n-space.mt-half.w-full(justify="center")
                                          n-space(justify="center")
                                            span Characteristics:
                                            for characteristic, i in hccCharacteristics
                                              n-tooltip(:key="i", trigger="hover")
                                                template(#header) {{ characteristic.header }}
                                                template(#trigger)
                                                  n-button(
                                                    text,
                                                    @click="hccAddCharacteristic(characteristic.label, section_index, answer_index, section.id, answer.id)"
                                                  ) {{ characteristic.label }}
                                                p.ws-pre {{ characteristic.description }}
                                    else
                                      n-empty(description="Done", size="large")
                                        template(#icon)
                                          n-icon(:size="50")
                                            checkup-list-icon
                          n-space(vertical)
                            for snapshot, i in answer.value
                              div(:key="snapshot.id")
                                n-divider
                                n-h3 Snapshot {{ i + 1 }} / {{ answer.progress.total }}
                                n-space(vertical)
                                  n-space(justify="center")
                                    n-image.mx-auto(:src="createObjectURL(snapshot.file)")
                                  quill-editor(
                                    theme="snow",
                                    toolbar="minimal",
                                    v-model:content="snapshot.description",
                                    placeholder="Conclusion"
                                  )
        n-form-item
          n-button(
            type="primary",
            attr-type="submit",
            :loading="answerForm.processing",
            :style="{...mlAuto, ...mr(0)}"
          ) Submit
</template>

<style scoped>
.comparator-container {
  position: relative;
  min-height: 250px;
  width: 100%;
  height: 100%;
  background-color: white;
}

.comparator-images {
  position: absolute;
  top: 0;
  left: 50%;
  transform: translate(-50%, 0);
  width: 80%;
  height: 80%;
}

.comparator-images-item {
  border-style: solid;
  border-width: 0.75px;
}

.comparator-image {
  object-fit: contain;
  width: 100%;
  height: 100%;
}

.comparator-svg {
  width: 100%;
  position: absolute;
  height: 100%;
  top: 0;
  left: 0;
}
</style>

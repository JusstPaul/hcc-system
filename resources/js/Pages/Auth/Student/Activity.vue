<script>
import mobile from 'is-mobile'
import { ref } from 'vue'
import { convert } from 'html-to-text'
import { useAsyncState } from '@vueuse/core'
import { isUndefined } from 'lodash'
import { nanoid } from 'nanoid'
import { stringify as romanStringify } from 'roman-numerals-convert'
import { Inertia } from '@inertiajs/inertia'
import { useForm, usePage } from '@inertiajs/inertia-vue3'
import { jsPDF } from 'jspdf'
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
  NModal,
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
    NModal,
    QuillEditor,
    CheckupListIcon,
  },
  props: {
    student_id: String,
    activity: Object,
  },
  setup({ activity, student_id }) {
    if (mobile()) {
      screen.orientation.lock('landscape')
    }

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
                  collapsed: false,
                },
                files: {
                  questioned: questioned,
                  samples: samples,
                },
                value: {
                  snapshots: [],
                  conclusionType: null,
                  conclusion: '',
                },
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

      const collapsed =
        answerForm.answers[parentIndex].values[childIndex].state.collapsed

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
        collapsed,
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

            answerForm.answers[parentIndex].values[
              childIndex
            ].value.snapshots.push({
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
        label: '1',
        header: 'Pen pressure',
        abbrev: 'PP',
        description: 'The average force with which the pen contracts the paper',
      },
      {
        label: '2',
        header: 'Patching',
        abbrev: 'PCH',
        description:
          'The retouching or going back over a defective writing stroke.',
      },
      {
        label: '3',
        header: 'Tremor',
        abbrev: 'TRM',
        description: 'The irregular shaky stroke.',
      },
      {
        label: '4',
        header: 'Stroke',
        abbrev: 'STR',
        description:
          'These are series of lines or curves within a single letter.',
      },
      {
        label: '5',
        header: 'Diacritic',
        abbrev: 'DIA',
        description:
          'It is a sign added to a letter or symbol to give it a particular phonetic value.',
      },
      {
        label: '6',
        header: 'Baseline',
        abbrev: 'BA',
        description:
          'It is the ruled or imaginary line upon which the writing rests.',
      },
      {
        label: '7',
        header: 'Alignment',
        abbrev: 'AL',
        description:
          'The relation of parts of the whole line of writing or line of individual letter in words to the baseline.',
      },
      {
        label: '8',
        header: 'Line quality',
        abbrev: 'LQ',
        description:
          'Refers to the overall character of the written strokes from the initial to the terminal.',
      },
      {
        label: '9',
        header: 'Lateral spacing',
        abbrev: 'LS',
        description:
          'The horizontal dimension of writing produced by the width of letters, the space between letters and words, and the width of margins.',
      },
      {
        label: '10',
        header: 'Natural variation',
        abbrev: 'NV',
        description:
          'The normal or usual deviation found in repeated specimen of any individual handwriting.',
      },
      {
        label: '11',
        header: 'Rhythm',
        abbrev: 'RH',
        description:
          'The element of the writing movement marked by regular or periodic recurrences. It may be classed as smooth, intermittent, or jerky in its quality.',
      },
      {
        label: '12',
        header: 'Proportion',
        abbrev: 'PR',
        description: 'It is the relation of the tall and short letters.',
      },
      {
        label: '13',
        header: 'Slant',
        abbrev: 'SLT',
        description:
          'The angle or inclination of the axis of letters relative to the baseline.',
      },
      {
        label: '14',
        header: 'Hiatus',
        abbrev: 'HIA',
        description:
          'The gap in writing stroke of a letter formed when the instrument leaves the paper. An opening, an interruption in the continuity of a line.',
      },
      {
        label: '15',
        header: 'Pen lift',
        abbrev: 'PL',
        description:
          'Is an interruption in stroke caused by removing or lifting the writing instrument from the paper.',
      },
      {
        label: '16',
        header: 'Ligature',
        abbrev: 'LG',
        description:
          'It is a group of connected characters treated typographically as a single character, sometimes a stroke or bar connecting two letters.',
      },
      {
        label: '17',
        header: 'Retracing',
        abbrev: 'RT',
        description:
          'The stroke that goes back over another writing stroke.\nIn natural handwriting there may be many instances in which the pen doubles back over the same course but some retracing in fraudulent\nsignatures represents a reworking of a letter form or stroke.',
      },
      {
        label: '18',
        header: 'Writing habit',
        abbrev: 'WH',
        description:
          'It is persistently repeated element or detail of writing that occurs when the opportunity allows. ',
      },
      {
        label: '19',
        header: 'Hook or Trough',
        abbrev: 'HT',
        description: 'The bend, crook or curve on the inner of the bottom loop or curve of small letter.'
      },
      {
        label: '20',
        header: 'Hump',
        abbrev: 'HMP',
        description: 'The rounded outside of the bend, crook or curve in small letters.'
      },
      {
        label: '21',
        header: 'Diacritic',
        abbrev: 'DAT',
        description: 'An element added to complete certain letters.'
      },
      {
        label: '22',
        header: 'Eye loop or Eyelet',
        abbrev: 'EL',
        description: 'The small loop formed by strokes and extend in divergent direction.'
      },
      {
        label: '23',
        header: 'Blunt Ending or Beginning',
        abbrev: 'BLE',
        description: 'Blunt ending and initial strokes are results of the drawing process in forgery.'
      },
      {
        label: '24',
        header: 'Buckle Knot',
        abbrev: 'BK',
        description: 'The horizontal and looped stokes that are often used to complete such letters.'
      },
      {
        label: '25',
        header: 'Arc or Arch',
        abbrev: 'ARC',
        description: 'Any arcade form in the body of the letter.'
      }
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
            const snapshots = answer.value.snapshots.map((val) => {
              const fileContent = val.file

              return {
                id: val.id,
                description: val.description,
                fileContent,
              }
            })

            return {
              id: answer.id,
              value: {
                snapshots,
                conclusion: answer.value.conclusion,
                conclusionType: answer.value.conclusionType,
              },
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
      if (direction === 'left') {
        return {
          transform: `scale(${state.zoom[direction]})`,
          opacity: state.opacity[direction],
          filter: `brightness(${state.brightness[direction]})`,
          marginRight: `${state.gap}px`,
        }
      }

      return {
        transform: `scale(${state.zoom[direction]})`,
        opacity: state.opacity[direction],
        filter: `brightness(${state.brightness[direction]})`,
        marginLeft: `${state.gap}px`,
      }
    }

    function toPDF(data, conclusion, title) {
      const doc = new jsPDF({
        unit: 'in',
        format: 'letter',
        orientation: 'portrait',
      })
      doc.setFontSize(12)

      data.map(({ file, description }) => {
        doc.addImage(URL.createObjectURL(file), 'JPEG', 0, 2)

        const html = convertDeltaContent(description)
        const text = convert(html)
        doc.text(text, 2, 5)

        doc.addPage('letter')
      })

      const html = convertDeltaContent(conclusion)
      const text = convert(html)
      doc.text(text, 2, 2)

      doc.save(title)
    }

    const showConfirm = ref(false)

    function submit() {
      answerForm
        .transform((data) => transformAnswers(data))
        .post(
          route('post.student.activity', {
            student_id,
            activity_id: activity._id,
          }),
          {
            _method: 'put',
          },
        )
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
      toPDF,
      showConfirm,
      submit,
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
        @submit.prevent="() => showConfirm = !showConfirm"
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
                                    n-layout-sider(
                                      bordered,
                                      show-trigger,
                                      collapse-mode="width",
                                      :collapsed-width="2",
                                      :width="175",
                                      :collapsed="answer.state.collapsed",
                                      @collapse="() => answer.state.collapsed = true",
                                      @expand="() => answer.state.collapsed = false"
                                    )
                                      if !answer.state.collapsed
                                        n-space.pt-half.pr-xs(vertical, :size="[0,10]" :item-style="mrHalfRem")
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
                                                :max="2",
                                                :min="0.1",
                                                :step="0.1"
                                              )
                                            else
                                              n-slider(
                                                size="small",
                                                v-model:value="answer.state.brightness.right",
                                                :max="2",
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
                                              :min="-100",
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
                                                     .overflow-hidden.h-full
                                                       if answer.files.questioned.isLoading
                                                         n-skeleton(height="100%", width="100%")
                                                       else
                                                         img.comparator-image(
                                                           :src="answer.files.questioned.state",
                                                           :style="stateToCSS(answer.state, 'left')",
                                                         )

                                                 n-grid-item.comparator-images-item.overflow-hidden
                                                   n-h3.text-center(style="padding: 0; margin: 0;") Standard Signature {{ answer.progress.current + 1 }}
                                                   .overflow-hidden.h-full
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
                                                    @click="hccAddCharacteristic(characteristic.abbrev, section_index, answer_index, section.id, answer.id)"
                                                  ) {{ characteristic.label }}
                                                p.ws-pre {{ characteristic.description }}
                                    else
                                      n-empty(description="Done", size="large")
                                        template(#icon)
                                          n-icon(:size="50")
                                            checkup-list-icon
                                        template(#extra)
                                          n-button(@click="() => toPDF(answer.value.snapshots, answer.value.conclusion, `${title} ${section_index + 1}.pdf`)") Save
                          n-space(vertical)
                            for snapshot, i in answer.value.snapshots
                              div(:key="snapshot.id")
                                n-divider
                                n-h3 Snapshot {{ i + 1 }} / {{ answer.progress.total }}
                                n-space(vertical)
                                  n-space(justify="center")
                                    n-image.mx-auto(:src="createObjectURL(snapshot.file)", object-fit="contain", height="300")
                                  quill-editor(
                                    theme="snow",
                                    toolbar="minimal",
                                    v-model:content="snapshot.description",
                                    placeholder="Evaluation"
                                  )

                            .w-full
                              n-divider
                              n-form-item(:show-label="false")
                                n-space.w-full(vertical)
                                  n-select(
                                    v-model:value="answer.value.conclusionType",
                                    :options=`[
                                      {
                                          label: 'Genuine',
                                          value: 'Genuine'
                                      },
                                      {
                                          label: 'Forged',
                                          value: 'Forged'
                                      }
                                    ]`,
                                    placeholder="Overall Conclusion"
                                  )
                                  quill-editor(
                                    theme="snow",
                                    toolbar="minimal",
                                    v-model:content="answer.value.conclusion",
                                    placeholder="Justify your conclusion"
                                  )
        n-form-item
          n-button(
            type="primary",
            attr-type="submit",
            :loading="answerForm.processing",
            :style="{...mlAuto, ...mr(0)}"
          ) Submit
n-modal(
  v-model:show="showConfirm",
  preset="dialog",
  title="Confirm",
  content="Are you sure you want to submit the activity?",
  positive-text="Submit",
  negative-text="Cancel",
  @positive-click="() => submit()"
)
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

import { Fragment, useEffect } from 'react'
import { isArray, isBoolean, isObject } from 'lodash'
import AdminLayout from '@components/layouts/admin'
import Head from '@components/head'
import Header from '@components/header'
import Container from '@components/container'
import Table, { THead } from '@components/table'

const Classrooms = ({
  school_year: schoolYear,
  classrooms,
  has_instructors: hasInstructors,
}) => {
  if (import.meta.env.DEV) {
    useEffect(() => {
      ;(async () => {
        const joi = (await import('joi')).default
        joi.boolean().validate(hasInstructors)
      })().catch(console.error)
    }, [schoolYear, classrooms, hasInstructors])
  }

  return (
    <Fragment>
      <Head title="Classrooms" />
      <AdminLayout>
        <Header>
          <h1 className="text-xl font-bold">Classrooms</h1>
        </Header>

        <Container className="overflow-auto">
          <Table isEmpty={classrooms.length === 0} emptyMessage="No Classrooms">
            <THead
              headings={[
                'Section',
                'Instructor',
                'Room',
                'Time',
                'Days',
                'Action',
              ]}
            />
            <tbody></tbody>
          </Table>
        </Container>
      </AdminLayout>
    </Fragment>
  )
}
export default Classrooms

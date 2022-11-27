import { Fragment } from 'react'
import AdminLayout from '@components/layouts/admin'
import Head from '@components/head'
import Header from '@components/header'

const Classrooms = ({
  school_year: schoolYear,
  classrooms,
  has_instructions: hasInstructions,
}) => {
  return (
    <Fragment>
      <Head title="Classrooms" />
      <AdminLayout>
        <Header>
          <h1 className="text-xl font-bold">Classrooms</h1>
        </Header>
      </AdminLayout>
    </Fragment>
  )
}
export default Classrooms

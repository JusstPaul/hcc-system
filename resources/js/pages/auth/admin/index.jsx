import { Fragment } from 'react'
import AdminLayout from '@components/layouts/admin'
import Head from '@components/head'

const AdminIndex = () => {
  return (
    <Fragment>
      <Head title="Users" />
      <AdminLayout>Admin</AdminLayout>
    </Fragment>
  )
}
export default AdminIndex

import { Fragment } from 'react'
import { Link } from '@inertiajs/inertia-react'
import { UserPlusIcon } from '@heroicons/react/20/solid'
import AdminLayout from '@components/layouts/admin'
import Head from '@components/head'
import Header from '@components/header'

const AdminIndex = ({ users }) => {
  if (import.meta.env.DEV) {
    console.log(users)
  }

  return (
    <Fragment>
      <Head title="Users" />
      <AdminLayout>
        <Header>
          <h1 className="text-xl font-bold">Users</h1>
          <Link href={route('admin.create_user')} className="btn flex gap-2">
            <span aria-hidden="true">
              <UserPlusIcon className="h-5" />
            </span>
            <span>Create</span>
          </Link>
        </Header>
      </AdminLayout>
    </Fragment>
  )
}
export default AdminIndex

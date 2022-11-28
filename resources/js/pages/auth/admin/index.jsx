import { Fragment, useEffect } from 'react'
import { isNull } from 'lodash'
import { Link } from '@inertiajs/inertia-react'
import { UserPlusIcon } from '@heroicons/react/20/solid'
import AdminLayout from '@components/layouts/admin'
import Head from '@components/head'
import Header from '@components/header'
import Container from '@components/container'
import Table, { THead, TData } from '@components/table'
import { profileToName } from '@js/utils'

const AdminIndex = ({ users }) => {
  if (import.meta.env.DEV) {
    useEffect(() => {
      ;(async () => {
        const joi = (await import('joi')).default

        joi
          .object({
            current_page: joi.number().integer().min(1).required(),
            data: joi.array().items(
              joi.object({
                _id: joi.string(),
                username: joi.string(),
                role_name: joi.string(),
              }),
            ),
            first_page_url: joi.string().required(),
            last_page: joi.number().integer().min(1).required(),
            last_page_url: joi.string().required(),
            next_page_url: joi.string().empty(null),
            prev_page_url: joi.string().empty(null),
            links: joi.array().items(
              joi.object({
                url: joi.string().empty(null),
                label: joi.string(),
                active: joi.boolean(),
              }),
            ),
            path: joi.string(),
            per_page: joi.number().integer(),
            total: joi.number().integer().min(1),
          })
          .validate(users)
      })().catch(console.error)
    }, [users])
  }

  const emptyMessage = () => {
    if (users.data.length === 0) {
      if (isNull(users.next_page_url)) {
        return 'End of User list'
      }
    }
    return 'No Users'
  }

  const _users = (() => {
    return users.data.map(
      ({ _id: id, username, role_name: roleName, profile }) => ({
        id,
        list: [username, profileToName(profile), roleName, 'Action'],
      }),
    )
  })()

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

        <Container className="overflow-auto">
          <Table
            isEmpty={users.data.length === 0}
            emptyMessage={emptyMessage()}
          >
            <THead headings={['Username', 'Name', 'Role', 'Action']} />
            <tbody>
              <TData data={_users} />
            </tbody>
          </Table>
          <Link href={users.next_page_url} only={['users']}>
            Next
          </Link>
        </Container>
      </AdminLayout>
    </Fragment>
  )
}
export default AdminIndex

import { Fragment, useEffect } from 'react'
import { isArray, isString } from 'lodash'
import { useForm } from '@inertiajs/inertia-react'
import AdminLayout from '@components/layouts/admin'
import Head from '@components/head'
import Header from '@components/header'
import BackLink from '@components/back-link'
import Container from '@components/container'
import Form, { Fieldset, TextInput, Select } from '@components/form'

const CreateUser = ({ roles }) => {
  /** Validate args */
  if (import.meta.env.DEV) {
    useEffect(() => {
      if (isArray(roles)) {
        const res = roles.map((v) => isString(v)).reduce((a, v) => a && v)
        if (!res) {
          console.error('Props Type: (role.*) is expected to be string')
        }
      } else {
        console.error('Props Type: (role) is expected to be array')
      }
    }, [roles])
  }

  const { data, setData, post, processing, errors } = useForm({
    username: '',
    role: '',
    lName: '',
    mName: '',
    fName: '',
    details: {
      contact: '',
      email: '',
      contactPerson: '',
      contactPerson: '',
    },
  })

  const handleTextChange = (e) => {
    const { name, value } = e.target
    switch (name) {
      case 'username':
        setData(name, value)
        break
    }
  }

  const roleOptions = roles.map((v) => ({
    label: `${v.charAt(0).toUpperCase()}${v.slice(1)}`,
    value: v,
  }))

  return (
    <Fragment>
      <Head title="Create User" />
      <AdminLayout>
        <Header>
          <BackLink />
          <h1 className="text-xl font-bold">Create User</h1>
        </Header>
        <Container width="max-w-lg" className="pt-4">
          <Form
            legend="Create User Form"
            onSubmit={(_) => {
              const target = route('post.admin.create_user')
              // post(target)
              console.log(data)
            }}
            id="create-user-form"
          >
            <Fieldset legend="User Type">
              <div className="flex flex-col md:flex-row gap-2">
                <TextInput
                  name="username"
                  label="Username"
                  type="text"
                  id="username"
                  className="grow mb-auto"
                  value={data.username}
                  onChange={handleTextChange}
                  error={errors.username}
                  autoFocus
                />

                <Select
                  id="role"
                  label="Role"
                  form="create-user-form"
                  placeholder="Select a role"
                  className="md:w-fit grow-0 mb-auto"
                  options={roleOptions}
                  value={data.role}
                  onChange={(e) => setData('role', e.target.value)}
                  error={errors.role}
                />
              </div>
            </Fieldset>

            <Fieldset legend="User Details" isLegendVisible></Fieldset>
          </Form>
        </Container>
      </AdminLayout>
    </Fragment>
  )
}
export default CreateUser

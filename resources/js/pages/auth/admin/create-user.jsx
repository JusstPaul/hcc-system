import { Fragment, useEffect, useMemo } from 'react'
import { isArray, isString } from 'lodash'
import { useForm } from '@inertiajs/inertia-react'
import AdminLayout from '@components/layouts/admin'
import Head from '@components/head'
import Header from '@components/header'
import BackLink from '@components/back-link'
import Container from '@components/container'
import Button from '@components/button'
import Form, { Fieldset, TextInput, Select } from '@components/form'

const DetailFields = ({ data, setData }) => {
  if (data.role === 'admin') {
    return <></>
  }
  const roleStudent = data.role === 'student'
  const roleStudentOrInstructor =
    data.role === 'instructor' || data.role === 'student'

  const handleDetailsInput = (e) => {
    const { value, name } = e.target
    const details = {
      ...data.details,
      [name]: value,
    }
    setData('details', details)
  }

  return (
    <div className="flex flex-col gap-2">
      {roleStudentOrInstructor ? (
        <div className="md:flex md:gap-2">
          <TextInput
            name="contact"
            label="Contact Number"
            type="text"
            id="contact"
            className="grow"
            value={data.details.contact}
            onChange={(e) => handleDetailsInput(e)}
            maxLength={11}
          />

          <TextInput
            name="email"
            label="Email"
            type="email"
            id="email"
            className="grow"
            value={data.details.email}
            onChange={(e) => handleDetailsInput(e)}
          />
        </div>
      ) : (
        <></>
      )}
      {roleStudent ? (
        <div className="md:flex md:gap-2">
          <TextInput
            name="contactPerson"
            label="Contact Person"
            type="text"
            id="contactPerson"
            className="grow"
            value={data.details.contactPerson}
            onChange={(e) => handleDetailsInput(e)}
          />
          <TextInput
            name="contactPersonContact"
            label="Contact Number"
            type="text"
            id="contactPersonContact"
            className="grow"
            value={data.details.contactPersonContact}
            onChange={(e) => handleDetailsInput(e)}
          />
        </div>
      ) : (
        <></>
      )}
    </div>
  )
}

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
      contactPersonContact: '',
    },
  })

  const handleTextChange = (e) => {
    const { name, value } = e.target
    setData(name, value)
  }

  const roleOptions = roles.map((v) => ({
    label: `${v.charAt(0).toUpperCase()}${v.slice(1)}`,
    value: v,
  }))

  const detailsErrorMessage = useMemo(() => {
    return <></>
  }, [data.lName, data.details])

  return (
    <Fragment>
      <Head title="Create User" />

      <AdminLayout>
        <Header>
          <BackLink />
          <h1 className="text-xl font-bold">Create User</h1>
        </Header>

        <Container width="max-w-4xl" className="pt-4">
          <Form
            legend="Create User Form"
            onSubmit={(_) => {
              const target = route('post.admin.create_user')
              post(target)
            }}
            id="create-user-form"
          >
            <Fieldset legend="User Credentials" className="w-full">
              {detailsErrorMessage}

              <div className="flex flex-col md:flex-row gap-2">
                <TextInput
                  name="username"
                  label="Username"
                  type="text"
                  id="username"
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
                  className="md:w-fit"
                  options={roleOptions}
                  value={data.role}
                  onChange={(e) => setData('role', e.target.value)}
                  error={errors.role}
                />
              </div>
            </Fieldset>

            <Fieldset legend="User Details" isLegendVisible>
              <div className="flex flex-col gap-2">
                <div className="flex flex-col flex-wrap md:flex-row gap-2">
                  <TextInput
                    name="lName"
                    label="Last Name"
                    type="text"
                    id="lName"
                    className="grow"
                    value={data.lName}
                    onChange={handleTextChange}
                    error={errors.lName}
                  />

                  <TextInput
                    name="fName"
                    label="First Name"
                    type="text"
                    id="fName"
                    className="grow"
                    value={data.fName}
                    onChange={handleTextChange}
                    error={errors.fName}
                  />

                  <TextInput
                    name="mName"
                    label="Middle Name"
                    type="text"
                    id="mName"
                    className="grow md:grow-0 lg:grow"
                    value={data.mName}
                    onChange={handleTextChange}
                    error={errors.mName}
                  />
                </div>

                <DetailFields data={data} setData={setData} />
              </div>
            </Fieldset>

            <Button
              className="mt-4 ml-auto"
              disabled={processing}
              type="submit"
            >
              Submit
            </Button>
          </Form>
        </Container>
      </AdminLayout>
    </Fragment>
  )
}
export default CreateUser

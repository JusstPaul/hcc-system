import { useForm } from '@inertiajs/inertia-react'
import Container from '@components/container'
import Form, { TextInput } from '@components/form'
import Button from '@components/button'

const Login = () => {
  const { data, setData, post, processing, errors } = useForm({
    username: '',
    password: '',
    remember: 0,
  })

  const handleTextChange = (e) => {
    const { name, value } = e.target
    switch (name) {
      case 'username':
      case 'password':
        setData(name, value)
        break
    }
  }

  return (
    <Container width="max-w-md flex flex-col">
      <h1 className="text-center py-14 text-2xl font-bold">
        Handwriting Comparator Ex
      </h1>
      <div className="flex">
        <Form
          legend="Login Form"
          className="m-auto"
          onSubmit={(_e) => {
            console.log(data)
          }}
        >
          <TextInput
            name="username"
            label="Username"
            type="text"
            id="username"
            value={data.username}
            onChange={handleTextChange}
          />
          <TextInput
            name="password"
            label="Password"
            type="password"
            id="password"
            value={data.password}
            onChange={handleTextChange}
          />

          <Button className="mt-4" type="submit">
            Submit
          </Button>
        </Form>
      </div>
    </Container>
  )
}
export default Login

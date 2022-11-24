import { useEffect } from 'react'
import { useForm } from '@inertiajs/inertia-react'
import Container from '@components/container'
import Form, { TextInput, PasswordInput } from '@components/form'
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
    <Container width="max-w-sm relative">
      <div className="login-position">
        <Form
          legend="Login Form"
          onSubmit={(_e) => {
            const target = route('post.login')
            post(target)
          }}
          className="w-full"
        >
          <h1 className="text-center pb-14 text-2xl font-black">
            Handwriting Comparator Ex
          </h1>
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
          <PasswordInput
            name="password"
            label="Password"
            id="password"
            value={data.password}
            onChange={handleTextChange}
          />

          <Button className="mt-4" disabled={processing} type="submit">
            Submit
          </Button>
        </Form>
      </div>
    </Container>
  )
}
export default Login

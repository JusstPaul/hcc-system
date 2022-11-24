import { Fragment } from 'react'
import { Link } from '@inertiajs/inertia-react'

const AuthLayout = ({ children }) => {
  // @ts-ignore
  const logoutRoute = route('post.logout')

  return (
    <Fragment>
      <Link href={logoutRoute} method="post" as="button" type="button">
        Logout
      </Link>
      {children}
    </Fragment>
  )
}
export default AuthLayout

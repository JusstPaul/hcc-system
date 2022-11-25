import { Fragment, useMemo } from 'react'
import { Link, usePage } from '@inertiajs/inertia-react'
import {
  ArrowLeftOnRectangleIcon,
  UserCircleIcon,
} from '@heroicons/react/20/solid'

const NavLink = ({ className, icon: Icon, label, ...props }) => {
  const _className = (() => {
    const c = 'inline-flex gap-1 py-2 px-4 w-full rounded hover:bg-primary-200'
    if (className) return `${c} ${className}`
    return c
  })()

  return (
    <Link className={_className} {...props}>
      {Icon ? (
        <span aria-hidden="true">
          <Icon className="h-6" />
        </span>
      ) : (
        <></>
      )}
      {label ? <span>{label}</span> : <></>}
    </Link>
  )
}

const AuthLayout = ({ children, navigation = [] }) => {
  const { user } = usePage().props

  const logoutRoute = route('post.logout')
  const profileRoute = useMemo(() => {
    switch (user.role) {
      case 'admin':
        return route('admin.profile')
      case 'instructor':
        return route('instructor.profile')
      case 'student':
        return route('instructor.student')
    }
  }, [user])

  const mainNav = useMemo(() => {
    return navigation.map(({ link, label, icon }, i) => (
      <li className="py-2" key={i}>
        <NavLink href={link} label={label} icon={icon} />
      </li>
    ))
  }, [navigation])

  return (
    <Fragment>
      <aside className="text-center fixed md:static w-9/12 md:w-60 h-full p-6 border-r text-primary-900 bg-gray-50">
        <nav
          className="flex flex-col justify-between h-full mx-auto md:mx-0"
          aria-aria-labelledby="main-nav-title"
        >
          <h4 className="sr-only" id="main-nav-title">
            Primary Navigation
          </h4>
          <ul aria-label="Main Navigation">{mainNav}</ul>
          <ul aria-label="Session Navigation">
            <li className="py-2">
              <NavLink
                href={profileRoute}
                icon={UserCircleIcon}
                label="Profile"
              />
            </li>
            <li className="py-2">
              <NavLink
                href={logoutRoute}
                as="button"
                method="POST"
                icon={ArrowLeftOnRectangleIcon}
                label="Logout"
              />
            </li>
          </ul>
        </nav>
      </aside>
      <main className="px-6 py-10">{children}</main>
    </Fragment>
  )
}
export default AuthLayout

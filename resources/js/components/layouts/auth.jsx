import { Fragment, useMemo, useState, useEffect, useRef } from 'react'
import { Link, usePage } from '@inertiajs/inertia-react'
import {
  ArrowLeftOnRectangleIcon,
  UserCircleIcon,
  Bars3Icon,
} from '@heroicons/react/20/solid'
import { isMdScreen } from '@js/utils'

const NavLink = ({
  className,
  icon: Icon,
  label,
  isActive = false,
  ...props
}) => {
  const _className = (() => {
    const c = `inline-flex gap-1 py-2 px-4 w-full rounded ${
      isActive ? 'bg-primary-500 text-gray-50' : 'hover:text-primary-500'
    }`
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
  const [isVisible, setVisible] = useState(false)
  const isMd = isMdScreen()

  const toggler = useMemo(() => {
    if (isMd) {
      setVisible(true)
      return <></>
    }
    setVisible(false)
    return (
      <button
        type="button"
        role="switch"
        aria-checked={isVisible}
        aria-controls="main-sidebar"
        onClick={() => setVisible(true)}
        className="p-2 hover:bg-gray-100 rounded-md"
      >
        <span className="sr-only">Toggle Navigation Visibility</span>
        <span aria-hidden="true">
          <Bars3Icon className="h-6" />
        </span>
      </button>
    )
  }, [isMd])

  const togglerClassName = useMemo(() => {
    if (isMd) {
      return ''
    }
    return '-translate-x-full'
  }, [isMd])

  const sidebarRef = useRef(null)

  useEffect(() => {
    const handleClickOutside = (e) => {
      if (sidebarRef.current && !sidebarRef.current.contains(e.target)) {
        setVisible(false)
      }
    }
    document.addEventListener('mousedown', handleClickOutside)

    return () => {
      document.removeEventListener('mousedown', handleClickOutside)
    }
  }, [sidebarRef])

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
    return navigation.map(({ link, label, icon, isActive }, i) => (
      <li className="py-2" key={i}>
        <NavLink href={link} label={label} icon={icon} isActive={isActive} />
      </li>
    ))
  }, [navigation])

  return (
    <Fragment>
      <aside
        id="main-sidebar"
        className={`text-center fixed top-0 left-0 h-screen w-9/12 md:w-60 px-6 py-2 border-r bg-gray-50 ${
          isMd ? '' : 'duration-150 ease-in-out'
        } ${isVisible ? 'translate-x-0' : togglerClassName}`}
        aria-label="Main Sidebar"
        aria-expanded={isVisible}
        ref={sidebarRef}
      >
        <nav
          className="flex flex-col justify-between h-full mx-auto md:mx-0 duration-150"
          aria-labelledby="main-nav-title"
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
      <main className="md:ml-60 px-2 py-1 grow">
        <header className="flex gap-4 mb-4 py-2 border-b">
          {toggler}
          <span id="header" className="grow p-2 flex gap-4"></span>
        </header>
        <div className="px-2 py-8">{children}</div>
      </main>
    </Fragment>
  )
}
export default AuthLayout

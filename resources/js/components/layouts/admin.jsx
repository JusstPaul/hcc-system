import { UserGroupIcon, AcademicCapIcon } from '@heroicons/react/20/solid'
import AuthLayout from './auth'

const AdminLayout = ({ children }) => {
  return (
    <AuthLayout
      navigation={[
        {
          link: route('admin.index'),
          label: 'Users',
          icon: UserGroupIcon,
        },
        {
          link: route('admin.create_user'),
          label: 'Classrooms',
          icon: AcademicCapIcon,
        },
      ]}
    >
      <header>Admin Panel</header>
      {children}
    </AuthLayout>
  )
}
export default AdminLayout

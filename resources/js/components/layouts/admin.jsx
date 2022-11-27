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
          link: route('admin.classrooms'),
          label: 'Classrooms',
          icon: AcademicCapIcon,
        },
      ]}
    >
      {children}
    </AuthLayout>
  )
}
export default AdminLayout

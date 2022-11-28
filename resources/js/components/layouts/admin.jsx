import { UserGroupIcon, AcademicCapIcon } from '@heroicons/react/20/solid'
import AuthLayout from './auth'

const AdminLayout = ({ children }) => {
  return (
    <AuthLayout
      navigation={[
        (() => {
          const link = route('admin.index')
          const isActive = route().current('admin.index')

          return {
            link,
            isActive,
            label: 'Users',
            icon: UserGroupIcon,
          }
        })(),
        (() => {
          const link = route('admin.classrooms')
          const isActive = route().current('admin.classrooms')

          return {
            link,
            isActive,
            label: 'Classrooms',
            icon: AcademicCapIcon,
          }
        })(),
      ]}
    >
      {children}
    </AuthLayout>
  )
}
export default AdminLayout

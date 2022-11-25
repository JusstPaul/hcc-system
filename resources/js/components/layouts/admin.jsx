import { UserGroupIcon, AcademicCapIcon } from '@heroicons/react/20/solid'
import Header from '../header'
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
      <Header>
        <h1 className="font-bold text-xl">Admin Panel</h1>
      </Header>
      {children}
    </AuthLayout>
  )
}
export default AdminLayout

import { usePage, Link } from '@inertiajs/inertia-react'
import { ArrowLeftIcon } from '@heroicons/react/20/solid'

const BackLink = () => {
  const { prevURL } = usePage().props

  return (
    <Link href={prevURL} aria-label="Go Back" className="py-1 px-2">
      <span aria-hidden="true">
        <ArrowLeftIcon className="h-5" />
      </span>
    </Link>
  )
}
export default BackLink

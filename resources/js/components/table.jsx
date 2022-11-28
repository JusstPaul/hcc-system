import { Fragment } from 'react'
import { NoSymbolIcon } from '@heroicons/react/20/solid'

const Table = ({
  className,
  children,
  isEmpty = false,
  emptyMessage = 'Empty',
  emptyIcon: EmptyIcon = () => <NoSymbolIcon className="h-20" />,
  ...props
}) => {
  const _className = (() => {
    const c = 'w-full table-fixed border-collapse text-sm bg-white'
    if (className) return `${c} ${className}`
    return c
  })()

  const Empty = () => {
    if (isEmpty)
      return (
        <div className="w-full flex flex-col justify-center py-6 text-gray-700 bg-white">
          <span aria-hidden="true" className="mx-auto mb-4">
            <EmptyIcon />
          </span>
          <h4 className="mx-auto font-medium text-3xl">{emptyMessage}</h4>
        </div>
      )
    return <></>
  }

  return (
    <Fragment>
      <table className={_className} {...props}>
        {children}
      </table>
      <Empty />
      <div className="bg-gray-100 h-4 w-full rounded-b-lg border-t border-gray-300"></div>
    </Fragment>
  )
}
export default Table

const THead = ({ headings = [], className }) => {
  const headingsLength = headings.length - 1

  const _className = (index) => {
    const c = (() => {
      const _c =
        'border-gray-300 font-semibold p-4 pt-6 text-left bg-gray-100 border-b'

      const additional = (() => {
        switch (index) {
          case 0:
            return 'pl-8 rounded-tl-lg'
          case headingsLength:
            return 'pr-8 rounded-tr-lg'
          default:
            return ''
        }
      })()

      return `${_c} ${additional}`
    })()
    if (className) return `${c} ${className}`
    return c
  }

  const _headings = headings.map((v, i) => (
    <th key={i} className={_className(i)}>
      {v}
    </th>
  ))

  return (
    <thead>
      <tr>{_headings}</tr>
    </thead>
  )
}

const TData = ({ className, data, ...props }) => {
  const dataLength = (() => {
    if (data.length === 0) return 0
    return data[0].length - 1
  })()

  const _className = (index) => {
    const c = (() => {
      const _c = 'p-4 text-left border-b'

      const additional = (() => {
        switch (index) {
          case 0:
            return 'pl-8'
          case dataLength:
            return 'pr-8'
          default:
            return ''
        }
      })()

      return `${_c} ${additional}`
    })()

    if (className) return `${c} ${className}`
    return c
  }

  return data.map(({ id, list }) => (
    <tr key={id}>
      {list.map((v, i) => (
        <td key={i} className={_className(i)}>
          {v}
        </td>
      ))}
    </tr>
  ))
}

export { THead, TData }

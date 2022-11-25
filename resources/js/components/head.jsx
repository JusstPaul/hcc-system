import { Helmet } from 'react-helmet-async'

const Head = ({ title, children, ...props }) => {
  const _title = (() => {
    const t = 'Handwriting Comparator Ex'
    if (title) return `${title} | ${t}`
    return t
  })()

  return (
    <Helmet title={_title} {...props}>
      {children}
    </Helmet>
  )
}
export default Head

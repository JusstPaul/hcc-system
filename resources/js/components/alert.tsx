const Alert = ({ children, title, icon, className, ...props }) => {
  const _className = (() => {
    const c = ''
    if (className) return `${c} ${className}`
    return c
  })()

  return (
    <div role="alert" className={_className} {...props}>
      <div></div>
      <div>{children}</div>
    </div>
  )
}
export default Alert

const Button = ({ children, className, disabled, ...props }) => {
  const _className = (() => {
    const c = 'btn'
    if (className) return `${c} ${className}`
    return c
  })()

  return (
    <button className={_className} disabled={disabled} {...props}>
      {children}
    </button>
  )
}
export default Button

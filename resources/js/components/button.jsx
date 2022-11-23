const Button = ({ children, className, ...props }) => {
  const _className = (() => {
    const c = 'rounded bg-primary-500 text-gray-50 py-1.5 font-semibold'
    if (className) return `${c} ${className}`
    return c
  })()

  return (
    <button className={_className} {...props}>
      {children}
    </button>
  )
}
export default Button

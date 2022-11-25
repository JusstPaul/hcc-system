const Button = ({ children, className, disabled, ...props }) => {
  const _className = (() => {
    const c =
      'rounded bg-primary-500 hover:bg-primary-600 text-gray-50 py-1.5 font-semibold text-sm focus:outline-none focus:border-primary-600 focus:border-2 disabled:opacity-75 disabled:hover:bg-primary-500'
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

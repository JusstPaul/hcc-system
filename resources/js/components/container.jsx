const Container = ({
  children,
  className,
  centered = true,
  width = 'max-w-screen-lg',
  padding = 'px-2',
  ...props
}) => {
  const _className = (() => {
    const c = (() => {
      const _c = `w-full h-full ${width} ${padding}`
      if (centered) return `${_c} mx-auto`
      return _c
    })()

    if (className) return `${c} ${className}`
    return c
  })()

  return <div className={_className} {...props}>{children}</div>
}
export default Container

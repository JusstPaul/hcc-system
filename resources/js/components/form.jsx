const Form = ({ children, onSubmit, className, legend, ...props }) => {
  const _legend = (() => {
    if (legend) return legend
    return 'Form'
  })()

  const _className = (() => {
    const c = 'w-full flex flex-col'
    if (className) return `${c} ${className}`
    return c
  })()

  return (
    <form
      onSubmit={(e) => {
        e.preventDefault()
        if (onSubmit) onSubmit(e)
      }}
      className={_className}
      {...props}
    >
      <fieldset className="flex flex-col gap-2">
        <legend className="sr-only">{_legend}</legend>
        {children}
      </fieldset>
    </form>
  )
}
export default Form
export { TextInput, Label }

const Label = ({ children, className, ...props }) => {
  const _className = (() => {
    const c = 'text-sm font-medium'
    if (className) return `${c} ${className}`
    return c
  })()
  return (
    <label className={_className} {...props}>
      {children}
    </label>
  )
}

const TextInput = ({ name, label, className, ...props }) => {
  const _className = (() => {
    const c = 'flex flex-col'
    if (className) return `${c} ${className}`
    return c
  })()

  const _label = () => {
    if (label) return <Label htmlFor={name}>{label}</Label>
    return <></>
  }

  return (
    <p className={_className}>
      {_label()}
      <input
        name={name}
        className="rounded focus:ring-primary-500 focus:border-primary-500"
        {...props}
      />
    </p>
  )
}

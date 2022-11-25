import { useState } from 'react'
import { isUndefined } from 'lodash'
import { EyeIcon, EyeSlashIcon } from '@heroicons/react/20/solid'

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
export { TextInput, PasswordInput, Label }

const Label = ({ children, className, ...props }) => {
  const _className = (() => {
    const c = 'text-xs font-semibold'
    if (className) return `${c} ${className}`
    return c
  })()
  return (
    <label className={_className} {...props}>
      {children}
    </label>
  )
}

const Error = ({ children, className, ...props }) => {
  const _className = (() => {
    const c = 'text-xs text-error-600'
    if (className) return `${c} ${className}`
    return c
  })()
  return (
    <span className={_className} {...props}>
      {children}
    </span>
  )
}

const TextInput = ({ name, label, className, error, ...props }) => {
  const _className = (() => {
    const c = 'flex flex-col'
    if (className) return `${c} ${className}`
    return c
  })()

  const _label = () => {
    if (label) return <Label htmlFor={name}>{label}</Label>
    return <></>
  }

  const _error = () => {
    if (error) return <Error id={`${name}-error`}>{error}</Error>
    return <></>
  }

  return (
    <p className={_className}>
      {_label()}
      <input
        name={name}
        className="rounded py-1 focus:ring-primary-600 focus:border-primary-600"
        aria-errormessage={`${name}-error`}
        aria-invalid={!isUndefined(error)}
        {...props}
      />
      {_error()}
    </p>
  )
}

const PasswordInput = ({
  name,
  label,
  className,
  onFocus,
  onBlur,
  ...props
}) => {
  const [show, setShow] = useState(false)
  const [focused, setFocused] = useState(false)

  const _className = (() => {
    const c = 'flex flex-col'
    if (className) return `${c} ${className}`
    return c
  })()

  const _label = () => {
    if (label) return <Label htmlFor={name}>{label}</Label>
  }

  const detectInputFocus = () => {
    if (focused) {
      return 'border-primary-600'
    }
    return 'border-gray-600'
  }

  return (
    <p className={_className}>
      {_label()}
      <span className="relative">
        <input
          name={name}
          className="rounded py-1 focus:ring-primary-600 focus:border-primary-600 block w-full"
          type={show ? 'text' : 'password'}
          onFocus={(e) => {
            setFocused(true)
            if (onFocus) onFocus(e)
          }}
          onBlur={(e) => {
            setFocused(false)
            if (onBlur) onBlur(e)
          }}
          {...props}
        />
        <button
          type="button"
          className={`absolute inset-y-0 right-0 px-3 flex items-center leading-5 border-t border-r border-b ${detectInputFocus()} rounded-r text-gray-700 bg-gray-200 focus:outline-none focus:bg-gray-300`}
          onClick={() => setShow(!show)}
          tabIndex={0}
          role="switch"
          aria-controls={name}
          aria-expanded={show}
        >
          <span className="sr-only">Toggle Password Visibility</span>
          {show ? (
            <EyeSlashIcon className="h-5" />
          ) : (
            <EyeIcon className="h-5" />
          )}
        </button>
      </span>
    </p>
  )
}

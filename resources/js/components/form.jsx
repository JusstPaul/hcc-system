import { useState } from 'react'
import { isUndefined, isNull } from 'lodash'
import { EyeIcon, EyeSlashIcon } from '@heroicons/react/20/solid'

const Form = ({ children, onSubmit, className, ...props }) => {
  const _className = (() => {
    const c = 'w-full flex flex-col gap-4'
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
      {children}
    </form>
  )
}

const Fieldset = ({
  className,
  children,
  legend,
  isLegendVisible = false,
  ...props
}) => {
  const _className = (() => {
    const c = 'flex flex-col gap-2'
    if (className) return `${className} ${c}`
    return c
  })()

  const _legendClassName = (() => {
    if (!isLegendVisible) return 'sr-only'
    return 'font-semibold'
  })()

  return (
    <fieldset className={_className} {...props}>
      {legend ? <legend className={_legendClassName}>{legend}</legend> : <></>}
      {children}
    </fieldset>
  )
}

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

const TextInput = ({ name, label, className, error, id, ...props }) => {
  const _className = (() => {
    const c = 'flex flex-col mb-auto'
    if (className) return `${c} ${className}`
    return c
  })()

  const _label = () => {
    if (label) return <Label htmlFor={id}>{label}</Label>
    return <></>
  }

  const _name = (() => {
    if (name) return name
    if (id) return id
    return ''
  })()

  const _error = () => {
    if (error) return <Error id={`${name}-error`}>{error}</Error>
    return <></>
  }

  return (
    <p className={_className}>
      {_label()}
      <input
        name={name}
        id={id}
        className="rounded py-1 focus:ring-primary-600 focus:border-primary-600"
        aria-errormessage={`${_name}-error`}
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
  id,
  className,
  onFocus,
  onBlur,
  ...props
}) => {
  const [show, setShow] = useState(false)
  const [focused, setFocused] = useState(false)

  const _className = (() => {
    const c = 'flex flex-col mb-auto'
    if (className) return `${c} ${className}`
    return c
  })()

  const _label = () => {
    if (label) return <Label htmlFor={id}>{label}</Label>
    return <></>
  }

  const detectInputFocus = () => {
    if (focused) {
      return 'border-primary-600'
    }
    return 'border-gray-500'
  }

  return (
    <p className={_className}>
      {_label()}
      <span className="relative">
        <input
          name={name}
          id={id}
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

const Checkbox = ({ name, label, className, id, ...props }) => {
  const _className = (() => {
    const c = 'flex gap-2 mb-auto'
    if (className) return `${c} ${className}`
    return c
  })()

  const _label = () => {
    if (label) return <Label htmlFor={id}>{label}</Label>
    return <></>
  }

  return (
    <p className={_className}>
      <input
        type="checkbox"
        name={name}
        id={id}
        {...props}
        className="rounded py-1 text-primary-500 focus:ring-primary-600 focus:border-primary-600"
      />
      {_label()}
    </p>
  )
}

const Select = ({
  id,
  label,
  className,
  placeholder,
  value,
  options,
  error,
  ...props
}) => {
  const _className = (() => {
    const c = 'flex flex-col mb-auto'
    if (className) return `${c} ${className}`
    return c
  })()

  const _label = () => {
    if (label && id) return <Label htmlFor={id}>{label}</Label>
  }

  const _placeholder = () => {
    if (placeholder) {
      return (
        <option value="" disabled hidden>
          {placeholder}
        </option>
      )
    }
    return <></>
  }

  const placeholderColor = () => {
    if (isNull(value) || isUndefined(value) || value === '') {
      return 'text-gray-600'
    }
    return ''
  }

  const _options = () => {
    if (options) {
      return options.map(({ label, value }, i) => (
        <option key={i} value={value}>
          {label}
        </option>
      ))
    }
    return <></>
  }

  const _error = () => {
    if (error) return <Error id={`${id}-error`}>{error}</Error>
    return <></>
  }

  return (
    <p className={_className}>
      {_label()}
      <select
        id={id}
        className={`rounded py-1 focus:ring-primary-600 focus:border-primary-600 ${placeholderColor()}`}
        value={value}
        aria-errormessage={`${id}-error`}
        {...props}
      >
        {_placeholder()}
        {_options()}
      </select>
      {_error()}
    </p>
  )
}

export default Form
export { Fieldset, TextInput, PasswordInput, Label, Checkbox, Select }

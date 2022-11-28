import { useEffect, useState, Fragment } from 'react'
import ReactDOM from 'react-dom'

const Header = ({ children }) => {
  const [isReady, setReady] = useState(false)
  useEffect(() => {
    setReady(true)
  }, [])

  if (isReady) {
    return ReactDOM.createPortal(
      <Fragment>{children}</Fragment>,
      document.getElementById('header'),
    )
  }
  return <></>
}
export default Header

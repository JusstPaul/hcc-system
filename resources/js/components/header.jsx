import { useEffect, useState } from 'react'
import ReactDOM from 'react-dom'

const Header = ({ children }) => {
  const [isReady, setReady] = useState(false)
  useEffect(() => {
    setReady(true)
  }, [])

  return isReady ? (
    ReactDOM.createPortal(children, document.getElementById('header'))
  ) : (
    <></>
  )
}
export default Header

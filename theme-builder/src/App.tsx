import { useState, useEffect } from 'react'
import { Toaster } from 'sonner'
import { WorkspaceProvider } from './contexts/WorkspaceContext'
import { WorkspaceView } from './components/workspace/WorkspaceView'
import { LoginForm } from './components/auth/LoginForm'
import './App.css'

function App() {
  const [isAuthenticated, setIsAuthenticated] = useState<boolean | null>(null)

  useEffect(() => {
    // Check if user has a token
    const token = localStorage.getItem('jwt_token')
    setIsAuthenticated(!!token)
  }, [])

  // Show nothing while checking authentication
  if (isAuthenticated === null) {
    return null
  }

  // Show login form if not authenticated
  if (!isAuthenticated) {
    return <LoginForm />
  }

  // Show workspace if authenticated
  return (
    <WorkspaceProvider>
      <WorkspaceView />
      <Toaster position="bottom-right" richColors />
    </WorkspaceProvider>
  )
}

export default App

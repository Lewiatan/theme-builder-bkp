import { useState } from 'react';
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';

export function LoginForm() {
  const [token, setToken] = useState('eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJpYXQiOjE3NjE4MjA1MTIsImV4cCI6MTc2MTgyNDExMiwicm9sZXMiOlsiUk9MRV9VU0VSIl0sInVzZXJJZCI6ImRlbW9AZXhhbXBsZS5jb20iLCJlbWFpbCI6ImRlbW9AZXhhbXBsZS5jb20ifQ.RpYVyThLARvwkj1MciuFtvCcgDAlDfxCXuHGcx5nQtdJXW-s7BeAEm--yui6F5TheaWzfEAfcOfMXtYUQ48CIyW3nlgxYcQHOnbSJzOm33n04EMFx_u3gkTJwmW_BD6OwZv_rRSnc1nPz028ANaISJOxY-pcyebQQ50sdGp4SAaTxSiOIObWyHf_PjwIEo1F4S9y6hkNfG86ItJko5Z3cMM46v5xwQ5exaY4KRjXH8IKCjFvsEwV3NaW9_v_9jnKSU7AiCorFQz-RXY6tzy2rK1BWbGxDlGW6N9IrfuyafifsQXlE2Bv3m8gKcnMLYnWSAftJAEQCAfvm5Oawm7FhA');

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();

    if (token.trim()) {
      localStorage.setItem('jwt_token', token.trim());
      window.location.href = '/';
    }
  };

  return (
    <div className="flex min-h-screen items-center justify-center bg-gray-100">
      <div className="w-full max-w-md rounded-lg bg-white p-8 shadow-lg">
        <h1 className="mb-6 text-center text-2xl font-bold text-gray-900">
          Theme Builder Login
        </h1>
        <p className="mb-6 text-center text-sm text-gray-600">
          Enter your JWT token to access the workspace
        </p>

        <form onSubmit={handleSubmit} className="space-y-4">
          <div>
            <Label htmlFor="token">JWT Token</Label>
            <textarea
              id="token"
              value={token}
              onChange={(e) => setToken(e.target.value)}
              placeholder="Paste your JWT token here..."
              className="mt-1 w-full rounded-md border border-gray-300 p-3 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
              rows={4}
              required
            />
          </div>

          <Button type="submit" className="w-full" disabled={!token.trim()}>
            Login
          </Button>
        </form>

        <div className="mt-6 rounded-md bg-blue-50 p-4">
          <p className="text-xs text-blue-900">
            <strong>For testing:</strong> Get a valid JWT token from the backend API and paste it above.
          </p>
        </div>
      </div>
    </div>
  );
}

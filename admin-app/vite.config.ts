import { defineConfig } from 'vite'
import react from '@vitejs/plugin-react'
import path from 'path'

// https://vitejs.dev/config/
export default defineConfig({
  plugins: [react()],
  base: '', // Use relative paths for assets
  resolve: {
    alias: {
      "@": path.resolve(__dirname, "./src"),
    },
  },
  build: {
    outDir: '../build',
    emptyOutDir: true,
    manifest: true,
    rollupOptions: {
      input: './src/main.tsx',
    },
  },
  server: {
    host: 'localhost',
    port: 3001,
    strictPort: true,
    origin: 'http://localhost:3001',
    cors: true,
  },
})

import react from "@vitejs/plugin-react";
import path from "path";
import { defineConfig } from "vite";

// https://vitejs.dev/config/
export default defineConfig({
  plugins: [react()],
  base: "", // Use relative paths for assets
  resolve: {
    alias: {
      "@": path.resolve(__dirname, "./src"),
      "/src": path.resolve(__dirname, "./src"),
    },
  },
  build: {
    outDir: "../build",
    emptyOutDir: true,
    manifest: true,
    rollupOptions: {
      input: "./src/main.tsx",
      output: {
        manualChunks: {
          vendor: ["react", "react-dom", "react-router-dom"],
          ui: ["lucide-react", "sonner", "clsx", "tailwind-merge"],
          charts: ["recharts"],
          query: ["@tanstack/react-query"],
        },
      },
    },
  },
  server: {
    host: "localhost",
    port: 3001,
    strictPort: true,
    origin: "http://localhost:3001",
    cors: true,
  },
});

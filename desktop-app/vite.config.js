import { defineConfig } from 'vite'
import react from '@vitejs/plugin-react'
import electron from 'vite-plugin-electron'
import renderer from 'vite-plugin-electron-renderer'
import path from 'path'
import { fileURLToPath } from 'url'
import fs from 'fs'

const __dirname = path.dirname(fileURLToPath(import.meta.url))

const copyPreloadCJS = {
  name: 'copy-preload-cjs',
  closeBundle() {
    const outDir = path.join(__dirname, 'dist-electron/preload')
    if (!fs.existsSync(outDir)) fs.mkdirSync(outDir, { recursive: true })
    fs.copyFileSync(
      path.join(__dirname, 'src/main/preload.js'),
      path.join(outDir, 'preload.cjs')
    )
  }
}

export default defineConfig({
  root: path.join(__dirname, 'src/renderer'),
  build: {
    outDir: path.join(__dirname, 'dist'),
    emptyOutDir: true,
  },
  plugins: [
    react(),
    electron([
      {
        entry: path.join(__dirname, 'src/main/index.js'),
        vite: {
          build: {
            outDir: path.join(__dirname, 'dist-electron/main'),
            rollupOptions: {
              external: ['electron', 'better-sqlite3', 'sharp', 'path', 'fs', 'os', 'url', 'crypto', 'http', 'https']
            }
          }
        }
      }
    ]),
    renderer(),
    copyPreloadCJS,
  ]
})

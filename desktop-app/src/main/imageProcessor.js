import sharp from 'sharp'
import path from 'path'

export async function processImage(inputPath, outputPath, options = {}) {
  const { watermark = false, size = 1200 } = options

  const image = sharp(inputPath)
  const meta = await image.metadata()
  const { width, height } = meta

  // Center-crop to square
  const squareSize = Math.min(width, height)
  const left = Math.floor((width - squareSize) / 2)
  const top = Math.floor((height - squareSize) / 2)

  let pipeline = image
    .extract({ left, top, width: squareSize, height: squareSize })
    .resize(size, size, { fit: 'fill' })

  if (watermark) {
    // SVG watermark text bottom-right
    const svgText = `
      <svg width="${size}" height="${size}">
        <text
          x="${size - 16}"
          y="${size - 16}"
          text-anchor="end"
          font-family="Arial, sans-serif"
          font-size="22"
          fill="rgba(255,255,255,0.55)"
          font-weight="bold"
        >plantaphilia.eu</text>
      </svg>
    `
    const svgBuffer = Buffer.from(svgText)
    pipeline = pipeline.composite([{ input: svgBuffer, top: 0, left: 0 }])
  }

  await pipeline.jpeg({ quality: 88 }).toFile(outputPath)
  return outputPath
}

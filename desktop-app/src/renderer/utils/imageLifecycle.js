export function extOf(filename) {
  const m = /\.[a-zA-Z0-9]+$/.exec(filename || '')
  return m ? m[0] : ''
}

// Copies a set of already-finalized DB image rows into a temp staging
// subfolder for an editing session — originals stay untouched until the
// session is actually saved, so cancelling never loses/mutates anything.
// destSubfolder must be unique per concurrent editing session (the base
// product uses 'temp', each variant dialog uses its own 'temp/variant-N')
// so staging one set never clobbers another's identically-named "Bild 1".
export async function copyImagesToTemp(dbImages, destSubfolder = 'temp') {
  if (!dbImages || dbImages.length === 0) return []
  const staged = []
  let n = 0
  for (const img of dbImages) {
    n++
    const tempName = `Bild ${n}${extOf(img.filename || img.local_path)}`
    const destPath = await window.api.copyToFolder(img.local_path, destSubfolder, tempName)
    // Stage the existing AVIF counterpart too (if any) — finalizeImages
    // deletes-then-recreates every original filename unconditionally, so
    // without this the AVIF for a kept-but-not-recropped image would be
    // deleted before it could be moved to its fresh final name.
    let avifTempPath = null
    if (img.avif_local_path) {
      try {
        avifTempPath = await window.api.copyToFolder(img.avif_local_path, `${destSubfolder}/avif`, `Bild ${n}.avif`)
      } catch (e) { /* missing/stale avif file — will simply be regenerated on next push */ }
    }
    staged.push({ ...img, local_path: destPath, avif_local_path: avifTempPath, isTemp: true })
  }
  return staged
}

// Moves a staged (temp) image set into its permanent folder, moving its AVIF
// counterpart along too. Returns the finalized image objects (permanent
// local_path/avif_local_path) — does NOT persist to any DB table itself;
// callers decide how (saveImage per image for the base product; attached to
// a variant's `images` field for saveVariants to persist in one call).
export async function finalizeImages(images, folderPath, filePrefix) {
  const finalized = []
  for (let i = 0; i < images.length; i++) {
    const img = images[i]
    const ext = extOf(img.filename || img.local_path)
    const filename = i === 0 ? `${filePrefix}_Vorschaubild${ext}` : `${filePrefix}_Bild${i}${ext}`
    const destPath = await window.api.moveToFolder(img.local_path, folderPath, filename)

    // Best-effort — a missing/failed AVIF shouldn't block saving the
    // product itself, it just means push regenerates it next time.
    let avifDestPath = null
    if (img.avif_local_path) {
      try {
        const avifFilename = filename.replace(/\.[^.]+$/, '.avif')
        avifDestPath = await window.api.moveToFolder(img.avif_local_path, `${folderPath}/avif`, avifFilename)
      } catch (e) { /* ignore — avif is a derived nicety, not the source of truth */ }
    }

    finalized.push({
      ...img,
      local_path: destPath,
      avif_local_path: avifDestPath,
      filename,
      sort_order: i,
    })
  }
  return finalized
}

import { useState, useEffect, useContext, useCallback } from 'react'
import { ToastContext } from '../App.jsx'

export function useSync() {
  const [pulling, setPulling] = useState(false)
  const [preparing, setPreparing] = useState(false)
  const [pushing, setPushing] = useState(false)
  const [lastSynced, setLastSynced] = useState(null)
  const [progress, setProgress] = useState('')
  const [pullConflicts, setPullConflicts] = useState([])
  const [pushPreview, setPushPreview] = useState(null)
  const addToast = useContext(ToastContext)

  useEffect(() => {
    const off = window.api.onProgress((msg) => setProgress(msg))
    return off
  }, [])

  const pull = useCallback(async () => {
    setPulling(true)
    setProgress('')
    try {
      const result = await window.api.pull()
      setLastSynced(new Date())
      if (result.conflicts && result.conflicts.length > 0) {
        setPullConflicts(result.conflicts)
        addToast(`Pull: ${result.count} Produkte, ${result.conflicts.length} Konflikte gefunden`, 'info')
      } else {
        addToast(`Pull: ${result.count} Produkte synchronisiert`, 'success')
      }
      // Trigger pull-complete event for migration check
      window.dispatchEvent(new CustomEvent('pull-complete'))
      return result
    } catch (e) {
      addToast(`Pull fehlgeschlagen: ${e.message}`, 'error')
    } finally {
      setPulling(false)
      setProgress('')
    }
  }, [addToast])

  const applyPullResolutions = useCallback(async (resolutions) => {
    await window.api.applyPullResolutions(resolutions)
    setPullConflicts([])
    addToast('Pull-Konflikte lokal übernommen', 'success')
  }, [addToast])

  const preparePush = useCallback(async () => {
    setPreparing(true)
    setProgress('Analysiere lokale Änderungen...')
    try {
      const data = await window.api.preparePush()
      const total = data.ready.length + data.conflicts.length + data.newProducts.length
      if (total === 0) {
        addToast('Keine lokalen Änderungen — nichts zu pushen', 'info')
        return
      }
      setPushPreview(data)
    } catch (e) {
      addToast(`Fehler: ${e.message}`, 'error')
    } finally {
      setPreparing(false)
      setProgress('')
    }
  }, [addToast])

  const push = useCallback(async ({ confirmedIds, resolutions }) => {
    setPushPreview(null)
    setPushing(true)
    setProgress('')
    try {
      const result = await window.api.push({ confirmedIds, resolutions })
      setLastSynced(new Date())
      addToast(`Push: ${result.pushed} Produkte hochgeladen${result.errors ? `, ${result.errors} Fehler` : ''}`, result.errors ? 'error' : 'success')
      return result
    } catch (e) {
      addToast(`Push fehlgeschlagen: ${e.message}`, 'error')
    } finally {
      setPushing(false)
      setProgress('')
    }
  }, [addToast])

  return {
    pulling, preparing, pushing,
    lastSynced, progress,
    pullConflicts, setPullConflicts,
    pushPreview, setPushPreview,
    pull, preparePush, push, applyPullResolutions,
  }
}

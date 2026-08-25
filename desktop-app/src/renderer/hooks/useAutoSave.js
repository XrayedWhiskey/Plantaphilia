import { useEffect, useRef } from 'react'

export function useAutoSave(data, saveFn, intervalMs = 60000) {
  const dataRef = useRef(data)
  const saveFnRef = useRef(saveFn)

  useEffect(() => { dataRef.current = data }, [data])
  useEffect(() => { saveFnRef.current = saveFn }, [saveFn])

  useEffect(() => {
    if (!intervalMs) return
    const id = setInterval(() => {
      if (dataRef.current && saveFnRef.current) {
        saveFnRef.current(dataRef.current)
      }
    }, intervalMs)
    return () => clearInterval(id)
  }, [intervalMs])
}

// Precedence rule shared between the client-side apply-on-select (ProductForm)
// and the server-side cascade-on-blueprint-save (database.js): a field may be
// written by `sourceType` ('gattung' | 'art') unless it has been manually
// overridden by the user, or is already owned by the higher-precedence 'art'
// source while sourceType is 'gattung'.
export function canApplyField(currentOwner, sourceType) {
  if (currentOwner === 'manual') return false
  if (currentOwner === 'art' && sourceType === 'gattung') return false
  return true
}

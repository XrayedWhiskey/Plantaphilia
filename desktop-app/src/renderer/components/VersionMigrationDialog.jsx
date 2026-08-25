import React, { useState } from 'react'

export default function VersionMigrationDialog({ migrationData, onMigrate, onIgnore, onClose }) {
  const [selectedProducts, setSelectedProducts] = useState(new Set())
  const [migrating, setMigrating] = useState(false)

  if (!migrationData || !migrationData.hasMigrations) {
    return null
  }

  const { currentVersion, changes, totalProducts } = migrationData

  // Alle Produkte aus allen Changes sammeln
  const allProductEntries = []
  for (const [changeKey, products] of Object.entries(changes)) {
    for (const product of products) {
      allProductEntries.push({ ...product, changeKey })
    }
  }

  // Toggle selection for all products of a specific change
  const toggleChange = (changeKey) => {
    const productsInChange = changes[changeKey]
    const allSelected = productsInChange.every(p => selectedProducts.has(p.productId))
    
    const newSelected = new Set(selectedProducts)
    if (allSelected) {
      // Deselect all
      productsInChange.forEach(p => newSelected.delete(p.productId))
    } else {
      // Select all
      productsInChange.forEach(p => newSelected.add(p.productId))
    }
    setSelectedProducts(newSelected)
  }

  // Toggle selection for single product
  const toggleProduct = (productId) => {
    const newSelected = new Set(selectedProducts)
    if (newSelected.has(productId)) {
      newSelected.delete(productId)
    } else {
      newSelected.add(productId)
    }
    setSelectedProducts(newSelected)
  }

  // Select all products
  const selectAll = () => {
    const allIds = allProductEntries.map(p => p.productId)
    setSelectedProducts(new Set(allIds))
  }

  // Deselect all
  const deselectAll = () => {
    setSelectedProducts(new Set())
  }

  const handleMigrate = async () => {
    if (selectedProducts.size === 0) return
    
    setMigrating(true)
    try {
      const productIds = Array.from(selectedProducts)
      await onMigrate(productIds)
    } finally {
      setMigrating(false)
    }
  }

  const handleIgnore = () => {
    onIgnore()
  }

  // Format change key for display
  const formatChangeKey = (key) => {
    const changeNames = {
      'care_temp_min': 'Temperaturbereiche (Pflege)',
      'care_temp_max': 'Temperaturbereiche (Pflege)',
      'shipping_dimensions': 'Versandmaße',
      'initial_version': 'Initiale Version',
      'base_schema': 'Basis-Schema'
    }
    return changeNames[key] || key
  }

  return (
    <div style={{
      position: 'fixed', inset: 0, background: 'rgba(0,0,0,0.7)', zIndex: 600,
      display: 'flex', alignItems: 'center', justifyContent: 'center'
    }}>
      <div style={{
        background: '#2F3B35', border: '1px solid rgba(155,111,208,0.3)',
        borderRadius: 8, width: 680, maxHeight: '85vh', display: 'flex', flexDirection: 'column',
        boxShadow: '0 24px 80px rgba(0,0,0,0.7)'
      }}>
        {/* Header */}
        <div style={{ padding: '18px 24px', borderBottom: '1px solid rgba(155,111,208,0.15)' }}>
          <div style={{ fontSize: 11, color: '#9B6FD0', fontWeight: 700, letterSpacing: 1.5, textTransform: 'uppercase', marginBottom: 6 }}>
            Produkt-Migration erforderlich
          </div>
          <h3 style={{ fontSize: 16, color: '#EAE4D6', fontWeight: 600, margin: 0 }}>
            App-Version {currentVersion} — Änderungen an {Object.keys(changes).length} System{Object.keys(changes).length !== 1 ? 'en' : ''}
          </h3>
          <div style={{ fontSize: 12, color: '#9CA59E', marginTop: 4 }}>
            {totalProducts} Produkt{totalProducts !== 1 ? 'e' : ''} müssen aktualisiert werden
          </div>
        </div>

        {/* Body */}
        <div style={{ flex: 1, overflowY: 'auto', padding: '0 24px 24px' }}>
          <div style={{ padding: '16px 0', borderBottom: '1px solid rgba(155,111,208,0.1)' }}>
            <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: 12 }}>
              <span style={{ fontSize: 12, color: '#C8C0AF', fontWeight: 600 }}>
                Zu migrierende Produkte
              </span>
              <div style={{ display: 'flex', gap: 8 }}>
                <button 
                  onClick={selectAll}
                  style={{ fontSize: 11, padding: '4px 10px', background: 'rgba(155,111,208,0.1)', border: '1px solid rgba(155,111,208,0.3)', borderRadius: 4, color: '#B8A8D8', cursor: 'pointer' }}
                >
                  Alle auswählen
                </button>
                <button 
                  onClick={deselectAll}
                  style={{ fontSize: 11, padding: '4px 10px', background: 'rgba(155,111,208,0.1)', border: '1px solid rgba(155,111,208,0.3)', borderRadius: 4, color: '#B8A8D8', cursor: 'pointer' }}
                >
                  Auswahl aufheben
                </button>
              </div>
            </div>
          </div>

          {/* Changes grouped by system */}
          {Object.entries(changes).map(([changeKey, products]) => {
            const allInChangeSelected = products.every(p => selectedProducts.has(p.productId))
            const someInChangeSelected = products.some(p => selectedProducts.has(p.productId))
            
            return (
              <div key={changeKey} style={{ marginBottom: 20 }}>
                {/* Change header */}
                <div 
                  onClick={() => toggleChange(changeKey)}
                  style={{
                    padding: '10px 14px', background: 'rgba(155,111,208,0.08)',
                    border: '1px solid rgba(155,111,208,0.2)', borderRadius: 6,
                    cursor: 'pointer', display: 'flex', justifyContent: 'space-between', alignItems: 'center',
                    marginBottom: 8
                  }}
                >
                  <div style={{ display: 'flex', alignItems: 'center', gap: 10 }}>
                    <input
                      type="checkbox"
                      checked={allInChangeSelected}
                      ref={el => { if (el) el.indeterminate = someInChangeSelected && !allInChangeSelected }}
                      onChange={(e) => { e.stopPropagation(); toggleChange(changeKey) }}
                      style={{ cursor: 'pointer' }}
                    />
                    <div>
                      <div style={{ fontSize: 13, color: '#EAE4D6', fontWeight: 600 }}>
                        {formatChangeKey(changeKey)}
                      </div>
                      <div style={{ fontSize: 11, color: '#9CA59E' }}>
                        {products.length} Produkt{products.length !== 1 ? 'e' : ''}
                      </div>
                    </div>
                  </div>
                  <div style={{ fontSize: 11, color: '#9CA59E' }}>
                    {allInChangeSelected ? '✓' : someInChangeSelected ? '○' : '○'}
                  </div>
                </div>

                {/* Products in this change */}
                <div style={{ paddingLeft: 36 }}>
                  {products.map((product) => {
                    const isSelected = selectedProducts.has(product.productId)
                    return (
                      <div
                        key={product.productId}
                        onClick={() => toggleProduct(product.productId)}
                        style={{
                          padding: '8px 12px', marginBottom: 4,
                          background: isSelected ? 'rgba(76, 175, 125, 0.1)' : 'transparent',
                          border: isSelected ? '1px solid rgba(76, 175, 125, 0.3)' : '1px solid transparent',
                          borderRadius: 4, cursor: 'pointer', display: 'flex', justifyContent: 'space-between', alignItems: 'center'
                        }}
                      >
                        <div style={{ display: 'flex', alignItems: 'center', gap: 8 }}>
                          <input
                            type="checkbox"
                            checked={isSelected}
                            onChange={(e) => { e.stopPropagation(); toggleProduct(product.productId) }}
                            style={{ cursor: 'pointer' }}
                          />
                          <span style={{ fontSize: 12, color: isSelected ? '#EAE4D6' : '#C8C0AF' }}>
                            {product.productName}
                          </span>
                        </div>
                        <div style={{ fontSize: 11, color: '#9CA59E' }}>
                          {product.fromVersion} → {product.toVersion}
                          {!product.canMigrate && (
                            <span style={{ color: '#E57373', marginLeft: 8 }}>⚠ Nicht kompatibel</span>
                          )}
                        </div>
                      </div>
                    )
                  })}
                </div>
              </div>
            )
          })}
        </div>

        {/* Footer */}
        <div style={{ padding: '16px 24px', borderTop: '1px solid rgba(155,111,208,0.15)', display: 'flex', gap: 10, justifyContent: 'space-between', alignItems: 'center' }}>
          <div style={{ fontSize: 11, color: '#9CA59E' }}>
            {selectedProducts.size} von {totalProducts} ausgewählt
          </div>
          <div style={{ display: 'flex', gap: 10 }}>
            <button 
              className="btn-secondary" 
              onClick={handleIgnore}
              style={{ fontSize: 12 }}
            >
              Ignorieren
            </button>
            <button 
              className="btn-secondary" 
              onClick={onClose}
              style={{ fontSize: 12 }}
            >
              Später
            </button>
            <button 
              className="btn-primary" 
              onClick={handleMigrate}
              disabled={selectedProducts.size === 0 || migrating}
              style={{ fontSize: 12 }}
            >
              {migrating ? 'Migriere...' : `${selectedProducts.size} Produkt${selectedProducts.size !== 1 ? 'e' : ''} migrieren`}
            </button>
          </div>
        </div>
      </div>
    </div>
  )
}

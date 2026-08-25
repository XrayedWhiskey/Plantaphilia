const { contextBridge, ipcRenderer } = require('electron')

contextBridge.exposeInMainWorld('api', {
  // Settings
  getSettings: () => ipcRenderer.invoke('db:getSettings'),
  setSetting: (key, value) => ipcRenderer.invoke('db:setSetting', key, value),

  // Products
  getProducts: (search) => ipcRenderer.invoke('db:getProducts', search),
  getProduct: (id) => ipcRenderer.invoke('db:getProduct', id),
  saveProduct: (product) => ipcRenderer.invoke('db:saveProduct', product),
  deleteProduct: (id) => ipcRenderer.invoke('db:deleteProduct', id),
  setProductImageFolder: (id, folderName) => ipcRenderer.invoke('db:setProductImageFolder', id, folderName),

  // Images
  getProductImages: (productId) => ipcRenderer.invoke('db:getProductImages', productId),
  saveImage: (image) => ipcRenderer.invoke('db:saveImage', image),
  deleteImage: (id) => ipcRenderer.invoke('db:deleteImage', id),

  // Tags
  getTagCategories: () => ipcRenderer.invoke('db:getTagCategories'),
  saveTagCategory: (cat) => ipcRenderer.invoke('db:saveTagCategory', cat),
  deleteTagCategory: (id) => ipcRenderer.invoke('db:deleteTagCategory', id),
  getTags: () => ipcRenderer.invoke('db:getTags'),
  saveTag: (tag) => ipcRenderer.invoke('db:saveTag', tag),
  deleteTag: (id) => ipcRenderer.invoke('db:deleteTag', id),
  getProductTags: (productId) => ipcRenderer.invoke('db:getProductTags', productId),
  setProductTags: (productId, tagIds) => ipcRenderer.invoke('db:setProductTags', productId, tagIds),
  getVariantTags: (variantId) => ipcRenderer.invoke('db:getVariantTags', variantId),
  setVariantTags: (variantId, tagIds) => ipcRenderer.invoke('db:setVariantTags', variantId, tagIds),
  getProductIdsByBlueprint: (type, id) => ipcRenderer.invoke('db:getProductIdsByBlueprint', type, id),

  // Kategorien
  getCategories: () => ipcRenderer.invoke('db:getCategories'),
  saveCategory: (cat) => ipcRenderer.invoke('db:saveCategory', cat),
  deleteCategory: (id) => ipcRenderer.invoke('db:deleteCategory', id),
  moveCategory: (id, direction) => ipcRenderer.invoke('db:moveCategory', id, direction),
  moveCarouselCategory: (id, direction) => ipcRenderer.invoke('db:moveCarouselCategory', id, direction),
  getProductCategories: (productId) => ipcRenderer.invoke('db:getProductCategories', productId),
  getCategoryProductsRecursive: (categoryId) => ipcRenderer.invoke('db:getCategoryProductsRecursive', categoryId),
  setProductCategories: (productId, categoryIds) => ipcRenderer.invoke('db:setProductCategories', productId, categoryIds),
  getCategoryProducts: (categoryId) => ipcRenderer.invoke('db:getCategoryProducts', categoryId),
  addProductToCategory: (categoryId, productId) => ipcRenderer.invoke('db:addProductToCategory', categoryId, productId),
  removeProductFromCategory: (categoryId, productId) => ipcRenderer.invoke('db:removeProductFromCategory', categoryId, productId),
  pushCategories: () => ipcRenderer.invoke('api:pushCategories'),

  // Snapshots
  getSnapshot: (productId) => ipcRenderer.invoke('db:getSnapshot', productId),
  saveSnapshot: (productId, data) => ipcRenderer.invoke('db:saveSnapshot', productId, data),

  // Variants
  getVariants: (productId) => ipcRenderer.invoke('db:getVariants', productId),
  saveVariants: (productId, variants) => ipcRenderer.invoke('db:saveVariants', productId, variants),

  // Specifications
  getSpecifications: () => ipcRenderer.invoke('db:getSpecifications'),
  saveSpecification: (spec) => ipcRenderer.invoke('db:saveSpecification', spec),
  deleteSpecification: (id) => ipcRenderer.invoke('db:deleteSpecification', id),
  getProductSpecifications: (productId) => ipcRenderer.invoke('db:getProductSpecifications', productId),
  setProductSpecifications: (productId, specIds) => ipcRenderer.invoke('db:setProductSpecifications', productId, specIds),

  // Gattung / Art blueprints
  getGattungen: () => ipcRenderer.invoke('db:getGattungen'),
  getGattung: (id) => ipcRenderer.invoke('db:getGattung', id),
  saveGattung: (gattung) => ipcRenderer.invoke('db:saveGattung', gattung),
  deleteGattung: (id) => ipcRenderer.invoke('db:deleteGattung', id),
  getArten: (gattungId) => ipcRenderer.invoke('db:getArten', gattungId),
  getArt: (id) => ipcRenderer.invoke('db:getArt', id),
  saveArt: (art) => ipcRenderer.invoke('db:saveArt', art),
  deleteArt: (id) => ipcRenderer.invoke('db:deleteArt', id),
  getKultivarsForArt: (artId) => ipcRenderer.invoke('db:getKultivarsForArt', artId),

  // Tax classes / delivery times
  getTaxClasses: () => ipcRenderer.invoke('db:getTaxClasses'),
  getTaxClass: (id) => ipcRenderer.invoke('db:getTaxClass', id),
  saveTaxClass: (taxClass) => ipcRenderer.invoke('db:saveTaxClass', taxClass),
  deleteTaxClass: (id) => ipcRenderer.invoke('db:deleteTaxClass', id),
  getDeliveryTimes: () => ipcRenderer.invoke('db:getDeliveryTimes'),
  getDeliveryTime: (id) => ipcRenderer.invoke('db:getDeliveryTime', id),
  saveDeliveryTime: (dt) => ipcRenderer.invoke('db:saveDeliveryTime', dt),
  deleteDeliveryTime: (id) => ipcRenderer.invoke('db:deleteDeliveryTime', id),

  // API
  pull: () => ipcRenderer.invoke('api:pull'),
  preparePush: () => ipcRenderer.invoke('api:preparePush'),
  push: (payload) => ipcRenderer.invoke('api:push', payload),
  applyPullResolutions: (res) => ipcRenderer.invoke('api:applyPullResolutions', res),
  getConflicts: () => ipcRenderer.invoke('api:getConflicts'),
  testConnection: () => ipcRenderer.invoke('api:testConnection'),
  pushSeoSettings: (payload) => ipcRenderer.invoke('api:pushSeoSettings', payload),

  // Image processing
  saveImageBlob: (buffer, slug, filename) => ipcRenderer.invoke('image:saveBlob', buffer, slug, filename),

  // Files
  readFileAsDataUrl: (path) => ipcRenderer.invoke('fs:readFileBase64', path),
  deleteFile: (path) => ipcRenderer.invoke('fs:deleteFile', path),
  moveFile: (srcPath, destPath) => ipcRenderer.invoke('fs:moveFile', srcPath, destPath),
  moveToFolder: (srcPath, folderSlug, filename) => ipcRenderer.invoke('fs:moveToFolder', srcPath, folderSlug, filename),
  copyToFolder: (srcPath, folderSlug, filename) => ipcRenderer.invoke('fs:copyToFolder', srcPath, folderSlug, filename),
  renameFolder: (oldSlug, newSlug) => ipcRenderer.invoke('fs:renameFolder', oldSlug, newSlug),
  deleteFileInFolder: (folderSlug, filename) => ipcRenderer.invoke('fs:deleteFileInFolder', folderSlug, filename),
  clearTempFolder: () => ipcRenderer.invoke('fs:clearTempFolder'),

  // Dialogs
  openFolder: () => ipcRenderer.invoke('dialog:openFolder'),
  openFile: () => ipcRenderer.invoke('dialog:openFile'),

  // Version Management
  getCurrentVersion: () => ipcRenderer.invoke('version:getCurrent'),
  checkMigrations: () => ipcRenderer.invoke('version:checkMigrations'),
  migrateProducts: (productIds) => ipcRenderer.invoke('version:migrateProducts', productIds),

  // Test Mode
  startTestMode: (version) => ipcRenderer.invoke('test:start', version),
  endTestMode: () => ipcRenderer.invoke('test:end'),

  // Events
  onProgress: (cb) => {
    const handler = (_, msg) => cb(msg)
    ipcRenderer.on('api:progress', handler)
    return () => ipcRenderer.removeListener('api:progress', handler)
  }
})

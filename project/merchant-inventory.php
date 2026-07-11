<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'merchant') {
    header("Location: login.php");
    exit;
}
$pageTitle = "GLOBAL SPORTS ARENA | Merchant Inventory";
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>

<link rel="stylesheet" href="assets/css/Merchant.css">

<style>
.inventory-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
    background: var(--bg-card);
    border-radius: 12px;
    overflow: hidden;
    color: var(--text-primary);
    border: 1px solid var(--border-glass);
}
.inventory-table th, .inventory-table td {
    padding: 15px;
    text-align: left;
    border-bottom: 1px solid var(--border-glass);
}
.inventory-table th {
    background: rgba(197, 168, 92, 0.15);
    color: var(--accent-gold);
}
.inventory-table img {
    max-width: 50px;
    border-radius: 8px;
}
.action-btn {
    padding: 8px 12px;
    border-radius: 6px;
    border: none;
    cursor: pointer;
    font-size: 0.9rem;
}
.btn-edit { background: transparent; border: 1px solid #c5a85c; color: #c5a85c; margin-right: 5px; transition: all 0.3s; }
.btn-edit:hover { background: rgba(197, 168, 92, 0.1); }
.btn-delete { background: transparent; border: 1px solid #ef4444; color: #ef4444; transition: all 0.3s; }
.btn-delete:hover { background: rgba(239, 68, 68, 0.1); }
.btn-add { background: #c5a85c; color: #111; padding: 10px 20px; font-size: 1rem; margin-bottom: 20px; font-weight: bold; transition: all 0.3s; }
.btn-add:hover { background: #d4bc74; box-shadow: 0 0 15px rgba(197, 168, 92, 0.4); }
.modal { display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.8); }
.modal-content { background-color: #0b0c10; margin: 4vh auto; padding: 30px; border: 1px solid #c5a85c; width: 500px; border-radius: 12px; color: #f5f6fa; }
.modal-content input, .modal-content select, .modal-content textarea { width: 100%; padding: 10px; margin: 10px 0; border-radius: 6px; border: 1px solid rgba(197,168,92,0.5); background: #0b0c10; color: #f5f6fa;}

/* Light Theme Overrides */
body.light-theme .modal-content {
    background-color: #fdfdf9;
    border-color: #8c7237;
    color: #111;
}
body.light-theme .modal-content input, 
body.light-theme .modal-content select, 
body.light-theme .modal-content textarea {
    background-color: #fff;
    color: #111;
    border-color: rgba(140, 114, 55, 0.4);
}
</style>

<div class="merchant-page">
  <div class="merchant-container w-full max-w-7xl mx-auto px-4">
    <div style="display: flex; justify-content: space-between; align-items: center;">
      <div>
        <h1 class="merchant-title">📦 Manage Inventory</h1>
        <p class="merchant-subtitle">Add or edit your products.</p>
      </div>
      <a href="merchant.php" style="color: #c5a85c; text-decoration: none;">&larr; Back to Dashboard</a>
    </div>
    
    <div style="margin-top: 30px;">
        <button class="action-btn btn-add" onclick="openAddModal()">+ Add New Product</button>
        
        <div class="overflow-x-auto"><table class="inventory-table">
            <thead>
                <tr>
                    <th>Image</th>
                    <th>Name</th>
                    <th>Category</th>
                    <th>Price (₹)</th>
                    <th>Stock</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="inventoryBody">
                <tr><td colspan="6" style="text-align: center;">Loading products...</td></tr>
            </tbody>
        </table></div>
    </div>
  </div>
</div>

<!-- Add/Edit Product Modal -->
<div id="productModal" class="modal">
  <div class="modal-content" style="max-height: 90vh; overflow-y: auto;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
      <h2 id="modalTitle" style="color: #c5a85c; margin: 0; font-size: 2.2rem; font-weight: 800; font-family: 'Playfair Display', serif;">Add Product</h2>
      <span onclick="closeModal()" style="color: var(--text-secondary); font-size: 28px; font-weight: bold; cursor: pointer; line-height: 1;">&times;</span>
    </div>
    <form id="productForm" onsubmit="saveProduct(event)">
      <input type="hidden" id="productId">
      
      <label style="display:block; margin-top: 10px; color: var(--text-secondary); font-size: 0.85rem;">Product Name *</label>
      <input type="text" id="productName" placeholder="e.g. Premium Football" required>
      
      <label style="display:block; margin-top: 10px; color: var(--text-secondary); font-size: 0.85rem;">Category *</label>
      <select id="productCategory" required onchange="toggleCustomCategory()">
        <option value="Footwear">Footwear</option>
        <option value="Apparel">Apparel</option>
        <option value="Equipment">Equipment</option>
        <option value="Accessories">Accessories</option>
        <option value="Fitness">Fitness</option>
        <option value="Shoes">Shoes</option>
        <option value="Jersey">Jersey</option>
        <option value="Racket">Racket</option>
        <option value="Ball">Ball</option>
        <option value="Other">Other</option>
      </select>
      
      <div id="customCategoryGroup" style="display: none; margin-top: 10px;">
        <label style="display:block; color: var(--text-secondary); font-size: 0.85rem;">Specify Category *</label>
        <input type="text" id="customCategory" placeholder="e.g. Protective Gear">
      </div>
      
      <div style="display: flex; gap: 10px;">
        <div style="flex: 1;">
          <label style="display:block; margin-top: 10px; color: var(--text-secondary); font-size: 0.85rem;">Price (₹) *</label>
          <input type="number" id="productPrice" placeholder="e.g. 999" required>
        </div>
        <div style="flex: 1;">
          <label style="display:block; margin-top: 10px; color: var(--text-secondary); font-size: 0.85rem;">Stock Quantity *</label>
          <input type="number" id="productStock" placeholder="Auto-calculated" required readonly style="opacity: 0.7; cursor: not-allowed;">
        </div>
      </div>

      <label style="display:block; margin-top: 10px; color: var(--text-secondary); font-size: 0.85rem;">Product Variants (Colors & Sizes) *</label>
      <div id="variantsContainer"></div>
      <button type="button" onclick="addVariantField()" style="background: transparent; border: 1px dashed #c5a85c; color: #c5a85c; padding: 5px 10px; border-radius: 4px; cursor: pointer; margin-top: 5px; font-size: 0.85rem;">+ Add Color Variant</button>

      <label style="display:block; margin-top: 10px; color: var(--text-secondary); font-size: 0.85rem;">Description</label>
      <textarea id="productDesc" placeholder="Describe the product..." rows="3"></textarea>
      
      <div style="display: flex; gap: 10px; margin-top: 20px;">
          <button type="submit" class="action-btn btn-add" style="flex: 1; margin: 0;">Save Product</button>
          <button type="button" class="action-btn btn-delete" style="flex: 1; margin: 0;" onclick="closeModal()">Cancel</button>
      </div>
    </form>
  </div>
</div>

<script>
let products = [];

async function loadProducts() {
    try {
        const merchantEmail = localStorage.getItem('userEmail');
        const res = await fetch(`api/index.php/products?merchantEmail=${encodeURIComponent(merchantEmail)}`);
        const data = await res.json();
        products = data;
        renderProducts();
    } catch(e) {
        console.error("Error loading products:", e);
        document.getElementById('inventoryBody').innerHTML = '<tr><td colspan="6" style="text-align: center; color: red;">Failed to load products.</td></tr>';
    }
}

function renderProducts() {
    const tbody = document.getElementById('inventoryBody');
    if (products.length === 0) {
        tbody.innerHTML = '<tr><td colspan="6" style="text-align: center;">No products found.</td></tr>';
        return;
    }
    
    tbody.innerHTML = products.map(p => `
        <tr>
            <td><img src="${p.image_url}" alt="${p.name}" onerror="this.src='https://images.unsplash.com/photo-1517649763962-0c623066013b?w=400&h=400&fit=crop'"></td>
            <td>${p.name}</td>
            <td>${p.category}</td>
            <td>₹${p.price}</td>
            <td>${p.stock}</td>
            <td>
                <button class="action-btn btn-edit" onclick='editProduct(${JSON.stringify(p).replace(/'/g, "&#39;")})'>Edit</button>
                <button class="action-btn btn-delete" onclick='deleteProduct(${p.id})'>Delete</button>
            </td>
        </tr>
    `).join('');
}

function openAddModal() {
    document.getElementById('modalTitle').textContent = 'Add Product';
    document.getElementById('productForm').reset();
    document.getElementById('productId').value = '';
    document.getElementById('variantsContainer').innerHTML = '';
    document.getElementById('productStock').value = '0';
    document.getElementById('productStock').removeAttribute('readonly');
    document.getElementById('productStock').style.opacity = '1';
    document.getElementById('productStock').style.cursor = 'text';
    document.getElementById('customCategoryGroup').style.display = 'none';
    document.getElementById('customCategory').value = '';
    document.getElementById('customCategory').required = false;
    document.getElementById('productModal').style.display = 'block';
}

function editProduct(p) {
    document.getElementById('modalTitle').textContent = 'Edit Product';
    document.getElementById('productId').value = p.id;
    document.getElementById('productName').value = p.name;
    
    const catSelect = document.getElementById('productCategory');
    let found = false;
    for (let opt of catSelect.options) {
        if (opt.value === p.category) {
            found = true;
            break;
        }
    }
    if (found) {
        catSelect.value = p.category;
        document.getElementById('customCategoryGroup').style.display = 'none';
        document.getElementById('customCategory').value = '';
        document.getElementById('customCategory').required = false;
    } else {
        catSelect.value = 'Other';
        document.getElementById('customCategoryGroup').style.display = 'block';
        document.getElementById('customCategory').value = p.category;
        document.getElementById('customCategory').required = true;
    }
    
    document.getElementById('productPrice').value = p.price;
    document.getElementById('productStock').value = p.stock;
    const variantsContainer = document.getElementById('variantsContainer');
    variantsContainer.innerHTML = '';
    
    let legacySizes = [];
    if (p.sizes && p.sizes !== "null" && p.sizes !== "") {
        try { legacySizes = JSON.parse(p.sizes); } catch(e){}
    }
    
    if (p.colors && p.colors !== "null" && p.colors !== "") {
        try {
            const parsedColors = JSON.parse(p.colors);
            if (Array.isArray(parsedColors)) {
                parsedColors.forEach((c, index) => {
                    let sizes = c.sizes || [];
                    if (index === 0 && sizes.length === 0 && legacySizes.length > 0) {
                        sizes = legacySizes;
                    }
                    addVariantField(c.color, c.image, sizes);
                });
            } else {
                throw new Error("Not array");
            }
        } catch(e) {
            let colorsArray = p.colors.split(',');
            colorsArray.forEach((c, index) => {
                let colorName = c.trim();
                if (colorName !== '') {
                    let sizes = (index === 0) ? legacySizes : [];
                    addVariantField(colorName, '', sizes);
                }
            });
        }
    } else if (legacySizes.length > 0) {
        addVariantField('Default', '', legacySizes);
    }
    updateTotalStock();

    document.getElementById('productDesc').value = p.description || '';
    document.getElementById('productModal').style.display = 'block';
}

function closeModal() {
    document.getElementById('productModal').style.display = 'none';
}

function toggleCustomCategory() {
    const val = document.getElementById('productCategory').value;
    if (val === 'Other') {
        document.getElementById('customCategoryGroup').style.display = 'block';
        document.getElementById('customCategory').required = true;
    } else {
        document.getElementById('customCategoryGroup').style.display = 'none';
        document.getElementById('customCategory').required = false;
    }
}

let variantIdCounter = 0;
function addVariantField(color = '', imageUrl = '', sizes = []) {
    const container = document.getElementById('variantsContainer');
    const vId = 'variant_' + (variantIdCounter++);
    const div = document.createElement('div');
    div.className = 'variant-block';
    div.style.border = '1px solid rgba(197,168,92,0.3)';
    div.style.padding = '10px';
    div.style.marginBottom = '10px';
    div.style.borderRadius = '6px';
    div.style.position = 'relative';
    
    let previewHtml = '';
    if (imageUrl) {
        previewHtml = `<img src="${imageUrl}" style="width: 38px; height: 38px; object-fit: cover; border-radius: 4px;">`;
    }
    
    div.innerHTML = `
        <button type="button" onclick="this.parentElement.remove(); updateTotalStock()" style="position: absolute; top: 5px; right: 5px; background: transparent; border: none; color: #ef4444; cursor: pointer; font-size: 1.2rem;">&times;</button>
        <div style="display: flex; gap: 10px; margin-bottom: 10px; padding-right: 20px;">
            <input type="text" class="color-name" placeholder="Color Name (e.g. Red)" value="${color}" style="flex: 1; padding: 8px; border-radius: 4px; border: 1px solid rgba(197,168,92,0.5);">
            <input type="file" class="color-image-file" accept=".jpg,.jpeg,.png,.webp" style="flex: 1.5; padding: 5px; border-radius: 4px; border: 1px solid rgba(197,168,92,0.5);">
            <input type="hidden" class="color-image-url" value="${imageUrl}">
            ${previewHtml}
        </div>
        <div class="variant-sizes-container" id="sizes_${vId}"></div>
        <button type="button" onclick="addSizeToVariant('${vId}')" style="background: transparent; border: 1px dashed rgba(197,168,92,0.6); color: var(--text-secondary); padding: 3px 8px; border-radius: 4px; cursor: pointer; font-size: 0.75rem;">+ Add Size</button>
    `;
    container.appendChild(div);
    
    if (sizes && sizes.length > 0) {
        sizes.forEach(s => addSizeToVariant(vId, s.size, s.stock));
    } else {
        addSizeToVariant(vId);
    }
}

function addSizeToVariant(vId, size = '', stock = 0) {
    const container = document.getElementById('sizes_' + vId);
    const div = document.createElement('div');
    div.className = 'size-row';
    div.style.display = 'flex';
    div.style.gap = '10px';
    div.style.marginTop = '5px';
    div.innerHTML = `
        <input type="text" class="size-name" placeholder="Size (e.g. 8)" value="${size}" style="flex: 1; padding: 6px; border-radius: 4px; border: 1px solid rgba(197,168,92,0.5); font-size: 0.85rem;">
        <input type="number" class="size-stock" placeholder="Stock" value="${stock}" oninput="updateTotalStock()" style="flex: 1; padding: 6px; border-radius: 4px; border: 1px solid rgba(197,168,92,0.5); font-size: 0.85rem;" min="0">
        <button type="button" onclick="this.parentElement.remove(); updateTotalStock()" style="background: transparent; border: none; color: #ef4444; cursor: pointer; font-size: 1rem;">&times;</button>
    `;
    container.appendChild(div);
    updateTotalStock();
}

function updateTotalStock() {
    const stockInputs = document.querySelectorAll('.size-stock');
    if (stockInputs.length > 0) {
        let total = 0;
        stockInputs.forEach(input => total += parseInt(input.value || 0));
        const stockEl = document.getElementById('productStock');
        stockEl.value = total;
        stockEl.setAttribute('readonly', true);
        stockEl.style.opacity = '0.7';
        stockEl.style.cursor = 'not-allowed';
    } else {
        const stockEl = document.getElementById('productStock');
        stockEl.removeAttribute('readonly');
        stockEl.style.opacity = '1';
        stockEl.style.cursor = 'text';
    }
}

async function saveProduct(e) {
    e.preventDefault();
    
    let category = document.getElementById('productCategory').value;
    if (category === 'Other') {
        category = document.getElementById('customCategory').value.trim();
        if (!category) {
            alert("Please specify a custom category.");
            return;
        }
    }
    
    const formData = new FormData();
    formData.append('merchantEmail', localStorage.getItem('userEmail'));
    formData.append('id', document.getElementById('productId').value);
    formData.append('name', document.getElementById('productName').value);
    formData.append('category', category);
    formData.append('price', document.getElementById('productPrice').value);
    formData.append('stock', document.getElementById('productStock').value);
    // Gather variants
    const variantBlocks = document.querySelectorAll('.variant-block');
    variantBlocks.forEach(block => {
        const colorName = block.querySelector('.color-name').value.trim();
        const colorFile = block.querySelector('.color-image-file');
        const colorUrl = block.querySelector('.color-image-url').value;
        
        if (colorName !== '') {
            formData.append('colorNames[]', colorName);
            if (colorFile.files[0]) {
                formData.append('colorImages[]', colorFile.files[0]);
                formData.append('colorExistingUrls[]', '');
            } else {
                formData.append('colorImages[]', new Blob());
                formData.append('colorExistingUrls[]', colorUrl);
            }
            
            const sizeRows = block.querySelectorAll('.size-row');
            let sizes = [];
            sizeRows.forEach(row => {
                const sName = row.querySelector('.size-name').value.trim();
                if (sName !== '') {
                    sizes.push({
                        size: sName,
                        stock: parseInt(row.querySelector('.size-stock').value || 0)
                    });
                }
            });
            formData.append('colorSizes[]', JSON.stringify(sizes));
        }
    });

    formData.append('description', document.getElementById('productDesc').value);
    formData.append('merchantEmail', localStorage.getItem('userEmail'));

    const apiUrl = id ? `api/index.php/products/${id}` : "api/index.php/products";

    try {
        const res = await fetch(apiUrl, {
            method: "POST", // The PHP backend usually expects POST for FormData even for updates
            body: formData
        });
        
        if(res.ok) {
            closeModal();
            loadProducts();
            alert('Product saved successfully!');
        } else {
            alert('Failed to save product.');
        }
    } catch(err) {
        console.error(err);
        alert('Error saving product.');
    }
}

async function deleteProduct(id) {
    if(confirm('Are you sure you want to delete this product?')) {
        try {
            const res = await fetch(`api/index.php/products/${id}`, { method: 'DELETE' });
            if(res.ok) {
                loadProducts();
                alert('Product deleted!');
            }
        } catch(e) {
            alert('Error deleting product.');
        }
    }
}

document.addEventListener('DOMContentLoaded', loadProducts);
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

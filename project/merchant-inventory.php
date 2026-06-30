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
    background: rgba(22, 24, 38, 0.8);
    border-radius: 12px;
    overflow: hidden;
}
.inventory-table th, .inventory-table td {
    padding: 15px;
    text-align: left;
    border-bottom: 1px solid rgba(197, 168, 92, 0.2);
}
.inventory-table th {
    background: rgba(197, 168, 92, 0.1);
    color: #c5a85c;
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
.modal-content { background-color: #161826; margin: 4vh auto; padding: 30px; border: 1px solid #c5a85c; width: 500px; border-radius: 12px; color: #fff; }
.modal-content input, .modal-content select, .modal-content textarea { width: 100%; padding: 10px; margin: 10px 0; border-radius: 6px; border: 1px solid rgba(197,168,92,0.5); background: #0b0c10; color: white;}
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
      <h2 id="modalTitle" style="color: #c5a85c; margin: 0;">Add Product</h2>
      <span onclick="closeModal()" style="color: #9aa0b4; font-size: 28px; font-weight: bold; cursor: pointer; line-height: 1;">&times;</span>
    </div>
    <form id="productForm" onsubmit="saveProduct(event)">
      <input type="hidden" id="productId">
      
      <label style="display:block; margin-top: 10px; color: #9aa0b4; font-size: 0.85rem;">Product Name *</label>
      <input type="text" id="productName" placeholder="e.g. Premium Football" required>
      
      <label style="display:block; margin-top: 10px; color: #9aa0b4; font-size: 0.85rem;">Category *</label>
      <input type="text" id="productCategory" placeholder="e.g. Ball" required>
      
      <div style="display: flex; gap: 10px;">
        <div style="flex: 1;">
          <label style="display:block; margin-top: 10px; color: #9aa0b4; font-size: 0.85rem;">Price (₹) *</label>
          <input type="number" id="productPrice" placeholder="e.g. 999" required>
        </div>
        <div style="flex: 1;">
          <label style="display:block; margin-top: 10px; color: #9aa0b4; font-size: 0.85rem;">Stock Quantity *</label>
          <input type="number" id="productStock" placeholder="Auto-calculated" required readonly style="opacity: 0.7; cursor: not-allowed;">
        </div>
      </div>

      <label style="display:block; margin-top: 10px; color: #9aa0b4; font-size: 0.85rem;">Available Colors (Comma Separated)</label>
      <input type="text" id="productColors" placeholder="e.g. Red, Blue, Black">

      <label style="display:block; margin-top: 10px; color: #9aa0b4; font-size: 0.85rem;">Product Sizes & Stock</label>
      <div id="sizesContainer"></div>
      <button type="button" onclick="addSizeField()" style="background: transparent; border: 1px dashed #c5a85c; color: #c5a85c; padding: 5px 10px; border-radius: 4px; cursor: pointer; margin-top: 5px; font-size: 0.85rem;">+ Add Size</button>

      <label style="display:block; margin-top: 10px; color: #9aa0b4; font-size: 0.85rem;">Image Source</label>
      <select id="imageSourceSelect" onchange="toggleImageInput()">
        <option value="device">Upload Image (Device)</option>
        <option value="url">Provide Image URL</option>
      </select>

      <div id="imageDeviceGroup">
        <input type="file" id="productImageFile" accept=".jpg,.jpeg,.png,.webp">
      </div>
      
      <div id="imageUrlGroup" style="display: none;">
        <input type="text" id="productImageUrl" placeholder="https://example.com/image.jpg or data:image/...">
      </div>

      <label style="display:block; margin-top: 10px; color: #9aa0b4; font-size: 0.85rem;">Description</label>
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
    document.getElementById('imageSourceSelect').value = 'device';
    document.getElementById('sizesContainer').innerHTML = '';
    document.getElementById('productStock').value = '0';
    document.getElementById('productStock').removeAttribute('readonly');
    document.getElementById('productStock').style.opacity = '1';
    document.getElementById('productStock').style.cursor = 'text';
    toggleImageInput();
    document.getElementById('productModal').style.display = 'block';
}

function editProduct(p) {
    document.getElementById('modalTitle').textContent = 'Edit Product';
    document.getElementById('productId').value = p.id;
    document.getElementById('productName').value = p.name;
    document.getElementById('productCategory').value = p.category;
    document.getElementById('productPrice').value = p.price;
    document.getElementById('productStock').value = p.stock;
    document.getElementById('productColors').value = p.colors || '';
    
    const imgUrl = p.image_url || '';
    if (imgUrl && !imgUrl.startsWith('uploads')) {
        document.getElementById('productImageUrl').value = imgUrl;
        document.getElementById('imageSourceSelect').value = 'url';
    } else {
        document.getElementById('productImageUrl').value = '';
        document.getElementById('imageSourceSelect').value = 'device';
    }
    toggleImageInput();

    const sizesContainer = document.getElementById('sizesContainer');
    sizesContainer.innerHTML = '';
    if (p.sizes) {
        try {
            const parsedSizes = JSON.parse(p.sizes);
            parsedSizes.forEach(s => addSizeField(s.size, s.stock));
        } catch(e) {}
    }
    updateTotalStock();

    document.getElementById('productDesc').value = p.description || '';
    document.getElementById('productModal').style.display = 'block';
}

function closeModal() {
    document.getElementById('productModal').style.display = 'none';
}

function toggleImageInput() {
    const source = document.getElementById('imageSourceSelect').value;
    if (source === 'device') {
        document.getElementById('imageDeviceGroup').style.display = 'block';
        document.getElementById('imageUrlGroup').style.display = 'none';
        document.getElementById('productImageUrl').value = '';
    } else {
        document.getElementById('imageDeviceGroup').style.display = 'none';
        document.getElementById('imageUrlGroup').style.display = 'block';
        document.getElementById('productImageFile').value = '';
    }
}

function addSizeField(size = '', stock = 0) {
    const container = document.getElementById('sizesContainer');
    const div = document.createElement('div');
    div.style.display = 'flex';
    div.style.gap = '10px';
    div.style.marginTop = '5px';
    div.innerHTML = `
        <input type="text" class="size-name" placeholder="Size (e.g. 8)" value="${size}" style="flex: 1; padding: 8px; border-radius: 4px; border: 1px solid rgba(197,168,92,0.5); background: #0b0c10; color: white;">
        <input type="number" class="size-stock" placeholder="Stock" value="${stock}" oninput="updateTotalStock()" style="flex: 1; padding: 8px; border-radius: 4px; border: 1px solid rgba(197,168,92,0.5); background: #0b0c10; color: white;" min="0">
        <button type="button" onclick="this.parentElement.remove(); updateTotalStock()" style="background: transparent; border: none; color: #ef4444; cursor: pointer; font-size: 1.2rem;">&times;</button>
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
    const id = document.getElementById('productId').value;
    
    const formData = new FormData();
    if(id) formData.append('id', id);
    formData.append('name', document.getElementById('productName').value);
    formData.append('category', document.getElementById('productCategory').value);
    formData.append('price', document.getElementById('productPrice').value);
    formData.append('stock', document.getElementById('productStock').value);
    formData.append('colors', document.getElementById('productColors').value);
    
    // Gather sizes
    const sizeNames = document.querySelectorAll('.size-name');
    const sizeStocks = document.querySelectorAll('.size-stock');
    const sizesArray = [];
    for(let i = 0; i < sizeNames.length; i++) {
        if(sizeNames[i].value.trim() !== '') {
            sizesArray.push({
                size: sizeNames[i].value.trim(),
                stock: parseInt(sizeStocks[i].value || 0)
            });
        }
    }
    if (sizesArray.length > 0) {
        formData.append('sizes', JSON.stringify(sizesArray));
    }

    formData.append('description', document.getElementById('productDesc').value);
    formData.append('imageUrl', document.getElementById('productImageUrl').value);
    formData.append('merchantEmail', localStorage.getItem('userEmail'));

    const fileInput = document.getElementById('productImageFile');
    if (fileInput.files[0]) {
        formData.append('productImage', fileInput.files[0]);
    }

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

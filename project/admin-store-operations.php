<?php
$pageTitle = "GLOBAL SPORTS ARENA | System Operations";
require_once __DIR__ . '/config/Database.php';

$db = (new Database())->getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    $prodId = $_POST['product_id'] ?? null;
    $name = $_POST['name'] ?? '';
    $category = $_POST['category'] ?? '';
    $price = $_POST['price'] ?? 0;
    $stock = $_POST['stock'] ?? 0;
    $description = $_POST['description'] ?? '';
    $colors = $_POST['colors'] ?? '';
    $sizes = $_POST['sizes'] ?? '';
    
    // Handle image upload if present
    $imageUrl = $_POST['existing_image'] ?? '';
    if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = __DIR__ . '/uploads/products/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
        $fileName = 'prod_' . uniqid() . '.' . pathinfo($_FILES['image_file']['name'], PATHINFO_EXTENSION);
        if (move_uploaded_file($_FILES['image_file']['tmp_name'], $uploadDir . $fileName)) {
            $imageUrl = 'uploads/products/' . $fileName;
        }
    }
    
    if ($action === 'add') {
        $stmt = $db->prepare("INSERT INTO products (name, category, price, stock, image_url, description, colors, sizes) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$name, $category, $price, $stock, $imageUrl, $description, $colors, $sizes]);
    } elseif ($action === 'edit' && $prodId) {
        $stmt = $db->prepare("UPDATE products SET name=?, category=?, price=?, stock=?, image_url=?, description=?, colors=?, sizes=? WHERE id=?");
        $stmt->execute([$name, $category, $price, $stock, $imageUrl, $description, $colors, $sizes, $prodId]);
    } elseif ($action === 'delete' && $prodId) {
        $stmt = $db->prepare("DELETE FROM products WHERE id=?");
        $stmt->execute([$prodId]);
    } elseif ($action === 'update_order' && isset($_POST['order_id'])) {
        $orderId = $_POST['order_id'];
        $orderStatus = $_POST['order_status'] ?? 'PENDING';
        $stmt = $db->prepare("UPDATE orders SET order_status=? WHERE id=?");
        $stmt->execute([$orderStatus, $orderId]);
    }
    
    header("Location: admin-store-operations.php");
    exit;
}

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';

// Fetch Products
$products = [];
try {
    $prodStmt = $db->query("SELECT * FROM products ORDER BY id DESC LIMIT 20");
    $products = $prodStmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Table might not exist or error
}

// Fetch Orders
$orders = [];
try {
    $orderStmt = $db->query("SELECT * FROM orders ORDER BY order_date DESC LIMIT 20");
    $orders = $orderStmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Table might not exist or error
}

?>

<link rel="stylesheet" href="assets/css/AdminDashboard.css?v=7">

<div class="admin-dashboard px-4 sm:px-8 py-10" style="background: #0b0c10; color: #f5f6fa; min-height: 100vh;">
  <?php require_once __DIR__ . '/includes/admin_navbar.php'; ?>
  <!-- Header -->
  <div class="admin-header" style="border-bottom: 1px solid rgba(197, 168, 92, 0.2); padding-bottom: 20px;">
    <div class="header-left">
      <div class="admin-badge" style="background: linear-gradient(135deg, #c5a85c 0%, #8c7237 100%); color: #0b0c10; fontWeight: bold; display: inline-block; padding: 4px 12px; border-radius: 4px; margin-bottom: 10px;">
        ⚙️ Administrative Core
      </div>
      <h1>System Operations</h1>
      <p>Real-time tournament CRUD controls, NXL ledger wallets adjustments, and synchronized orders listings in MySQL</p>
    </div>
  </div>

  <!-- Loading Overlay -->
  <div id="adminGlobalLoading" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(11,12,16,0.8); z-index: 9999; justify-content: center; align-items: center; color: #c5a85c; font-size: 1.5rem; font-weight: bold;">
    ? Processing request...
  </div>

  <!-- Dynamic KPI Stats Grid -->
  <div class="stats-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 15px; margin-top: 30px;">
    <div class="stat-card" style="background: #12131c; border: 1px solid rgba(197, 168, 92, 0.15); padding: 20px; border-radius: 12px; display: flex; align-items: center; gap: 15px;">
      <div class="stat-icon" style="font-size: 2rem;">📈</div>
      <div class="stat-info">
        <h3 style="font-size: 0.9rem; color: #9aa0b4; margin: 0 0 5px 0;">Live Total Sales</h3>
        <p class="stat-value" id="statTotalSales" style="color: #22c55e; font-size: 1.5rem; font-weight: bold; margin: 0;">?0</p>
        <span class="stat-change" style="font-size: 0.75rem; color: #9aa0b4;">Synchronized DB</span>
      </div>
    </div>

    <div class="stat-card" style="background: #12131c; border: 1px solid rgba(197, 168, 92, 0.15); padding: 20px; border-radius: 12px; display: flex; align-items: center; gap: 15px;">
      <div class="stat-icon" style="font-size: 2rem;">📈</div>
      <div class="stat-info">
        <h3 style="font-size: 0.9rem; color: #9aa0b4; margin: 0 0 5px 0;">Total NXL Issued</h3>
        <p class="stat-value" id="statTotalNxl" style="color: #c5a85c; font-size: 1.5rem; font-weight: bold; margin: 0;">0 Coins</p>
        <span class="stat-change" style="font-size: 0.75rem; color: #9aa0b4;">Loyalty Ledger</span>
      </div>
    </div>

    <div class="stat-card" style="background: #12131c; border: 1px solid rgba(197, 168, 92, 0.15); padding: 20px; border-radius: 12px; display: flex; align-items: center; gap: 15px;">
      <div class="stat-icon" style="font-size: 2rem;">📈</div>
      <div class="stat-info">
        <h3 style="font-size: 0.9rem; color: #9aa0b4; margin: 0 0 5px 0;">Active Customers</h3>
        <p class="stat-value" id="statActiveCustomers" style="font-size: 1.5rem; font-weight: bold; margin: 0;">0 Users</p>
        <span class="stat-change" style="font-size: 0.75rem; color: #9aa0b4;">Logged Profile</span>
      </div>
    </div>

    <div class="stat-card" style="background: #12131c; border: 1px solid rgba(197, 168, 92, 0.15); padding: 20px; border-radius: 12px; display: flex; align-items: center; gap: 15px;">
      <div class="stat-icon" style="font-size: 2rem;">📈</div>
      <div class="stat-info">
        <h3 style="font-size: 0.9rem; color: #9aa0b4; margin: 0 0 5px 0;">Active Merchants</h3>
        <p class="stat-value" id="statMerchants" style="color: #38bdf8; font-size: 1.5rem; font-weight: bold; margin: 0;">0 Merchants</p>
        <span class="stat-change" style="font-size: 0.75rem; color: #9aa0b4;">Registered Partners</span>
      </div>
    </div>
  </div>

  <div class="admin-content" style="margin-top: 40px;">
    
    <div style='display:flex; flex-direction:column; gap:30px;'>
<!-- Products CRUD Management Section -->
      <style>
        .product-form-section {
          width: max-content;
          max-width: 100%;
          margin: 0 auto;
          border-radius: 20px;
          padding: 25px;
          box-sizing: border-box;
        }
        @media (max-width: 767px) {
          .product-form-section { padding: 15px; }
        }
      </style>
      <div class="admin-card product-form-section">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h2 style="color: #c5a85c; margin: 0;">🛍️ Manage Products</h2>
            <button onclick="openProductModal()" class="btn-gold" style="padding: 8px 15px; font-size: 14px;">+ Add New Product</button>
        </div>
        
          <div class="overflow-x-auto"><table style="width: 100%; border-collapse: collapse; font-size: 0.9rem; text-align: left;">
            <thead>
              <tr style="border-bottom: 1px solid rgba(197,168,92,0.25); color: #c5a85c;">
                <th style="padding: 12px;">ID</th>
                <th style="padding: 12px;">Image</th>
                <th style="padding: 12px;">Name</th>
                <th style="padding: 12px;">Category</th>
                <th style="padding: 12px;">Price (?)</th>
                <th style="padding: 12px;">Stock</th>
                <th style="padding: 12px;">Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($products)): ?>
              <tr>
                <td colspan="7" style="text-align: center; padding: 20px; color: #9aa0b4;">No products found in the database.</td>
              </tr>
              <?php else: ?>
                  <?php foreach ($products as $prod): ?>
                  <tr style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                    <td style="padding: 12px;">#<?= $prod['id'] ?></td>
                    <td style="padding: 12px;">
                        <?php 
                        $img = $prod['image_url'];
                        if (empty($img) || (strpos($img, 'http') !== 0 && strpos($img, 'uploads/') !== 0 && strpos($img, 'data:image') !== 0)) {
                            if (stripos($prod['name'], 'shoes') !== false) {
                                $img = "https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=400&h=400&fit=crop";
                            } elseif (stripos($prod['name'], 'jersey') !== false) {
                                $img = "https://images.unsplash.com/photo-1517466787929-bc90951d0974?w=400&h=400&fit=crop";
                            } elseif (stripos($prod['name'], 'racket') !== false) {
                                $img = "https://images.unsplash.com/photo-1626224583764-f87db24ac4ea?w=400&h=400&fit=crop";
                            } elseif (stripos($prod['name'], 'ball') !== false || stripos($prod['name'], 'football') !== false) {
                                $img = "https://images.unsplash.com/photo-1614632537190-23e4146777db?w=400&h=400&fit=crop";
                            } elseif (stripos($prod['name'], 'gloves') !== false) {
                                $img = "https://images.unsplash.com/photo-1581009146145-b5ef050c2e1e?w=400&h=400&fit=crop";
                            } else {
                                $img = "https://images.unsplash.com/photo-1602143407151-7111542de6e8?w=400&h=400&fit=crop";
                            }
                        }
                        ?>
                        <img src="<?= htmlspecialchars($img) ?>" alt="Product" style="width: 40px; height: 40px; object-fit: cover; border-radius: 4px;">
                    </td>
                    <td style="padding: 12px; font-weight: bold;"><?= htmlspecialchars($prod['name']) ?></td>
                    <td style="padding: 12px;"><span style="background: rgba(197,168,92,0.1); padding: 4px 8px; border-radius: 4px; font-size: 0.8rem; color: #c5a85c;"><?= htmlspecialchars($prod['category']) ?></span></td>
                    <td style="padding: 12px;">?<?= number_format($prod['price'], 2) ?></td>
                    <td style="padding: 12px; color: <?= $prod['stock'] > 10 ? '#22c55e' : '#f59e0b' ?>;"><?= $prod['stock'] ?> in stock</td>
                    <td style="padding: 12px;">
                        <div style="display: flex; gap: 8px; align-items: center;">
                            <button onclick='openProductModal(<?= htmlspecialchars(json_encode($prod), ENT_QUOTES, "UTF-8") ?>)' style="background: #38bdf8; border:none; color:white; padding: 6px 12px; border-radius: 6px; cursor:pointer; font-size: 0.85rem; display: flex; align-items: center; gap: 5px;" title="Edit"><i class="fas fa-edit"></i> Edit</button>
                            
                            <form method="POST" style="margin:0;" onsubmit="return confirm('Are you sure you want to delete this product?');">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="product_id" value="<?= $prod['id'] ?>">
                                <button type="submit" style="background: #dc3545; border:none; color:white; padding: 6px 12px; border-radius: 6px; cursor:pointer; font-size: 0.85rem; display: flex; align-items: center; gap: 5px;" title="Delete"><i class="fas fa-trash-alt"></i> Delete</button>
                            </form>
                        </div>
                    </td>
                  </tr>
                  <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table></div>
      </div> <!-- /card products -->
          
<!-- Dynamic DB Order logs -->
      <style>
        .order-text-muted { color: #9aa0b4; }
        body.light-theme .order-text-muted { color: #64748b; }
        .order-row { border-bottom: 1px solid rgba(255,255,255,0.05); }
        body.light-theme .order-row { border-bottom: 1px solid rgba(0,0,0,0.05); }
      </style>
      <div class="admin-card" style="border-radius: 20px; padding: 25px;">
        <h2 style="color: #c5a85c; margin: 0 0 20px 0;">📦 Dynamic User Order Purchases</h2>
        
        <div style="overflow-x: auto;">
          <div class="overflow-x-auto"><table style="width: 100%; border-collapse: collapse; font-size: 0.85rem; text-align: left;">
            <thead>
              <tr style="border-bottom: 1px solid rgba(197,168,92,0.25); color: #c5a85c;">
                <th style="padding: 12px;">Order Ref</th>
                <th style="padding: 12px;">Date</th>
                <th style="padding: 12px;">Total Paid</th>
                <th style="padding: 12px;">NXL Earned</th>
                <th style="padding: 12px;">Payment</th>
                <th style="padding: 12px;">Status</th>
                <th style="padding: 12px;">Action</th>
              </tr>
            </thead>
            <tbody id="ordersTableBody">
              <?php if (empty($orders)): ?>
              <tr>
                <td colspan="7" style="text-align: center; padding: 20px; color: #9aa0b4;">No orders found in the database.</td>
              </tr>
              <?php else: ?>
                  <?php foreach ($orders as $ord): ?>
                  <tr class="order-row">
                    <td style="padding: 12px; font-weight: bold;">#<?= htmlspecialchars($ord['id']) ?></td>
                    <td class="order-text-muted" style="padding: 12px;"><?= date('M d, Y H:i', strtotime($ord['order_date'])) ?></td>
                    <td style="padding: 12px; color: #22c55e;">?<?= number_format($ord['total_amount'], 2) ?></td>
                    <td style="padding: 12px; color: #c5a85c;">+<?= $ord['nxl_coins_earned'] ?> <i class="fas fa-coins" style="font-size:0.75rem;"></i></td>
                    <td style="padding: 12px;">
                        <?php 
                        $payColor = strtolower($ord['payment_status']) === 'paid' ? '#22c55e' : '#f59e0b';
                        ?>
                        <span style="color: <?= $payColor ?>; font-weight: bold;"><?= strtoupper(htmlspecialchars($ord['payment_status'])) ?></span>
                    </td>
                    <td style="padding: 12px;">
                        <span style="background: rgba(197,168,92,0.1); color: #c5a85c; padding: 4px 8px; border-radius: 4px; font-size: 0.75rem; text-transform: uppercase;">
                            <?= htmlspecialchars($ord['order_status']) ?>
                        </span>
                    </td>
                    <td style="padding: 12px;">
                        <button onclick="openOrderModal('<?= htmlspecialchars($ord['id']) ?>', '<?= htmlspecialchars($ord['order_status']) ?>')" style="background:transparent; border: 1px solid rgba(197,168,92,0.5); color:#c5a85c; padding: 4px 10px; border-radius: 4px; cursor:pointer; font-size: 0.8rem; transition: background 0.3s;" onmouseover="this.style.background='rgba(197,168,92,0.1)'" onmouseout="this.style.background='transparent'">Update</button>
                    </td>
                  </tr>
                  <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table></div>
        </div> <!-- /overflow -->
      </div> <!-- /card orders -->
    </div> <!-- /flex col -->
  </div> <!-- /admin-content -->
</div> <!-- /admin-dashboard -->

<!-- Product Modal -->
<style>
.modal-input {
    width: 100%; padding: 10px; background: #0b0c10; border: 1px solid rgba(255,255,255,0.1); color: white; border-radius: 6px;
}
body.light-theme .modal-input {
    background: #ffffff; border: 1px solid rgba(197,168,92,0.4); color: #1a1a1a;
}
.modal-label {
    display:block; color:#9aa0b4; margin-bottom:5px;
}
body.light-theme .modal-label {
    color: #1a1a1a; font-weight: 500;
}
body.light-theme .modal-cancel-btn {
    border: 1px solid rgba(197,168,92,0.6) !important; color: #1a1a1a !important;
}
</style>
<div id="productModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(11,12,16,0.8); z-index: 10000; justify-content: center; align-items: center;">
    <div class="admin-card modal-content" style="border-radius: 12px; width: 90%; max-width: 500px; padding: 25px;">
        <h3 id="modalTitle" style="color: #c5a85c; margin-top: 0;">Add New Product</h3>
        
        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="action" id="modalAction" value="add">
            <input type="hidden" name="product_id" id="modalProductId" value="">
            <input type="hidden" name="existing_image" id="modalExistingImage" value="">
            
            <div style="margin-bottom: 15px;">
                <label class="modal-label">Name</label>
                <input type="text" name="name" id="modalName" required class="modal-input">
            </div>
            
            <div style="margin-bottom: 15px;">
                <label class="modal-label">Category</label>
                <select name="category" id="modalCategory" required class="modal-input">
                    <option value="Footwear">Footwear</option>
                    <option value="Apparel">Apparel</option>
                    <option value="Equipment">Equipment</option>
                    <option value="Accessories">Accessories</option>
                    <option value="Fitness">Fitness</option>
                    <option value="Shoes">Shoes</option>
                    <option value="Jersey">Jersey</option>
                    <option value="Racket">Racket</option>
                    <option value="Ball">Ball</option>
                </select>
            </div>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;">
                <div>
                    <label class="modal-label">Price (?)</label>
                    <input type="number" step="0.01" name="price" id="modalPrice" required class="modal-input">
                </div>
                <div>
                    <label class="modal-label">Stock Quantity</label>
                    <input type="number" name="stock" id="modalStock" required class="modal-input">
                </div>
            </div>
            
            <div style="margin-bottom: 15px;">
                <label class="modal-label">Description (Optional)</label>
                <textarea name="description" id="modalDescription" rows="3" class="modal-input"></textarea>
            </div>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;">
                <div>
                    <label class="modal-label">Colors (Comma separated)</label>
                    <input type="text" name="colors" id="modalColors" placeholder="e.g. Red, Blue, Green" class="modal-input">
                </div>
                <div>
                    <label class="modal-label">Sizes</label>
                    <select name="sizes" id="modalSizes" class="modal-input">
                        <option value="">None / Not Applicable</option>
                        <option value="S, M, L, XL, XXL">Apparel (S, M, L, XL, XXL)</option>
                        <option value="6, 7, 8, 9, 10, 11">Shoes (6, 7, 8, 9, 10, 11)</option>
                        <option value="Free Size">Free Size</option>
                    </select>
                </div>
            </div>
            
            <div style="margin-bottom: 25px;">
                <label class="modal-label">Image Upload (Optional)</label>
                <input type="file" name="image_file" accept="image/*" style="width: 100%; color: #9aa0b4;">
                <p id="modalImageText" style="font-size: 0.8rem; color: #38bdf8; margin-top: 5px;"></p>
            </div>
            
            <div style="display: flex; justify-content: flex-end; gap: 10px;">
                <button type="button" onclick="closeProductModal()" class="modal-cancel-btn" style="padding: 10px 15px; background: transparent; border: 1px solid rgba(255,255,255,0.2); color: white; border-radius: 6px; cursor: pointer;">Cancel</button>
                <button type="submit" class="btn-gold" style="padding: 10px 20px; font-weight: bold;">Save Product</button>
            </div>
        </form>
    </div>
</div>

<!-- Order Update Modal -->
<div id="orderModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(11,12,16,0.85); z-index: 10000; justify-content: center; align-items: center;">
    <div class="admin-card modal-content" style="border: 1px solid rgba(197, 168, 92, 0.3); border-radius: 12px; padding: 30px; width: 400px; max-width: 90%;">
        <h2 style="color: #c5a85c; margin-top: 0;">Update Order <span id="modalOrderRef"></span></h2>
        
        <form method="POST" style="display: flex; flex-direction: column; gap: 20px;">
            <input type="hidden" name="action" value="update_order">
            <input type="hidden" name="order_id" id="modalOrderId">
            
            <div>
                <label class="modal-label">Order Status</label>
                <select name="order_status" id="modalOrderStatus" class="modal-input">
                    <option value="PENDING">PENDING</option>
                    <option value="CONFIRMED">CONFIRMED</option>
                    <option value="PROCESSING">PROCESSING</option>
                    <option value="SHIPPED">SHIPPED</option>
                    <option value="DELIVERED">DELIVERED</option>
                    <option value="CANCELLED">CANCELLED</option>
                </select>
            </div>
            
            <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 10px;">
                <button type="button" onclick="closeOrderModal()" class="modal-cancel-btn" style="padding: 10px 15px; background: transparent; border: 1px solid rgba(255,255,255,0.2); color: white; border-radius: 6px; cursor: pointer;">Cancel</button>
                <button type="submit" class="btn-gold" style="padding: 10px 20px; font-weight: bold; cursor: pointer;">Save Status</button>
            </div>
        </form>
    </div>
</div>

<script>
function openProductModal(product = null) {
    document.getElementById('productModal').style.display = 'flex';
    
    let sizesSelect = document.getElementById('modalSizes');
    
    if (product) {
        document.getElementById('modalTitle').innerText = 'Edit Product';
        document.getElementById('modalAction').value = 'edit';
        document.getElementById('modalProductId').value = product.id;
        document.getElementById('modalName').value = product.name;
        document.getElementById('modalCategory').value = product.category;
        document.getElementById('modalPrice').value = product.price;
        document.getElementById('modalStock').value = product.stock;
        document.getElementById('modalDescription').value = product.description || '';
        document.getElementById('modalColors').value = product.colors || '';
        
        // Handle custom sizes that aren't in the preset dropdown
        if (product.sizes) {
            let exists = Array.from(sizesSelect.options).some(opt => opt.value === product.sizes);
            if (!exists) {
                let opt = document.createElement('option');
                opt.value = product.sizes;
                opt.text = "Custom Format (Keep Existing)";
                sizesSelect.add(opt);
            }
        }
        sizesSelect.value = product.sizes || '';
        
        document.getElementById('modalExistingImage').value = product.image_url;
        document.getElementById('modalImageText').innerText = product.image_url ? 'Has existing image.' : 'No existing image.';
    } else {
        document.getElementById('modalTitle').innerText = 'Add New Product';
        document.getElementById('modalAction').value = 'add';
        document.getElementById('modalProductId').value = '';
        document.getElementById('modalName').value = '';
        document.getElementById('modalCategory').value = 'Footwear';
        document.getElementById('modalPrice').value = '';
        document.getElementById('modalStock').value = '';
        document.getElementById('modalDescription').value = '';
        document.getElementById('modalColors').value = '';
        sizesSelect.value = '';
        document.getElementById('modalExistingImage').value = '';
        document.getElementById('modalImageText').innerText = '';
    }
}

function closeProductModal() {
    document.getElementById('productModal').style.display = 'none';
}

function openOrderModal(orderId, currentStatus) {
    document.getElementById('modalOrderId').value = orderId;
    document.getElementById('modalOrderRef').innerText = '#' + orderId;
    document.getElementById('modalOrderStatus').value = currentStatus;
    document.getElementById('orderModal').style.display = 'flex';
}

function closeOrderModal() {
    document.getElementById('orderModal').style.display = 'none';
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

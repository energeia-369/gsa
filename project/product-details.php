<?php
$pageTitle = "GLOBAL SPORTS ARENA | Product Details";
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
require_once __DIR__ . '/models/Product.php';

$productId = isset($_GET['id']) ? intval($_GET['id']) : 1;

$productModel = new Product();
$product = null;

try {
    $product = $productModel->findById($productId);
} catch (Exception $e) {
    error_log("Failed to load product detail: " . $e->getMessage());
}

// Default product fallback if not found
if (!$product) {
    $product = [
        "id" => 1,
        "name" => "Sports Shoes",
        "price" => 1999,
        "category" => "Footwear",
        "description" => "Experience next-level performance with our premium sports shoes. Engineered for runners and athletes, featuring breathable mesh, responsive cushioning, and durable rubber outsole for maximum grip.",
        "image_url" => "https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=400&h=400&fit=crop",
        "stock" => 50
    ];
}

$price = floatval($product['price']);
$originalPrice = $price * 1.5;
$imageUrl = $product['image_url'] ?? '';
if (empty($imageUrl) || (strpos($imageUrl, 'http') !== 0 && strpos($imageUrl, 'uploads/') !== 0 && strpos($imageUrl, 'data:image') !== 0)) {
    if (stripos($product['name'], 'shoes') !== false) {
        $imageUrl = "https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=400&h=400&fit=crop";
    } elseif (stripos($product['name'], 'jersey') !== false) {
        $imageUrl = "https://images.unsplash.com/photo-1517466787929-bc90951d0974?w=400&h=400&fit=crop";
    } elseif (stripos($product['name'], 'racket') !== false) {
        $imageUrl = "https://images.unsplash.com/photo-1626224583764-f87db24ac4ea?w=400&h=400&fit=crop";
    } elseif (stripos($product['name'], 'ball') !== false || stripos($product['name'], 'football') !== false) {
        $imageUrl = "https://images.unsplash.com/photo-1614632537190-23e4146777db?w=400&h=400&fit=crop";
    } elseif (stripos($product['name'], 'gloves') !== false) {
        $imageUrl = "https://images.unsplash.com/photo-1581009146145-b5ef050c2e1e?w=400&h=400&fit=crop";
    } elseif (stripos($product['name'], 'bottle') !== false) {
        $imageUrl = "https://images.unsplash.com/photo-1602143407151-7111542de6e8?w=400&h=400&fit=crop";
    } else {
        $imageUrl = "https://images.unsplash.com/photo-1602143407151-7111542de6e8?w=400&h=400&fit=crop";
    }
}

$sizesJson = $product['sizes'] ?? null;
$sizes = [];
if ($sizesJson) {
    $parsed = json_decode($sizesJson, true);
    if (is_array($parsed)) {
        $sizes = $parsed;
    }
}
if (empty($sizes) && stripos($product['category'], 'Footwear') !== false) {
    $sizes = [
        ["size" => "6", "stock" => 10],
        ["size" => "7", "stock" => 10],
        ["size" => "8", "stock" => 10],
        ["size" => "9", "stock" => 10],
        ["size" => "10", "stock" => 10],
    ];
}
$firstAvailableSize = null;
foreach ($sizes as $s) {
    if (intval($s['stock'] ?? 0) > 0) {
        $firstAvailableSize = $s['size'];
        break;
    }
}
?>

<link rel="stylesheet" href="assets/css/ProductDetails.css?v=2">

<div class="back-container">
    <a href="products.php" style="display: inline-flex; align-items: center; color: #c5a85c; text-decoration: none; font-weight: bold; font-size: 1.1rem; transition: all 0.3s;" onmouseover="this.style.color='#d4bc74'; this.style.transform='translateX(-5px)'" onmouseout="this.style.color='#c5a85c'; this.style.transform='translateX(0)'">
        <span style="margin-right: 8px; font-size: 1.2rem;">&larr;</span> Back to Store
    </a>
</div>

<div class="product-details flex flex-col lg:flex-row gap-8 max-w-7xl mx-auto px-4 py-10">
  <!-- Left Column - Gallery -->
  <div class="product-gallery">
    <div class="product-image-box">
      <div class="product-image" style="background: none; display: flex; align-items: center; justify-content: center;">
        <img src="<?php echo htmlspecialchars($imageUrl); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" style="max-width: 100%; max-height: 100%; border-radius: 15px; object-fit: contain;">
      </div>
    </div>
    <div class="thumbnail-strip">
      <div class="thumbnail active" style="background-image: url('<?php echo htmlspecialchars($imageUrl); ?>'); background-size: cover; background-position: center;"></div>
      <div class="thumbnail" style="background-image: url('<?php echo htmlspecialchars($imageUrl); ?>'); background-size: cover; background-position: center; filter: grayscale(50%);"></div>
      <div class="thumbnail" style="background-image: url('<?php echo htmlspecialchars($imageUrl); ?>'); background-size: cover; background-position: center; filter: grayscale(50%);"></div>
    </div>
  </div>

  <!-- Right Column - Product Info -->
  <div class="product-info">
    <span class="product-badge">🔥 Best Seller</span>
    <h1><?php echo htmlspecialchars($product['name']); ?></h1>
    <div class="rating">
      <span class="stars" style="color: #c5a85c;">★★★★★</span>
      <span class="review-count">(2,384 reviews)</span>
    </div>
    <p class="product-description">
      <?php echo htmlspecialchars($product['description'] ?: 'No description available for this premium gear.'); ?>
    </p>
    
    <div class="price-section" style="display: flex; align-items: center; gap: 10px;">
      <h2 id="displayPrice">₹<?php echo number_format($price); ?></h2>
      <span class="original-price" id="displayOrigPrice">₹<?php echo number_format($originalPrice); ?></span>
      <span class="discount">50% OFF</span>
      
      <select id="detailCurrency" onchange="updateDetailCurrency()" style="margin-left: auto; padding: 5px 10px; border-radius: 8px; background: #12131c; color: #c5a85c; border: 1px solid rgba(197, 168, 92, 0.5); outline: none;">
        <option value="INR">₹ INR</option>
        <option value="USD">$ USD</option>
      </select>
    </div>

    <!-- Color Options -->
    <div class="color-section">
      <?php
      $productColors = [];
      $rawColors = $product['colors'] ?? '';
      $isJson = false;
      
      if (!empty($rawColors)) {
          $decoded = json_decode($rawColors, true);
          if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
              $productColors = $decoded;
              $isJson = true;
          } else {
              $productColors = explode(',', $rawColors);
          }
      }
      
      if (!empty($productColors)):
          if ($isJson) {
              $firstColorName = $productColors[0]['color'] ?? 'Unknown';
          } else {
              $firstColorRaw = trim($productColors[0]);
              $firstColorName = ucfirst($firstColorRaw);
              if (strpos($firstColorRaw, '|') !== false) {
                  $firstColorName = trim(explode('|', $firstColorRaw)[0]);
              }
          }
      ?>
          <p>Color: <span class="selected-value" id="colorSelectedVal"><?php echo htmlspecialchars($firstColorName); ?></span></p>
          <div class="color-options">
            <?php foreach ($productColors as $colorItem): 
                $colorSizesData = [];
                if ($isJson && !empty($colorItem['sizes'])) {
                    $colorSizesData = $colorItem['sizes'];
                } else if (!empty($sizes)) {
                    $colorSizesData = $sizes;
                }
                $sizesAttr = htmlspecialchars(json_encode($colorSizesData), ENT_QUOTES, 'UTF-8');
                
                if ($isJson) {
                    $colorName = htmlspecialchars($colorItem['color'] ?? '');
                    $colorImg = htmlspecialchars($colorItem['image'] ?? '');
                    if (!empty($colorImg)) {
                        echo "<div class=\"color-circle\" style=\"background-image: url('{$colorImg}'); background-size: cover; background-position: center; border: 1px solid rgba(197,168,92,0.5);\" onclick=\"selectColorImage('{$colorImg}', '{$colorName}', this, '{$sizesAttr}')\" title=\"{$colorName}\"></div>";
                    } else {
                        echo "<div class=\"color-circle\" style=\"background-color: #555;\" onclick=\"selectColorImage('', '{$colorName}', this, '{$sizesAttr}')\" title=\"{$colorName}\"></div>";
                    }
                } else {
                    $color = trim($colorItem);
                    if (empty($color)) continue;
                    $colorVal = $color;
                    $colorName = ucfirst($color);
                    if (strpos($color, '|') !== false) {
                        $parts = explode('|', $color);
                        $colorName = trim($parts[0]);
                        $colorVal = trim($parts[1]);
                    }
            ?>
              <div class="color-circle" style="background-color: <?php echo htmlspecialchars($colorVal); ?>;" onclick="selectColor('<?php echo htmlspecialchars($colorVal); ?>', '<?php echo htmlspecialchars($colorName); ?>', this, '<?php echo $sizesAttr; ?>')"></div>
            <?php } endforeach; ?>
          </div>
      <?php else: ?>
          <p>Color: <span class="selected-value" id="colorSelectedVal">Neon Green</span></p>
          <div class="color-options">
            <div class="color-circle" style="background-color: #22c55e;" onclick="selectColor('#22c55e', 'Neon Green', this, '[]')"></div>
            <div class="color-circle" style="background-color: #3b82f6;" onclick="selectColor('#3b82f6', 'Royal Blue', this, '[]')"></div>
            <div class="color-circle" style="background-color: #a855f7;" onclick="selectColor('#a855f7', 'Purple Velvet', this, '[]')"></div>
            <div class="color-circle" style="background-color: #ef4444;" onclick="selectColor('#ef4444', 'Flame Red', this, '[]')"></div>
          </div>
      <?php endif; ?>
    </div>

    <!-- Size Selection -->
    <div class="size-section" id="sizeSection" style="<?php echo empty($sizes) ? 'display: none;' : ''; ?>">
      <p>Select Size</p>
      <div class="size-options" id="sizeOptionsContainer">
        <?php if (!empty($sizes)): foreach ($sizes as $s): 
            $sz = htmlspecialchars($s['size']);
            $st = intval($s['stock'] ?? 0);
            $disabled = $st <= 0 ? 'disabled' : '';
            $style = $st <= 0 ? 'opacity: 0.5; text-decoration: line-through; cursor: not-allowed;' : '';
            $active = ($sz == $firstAvailableSize) ? 'active' : '';
        ?>
            <button class="size-btn <?php echo $active; ?>" style="<?php echo $style; ?>" <?php echo $disabled; ?> onclick="selectSize('<?php echo $sz; ?>', this)"><?php echo $sz; ?></button>
        <?php endforeach; endif; ?>
      </div>
    </div>

    <!-- Quantity Selector -->
    <div class="quantity-section">
      <p>Quantity</p>
      <div class="quantity-selector">
        <button class="qty-btn" onclick="adjustQty(-1)">−</button>
        <span class="qty-number" id="qtyNumber">1</span>
        <button class="qty-btn" onclick="adjustQty(1)">+</button>
      </div>
    </div>

    <!-- Action Buttons -->
    <div class="action-buttons">
      <button class="cart-btn" onclick="addProductToCart()">Add to Cart 🛒</button>
      <button class="buy-btn" onclick="buyProductNow()">Buy Now ⚡</button>
    </div>

    <!-- Delivery Info -->
    <div class="delivery-info">
      <div class="info-item">
        <span>🚚</span>
        <span>Free Delivery on orders above ₹999</span>
      </div>
      <div class="info-item">
        <span>↺</span>
        <span>30-Day Easy Returns</span>
      </div>
    </div>
  </div>
</div>

<script>
let selectedSize = "<?php echo htmlspecialchars($firstAvailableSize ?? ''); ?>";
let selectedColorName = "Neon Green";
let quantity = 1;

const productJson = <?php echo json_encode([
    "id" => intval($product['id']),
    "name" => $product['name'],
    "price" => floatval($product['price']),
    "image" => $imageUrl,
    "category" => $product['category']
]); ?>;

const basePrice = <?php echo $price; ?>;
const baseOrigPrice = <?php echo $originalPrice; ?>;

function updateDetailCurrency() {
    const currency = document.getElementById("detailCurrency").value;
    const isUSD = currency === "USD";
    const rate = isUSD ? 0.012 : 1;
    const symbol = isUSD ? "$" : "₹";
    
    document.getElementById("displayPrice").innerText = symbol + (basePrice * rate).toLocaleString(undefined, {minimumFractionDigits: isUSD ? 2 : 0, maximumFractionDigits: isUSD ? 2 : 0});
    document.getElementById("displayOrigPrice").innerText = symbol + (baseOrigPrice * rate).toLocaleString(undefined, {minimumFractionDigits: isUSD ? 2 : 0, maximumFractionDigits: isUSD ? 2 : 0});
}

function selectSize(size, btn) {
    selectedSize = size;
    const buttons = document.querySelectorAll(".size-btn");
    buttons.forEach(b => b.classList.remove("active"));
    btn.classList.add("active");
}

function renderSizes(sizesJsonStr) {
    try {
        const sizesArr = JSON.parse(sizesJsonStr);
        const section = document.getElementById("sizeSection");
        const container = document.getElementById("sizeOptionsContainer");
        if (!sizesArr || sizesArr.length === 0) {
            section.style.display = "none";
            selectedSize = "";
            return;
        }
        section.style.display = "block";
        let html = "";
        let firstAvailable = null;
        sizesArr.forEach(s => {
            const sz = s.size;
            const st = parseInt(s.stock || 0);
            const disabled = st <= 0 ? "disabled" : "";
            const style = st <= 0 ? "opacity: 0.5; text-decoration: line-through; cursor: not-allowed;" : "";
            if (st > 0 && !firstAvailable) firstAvailable = sz;
            html += `<button class="size-btn" style="${style}" ${disabled} onclick="selectSize('${sz}', this)">${sz}</button>`;
        });
        container.innerHTML = html;
        
        // Auto select first available
        if (firstAvailable) {
            const firstBtn = container.querySelector(`button:not([disabled])`);
            if (firstBtn) selectSize(firstAvailable, firstBtn);
        } else {
            selectedSize = "";
        }
    } catch (e) {}
}

function selectColorImage(imageUrl, colorName, element, sizesJson = '[]') {
    selectedColorName = colorName;
    document.getElementById("colorSelectedVal").textContent = colorName;
    
    // Visually mark active color option
    const circles = document.querySelectorAll(".color-circle");
    circles.forEach(c => c.style.outline = "none");
    element.style.outline = "2px solid #c5a85c";

    // Swap the main product image
    const mainImg = document.querySelector('.product-image-box .product-image img');
    if (mainImg && imageUrl) {
        mainImg.src = imageUrl;
    }

    // Remove any existing tint if present
    const tintOverlay = document.getElementById('imageColorTint');
    if (tintOverlay) {
        tintOverlay.style.opacity = '0';
    }
    
    renderSizes(sizesJson);
}

function selectColor(colorHex, colorName, element, sizesJson = '[]') {
    selectedColorName = colorName;
    document.getElementById("colorSelectedVal").textContent = colorName;
    // Visually mark active color option
    const circles = document.querySelectorAll(".color-circle");
    circles.forEach(c => c.style.outline = "none");
    element.style.outline = "2px solid #c5a85c";

    // Dynamically tint the product image
    const imgContainer = document.querySelector('.product-image-box .product-image');
    if (imgContainer) {
        let tintOverlay = document.getElementById('imageColorTint');
        if (!tintOverlay) {
            tintOverlay = document.createElement('div');
            tintOverlay.id = 'imageColorTint';
            tintOverlay.style.position = 'absolute';
            tintOverlay.style.top = '0';
            tintOverlay.style.left = '0';
            tintOverlay.style.width = '100%';
            tintOverlay.style.height = '100%';
            tintOverlay.style.pointerEvents = 'none';
            tintOverlay.style.mixBlendMode = 'color'; // Applies the color while keeping shadows/highlights intact
            tintOverlay.style.borderRadius = '15px';
            tintOverlay.style.transition = 'background-color 0.4s ease';
            
            imgContainer.style.position = 'relative';
            imgContainer.appendChild(tintOverlay);
        }
        
        // If color is white or transparent, remove tint
        if (colorHex.toLowerCase() === '#ffffff' || colorHex.toLowerCase() === 'white') {
            tintOverlay.style.opacity = '0';
        } else {
            tintOverlay.style.backgroundColor = colorHex;
            tintOverlay.style.opacity = '0.6';
        }
    }
    renderSizes(sizesJson);
}

function adjustQty(amount) {
    quantity += amount;
    if (quantity < 1) quantity = 1;
    document.getElementById("qtyNumber").textContent = quantity;
}

function addProductToCart() {
    if (!window.Cart) return;
    const cartProduct = {
        ...productJson,
        selectedSize: selectedSize,
        selectedColor: selectedColorName
    };
    window.Cart.addToCart(cartProduct, quantity);
}

function buyProductNow() {
    if (!window.Cart) return;
    const cartProduct = {
        ...productJson,
        selectedSize: selectedSize,
        selectedColor: selectedColorName
    };
    window.Cart.addToCart(cartProduct, quantity);
    window.location.href = "cart.php";
}
</script>



<?php require_once __DIR__ . '/includes/footer.php'; ?>

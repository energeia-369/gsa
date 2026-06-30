<?php
$pageTitle = "GLOBAL SPORTS ARENA | Admin Panel";
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>

<link rel="stylesheet" href="assets/css/Admin.css">

<div class="admin-page flex items-center justify-center min-h-screen px-4">
  <div class="admin-box">
    <h1>Admin Panel</h1>
    <p>Add new products to GLOBAL SPORTS ARENA store</p>

    <form id="adminProductForm" class="admin-form">
      <input
        type="text"
        id="productName"
        name="name"
        placeholder="Product Name"
        required
      />

      <input
        type="number"
        id="productPrice"
        name="price"
        placeholder="Price"
        required
      />

      <input
        type="number"
        id="productOriginalPrice"
        name="originalPrice"
        placeholder="Original Price"
        required
      />

      <select
        id="productCategory"
        name="category"
        required
      >
        <option value="">Select Category</option>
        <option value="Footwear">Footwear</option>
        <option value="Apparel">Apparel</option>
        <option value="Equipment">Equipment</option>
        <option value="Accessories">Accessories</option>
        <option value="Fitness">Fitness</option>
        <option value="Protective Gear">Protective Gear</option>
      </select>

      <input
        type="text"
        id="productImage"
        name="image"
        placeholder="Image URL"
        required
      />

      <input
        type="number"
        step="0.1"
        id="productRating"
        name="rating"
        placeholder="Rating"
        required
      />

      <label class="stock-check">
        <input
          type="checkbox"
          id="productInStock"
          name="inStock"
          checked
        />
        In Stock
      </label>

      <button type="submit">Add Product</button>
    </form>
  </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const token = localStorage.getItem("token");
    const role = localStorage.getItem("userRole");

    if (!token || role !== "ADMIN") {
        alert("Access Denied: Admin login required!");
        window.location.href = "login.php";
        return;
    }

    const form = document.getElementById("adminProductForm");
    form.addEventListener("submit", async function(e) {
        e.preventDefault();

        const name = document.getElementById("productName").value;
        const price = parseFloat(document.getElementById("productPrice").value);
        const originalPrice = parseFloat(document.getElementById("productOriginalPrice").value);
        const category = document.getElementById("productCategory").value;
        const imageUrl = document.getElementById("productImage").value;
        const rating = parseFloat(document.getElementById("productRating").value);
        const inStock = document.getElementById("productInStock").checked;

        const payload = {
            name: name,
            category: category,
            price: price,
            originalPrice: originalPrice,
            imageUrl: imageUrl,
            rating: rating,
            stock: inStock ? 10 : 0
        };

        try {
            const res = await fetch("api/index.php/products", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "Authorization": "Bearer " + token
                },
                body: JSON.stringify(payload)
            });

            const data = await res.json();

            // Fallback for localStorage to keep visual equivalence
            const defaultProducts = [
                { id: 1, name: "Sports Shoes", price: 1999, originalPrice: 2999, category: "Footwear", image: "https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=400&h=400&fit=crop", rating: 4.5, inStock: true },
                { id: 2, name: "Running Shoes", price: 2499, originalPrice: 3999, category: "Footwear", image: "https://images.unsplash.com/photo-1608231387042-66d1773070a5?w=400&h=400&fit=crop", rating: 4.7, inStock: true },
                { id: 3, name: "Football Cleats", price: 2999, originalPrice: 4999, category: "Footwear", image: "https://images.unsplash.com/photo-1579952363873-27f3bade9f55?w=400&h=400&fit=crop", rating: 4.6, inStock: true },
                { id: 4, name: "Basketball Shoes", price: 3499, originalPrice: 5499, category: "Footwear", image: "https://images.unsplash.com/photo-1600185365483-26d7a4cc7519?w=400&h=400&fit=crop", rating: 4.8, inStock: true },
                { id: 7, name: "Football Jersey", price: 799, originalPrice: 1299, category: "Apparel", image: "https://images.unsplash.com/photo-1517466787929-bc90951d0974?w=400&h=400&fit=crop", rating: 4.3, inStock: true },
                { id: 8, name: "Cricket Jersey", price: 899, originalPrice: 1499, category: "Apparel", image: "https://images.unsplash.com/photo-1531415074968-036ba1b575da?w=400&h=400&fit=crop", rating: 4.5, inStock: true },
                { id: 15, name: "Badminton Racket", price: 1499, originalPrice: 2499, category: "Equipment", image: "https://images.unsplash.com/photo-1626224583764-f87db24ac4ea?w=400&h=400&fit=crop", rating: 4.7, inStock: true },
                { id: 16, name: "Football", price: 999, originalPrice: 1599, category: "Equipment", image: "https://images.unsplash.com/photo-1577460551100-5ba8d6f05a0e?w=400&h=400&fit=crop", rating: 4.4, inStock: true },
                { id: 27, name: "Water Bottle", price: 299, originalPrice: 499, category: "Accessories", image: "https://images.unsplash.com/photo-1602143407151-7111542de6e8?w=400&h=400&fit=crop", rating: 4.2, inStock: true },
                { id: 28, name: "Gym Gloves", price: 499, originalPrice: 899, category: "Accessories", image: "https://images.unsplash.com/photo-1599058918147-1b2b1c6e7e4a?w=400&h=400&fit=crop", rating: 4.6, inStock: true }
            ];

            const existingProducts = JSON.parse(localStorage.getItem("products")) || defaultProducts;
            const newProduct = {
                id: data.id || Date.now(),
                name: name,
                price: price,
                originalPrice: originalPrice,
                category: category,
                image: imageUrl,
                rating: rating,
                inStock: inStock
            };
            existingProducts.push(newProduct);
            localStorage.setItem("products", JSON.stringify(existingProducts));

            alert("Product added successfully!");
            form.reset();
            document.getElementById("productInStock").checked = true;

        } catch (err) {
            console.error("Failed to add product", err);
            alert("Error adding product.");
        }
    });
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

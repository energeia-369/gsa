import { useState, useEffect } from "react";
import { useNavigate } from "react-router-dom";
import { defaultProducts } from "../data/productsData";
import "../styles/Admin.css";

function Admin() {
  const navigate = useNavigate();

  useEffect(() => {
    const token = localStorage.getItem("token");
    const role = localStorage.getItem("userRole");

    if (!token || role !== "ADMIN") {
      alert("Access Denied: Admin login required!");
      navigate("/login");
    }
  }, [navigate]);
  const [product, setProduct] = useState({
    name: "",
    price: "",
    originalPrice: "",
    category: "",
    image: "",
    rating: "",
    inStock: true,
  });

  const handleChange = (e) => {
    const { name, value, type, checked } = e.target;

    setProduct({
      ...product,
      [name]: type === "checkbox" ? checked : value,
    });
  };

  const handleSubmit = (e) => {
    e.preventDefault();

    const existingProducts =
      JSON.parse(localStorage.getItem("products")) || defaultProducts;

    const newProduct = {
      id: Date.now(),
      name: product.name,
      price: Number(product.price),
      originalPrice: Number(product.originalPrice),
      category: product.category,
      image: product.image,
      rating: Number(product.rating),
      inStock: product.inStock,
    };

    const updatedProducts = [...existingProducts, newProduct];

    localStorage.setItem("products", JSON.stringify(updatedProducts));

    alert("Product added successfully!");

    setProduct({
      name: "",
      price: "",
      originalPrice: "",
      category: "",
      image: "",
      rating: "",
      inStock: true,
    });
  };

  return (
    <div className="admin-page">
      <div className="admin-box">
        <h1>Admin Panel</h1>
        <p>Add new products to GLOBAL SPORTS ARENA store</p>

        <form onSubmit={handleSubmit} className="admin-form">
          <input
            type="text"
            name="name"
            placeholder="Product Name"
            value={product.name}
            onChange={handleChange}
            required
          />

          <input
            type="number"
            name="price"
            placeholder="Price"
            value={product.price}
            onChange={handleChange}
            required
          />

          <input
            type="number"
            name="originalPrice"
            placeholder="Original Price"
            value={product.originalPrice}
            onChange={handleChange}
            required
          />

          <select
            name="category"
            value={product.category}
            onChange={handleChange}
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
            name="image"
            placeholder="Image URL"
            value={product.image}
            onChange={handleChange}
            required
          />

          <input
            type="number"
            step="0.1"
            name="rating"
            placeholder="Rating"
            value={product.rating}
            onChange={handleChange}
            required
          />

          <label className="stock-check">
            <input
              type="checkbox"
              name="inStock"
              checked={product.inStock}
              onChange={handleChange}
            />
            In Stock
          </label>

          <button type="submit">Add Product</button>
        </form>
      </div>
    </div>
  );
}

export default Admin;
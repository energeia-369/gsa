import API from "./api";

export const getAllProducts = async () => {
  return await API.get("/products");
};

export const getProductById = async (id) => {
  return await API.get(`/products/${id}`);
};

export const addProduct = async (productData) => {
  return await API.post("/admin/products", productData);
};

export const updateProduct = async (id, productData) => {
  return await API.put(`/admin/products/${id}`, productData);
};

export const deleteProduct = async (id) => {
  return await API.delete(`/admin/products/${id}`);
};
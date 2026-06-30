import API from "./api";

export const placeOrder = async (orderData) => {
  return await API.post("/orders/place", orderData);
};

export const getMyOrders = async () => {
  return await API.get("/orders/my-orders");
};

export const getOrderById = async (id) => {
  return await API.get(`/orders/${id}`);
};
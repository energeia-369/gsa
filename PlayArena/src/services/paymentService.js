import API from "./api";

export const createPaymentOrder = async (paymentData) => {
  return await API.post("/payment/create-order", paymentData);
};

export const verifyPayment = async (paymentResponse) => {
  return await API.post("/payment/verify", paymentResponse);
};

export const getPaymentStatus = async (id) => {
  return await API.get(`/payment/status/${id}`);
};
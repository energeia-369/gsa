import { Routes, Route } from "react-router-dom";

import ScrollToTop from "./components/ScrollToTop";
import MainLayout from "./layouts/MainLayout";

import Home from "./pages/Home";
import AboutEvent from "./pages/AboutEvent";
import SportsCategories from "./pages/SportsCategories";
import Products from "./pages/Product";
import ProductDetails from "./pages/ProductDetails";
import Cart from "./pages/Cart";
import Checkout from "./pages/Checkout";
import EventRegistration from "./pages/EventRegistration";
import Login from "./pages/Login";
import Register from "./pages/Register";
import UserDashboard from "./pages/UserDashboard";
import AdminDashboard from "./pages/AdminDashboard";
import PaymentSuccess from "./pages/PaymentSuccess";
import PaymentFailed from "./pages/PaymentFailed";
import Admin from "./pages/Admin";
import Wallet from "./pages/Wallet";
import Credits from "./pages/Credits";
import Orders from "./pages/Orders";
import FAQ from "./pages/FAQ";
import ContactUs from "./pages/ContactUs";
import PrivacyPolicy from "./pages/PrivacyPolicy";
import TermsConditions from "./pages/TermsConditions";
import ReturnPolicy from "./pages/ReturnPolicy";
import DestinationDetail from "./pages/DestinationDetail";
import GSADetails from "./pages/GSADetails";

function App() {
  return (
    <>
      <ScrollToTop />
      <Routes>
        <Route path="/" element={<MainLayout />}>
        <Route index element={<Home />} />

        <Route path="about-event" element={<AboutEvent />} />
        
        <Route path="destination/:id" element={<DestinationDetail />} />

        <Route path="gsa-details" element={<GSADetails />} />

        <Route path="sports-categories" element={<SportsCategories />} />

        <Route path="products" element={<Products />} />

        <Route path="products/:id" element={<ProductDetails />} />

        <Route path="cart" element={<Cart />} />

        <Route path="checkout" element={<Checkout />} />

        <Route path="event-registration" element={<EventRegistration />} />

        <Route path="login" element={<Login />} />

        <Route path="register" element={<Register />} />

        <Route path="user-dashboard" element={<UserDashboard />} />

        <Route path="admin-dashboard" element={<AdminDashboard />} />

        <Route path="payment-success" element={<PaymentSuccess />} />

        <Route path="payment-failed" element={<PaymentFailed />} />

        <Route path="admin" element={<Admin />} />

        <Route path="/wallet" element={<Wallet />} />

        <Route path="/credits" element={<Credits />} />
        
        <Route path="/orders" element={<Orders />} />

        <Route path="/faq" element={<FAQ />} />

        <Route path="/contact-us" element={<ContactUs />} />

        <Route path="/privacy-policy" element={<PrivacyPolicy />} />

        <Route path="/terms-conditions" element={<TermsConditions />} />
        
        <Route path="/return-policy" element={<ReturnPolicy />} />

      </Route>
      </Routes>
    </>
  );
}

export default App;
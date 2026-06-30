import { useEffect, useState } from "react";
import { Outlet, useLocation } from "react-router-dom";
import Navbar from "../components/Navbar";
import Footer from "../components/Footer";
import "../styles/MainLayout.css";

function MainLayout() {
  const location = useLocation();
  const [isAdmin, setIsAdmin] = useState(false);

  useEffect(() => {
    const role = localStorage.getItem("userRole");
    setIsAdmin(
      role === "ADMIN" &&
        location.pathname !== "/login" &&
        location.pathname !== "/register"
    );
  }, [location]);

  return (
    <div className={`main-layout ${isAdmin ? "admin-theme" : ""}`}>
      <Navbar />

      <main className="main-content">
        <Outlet />
      </main>

      <Footer />
    </div>
  );
}

export default MainLayout;
import { useEffect } from "react";
import { useLocation } from "react-router-dom";

function ScrollToTop() {
  const { pathname } = useLocation();

  useEffect(() => {
    // Reset window scroll to top of the page on route transitions
    window.scrollTo(0, 0);
  }, [pathname]);

  return null;
}

export default ScrollToTop;

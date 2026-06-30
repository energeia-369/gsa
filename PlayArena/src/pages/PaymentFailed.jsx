import "../styles/PaymentFailed.css";

function PaymentFailed() {
  return (
    <div className="failed-page">
      <div className="failed-box">
        <h1>Payment Failed</h1>
        <p>Please try again or use another payment method.</p>
      </div>
    </div>
  );
}

export default PaymentFailed;
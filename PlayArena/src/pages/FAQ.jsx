
import "../styles/SupportPages.css";

function FAQ() {
  return (
    <div className="support-page">
      <h1>Frequently Asked Questions</h1>

      <div className="support-card">
        <h3>How can I book an event?</h3>
        <p>You can go to the Events page, select your event, and click on Book Now.</p>
      </div>

      <div className="support-card">
        <h3>How can I track my order?</h3>
        <p>You can track your order from the My Orders page.</p>
      </div>

      <div className="support-card">
        <h3>Can I cancel my order?</h3>
        <p>Yes, pending orders can be cancelled from the Orders page.</p>
      </div>
    </div>
  );
}

export default FAQ;
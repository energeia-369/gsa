<?php
$pageTitle = "GLOBAL SPORTS ARENA | My Event Passes & Orders";
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>

<link rel="stylesheet" href="assets/css/Order.css">

<div class="orders-page" style="background: #F9F6F0; color: #3A342B; padding-bottom: 40px; overflow-x: hidden;">
  <div class="orders-hero" style="background: linear-gradient(135deg, #FDFBF7 0%, #F5F0E6 100%);">
    <div class="orders-hero-overlay"></div>
    <div class="orders-hero-content">
      <div class="hero-icon">📋</div>
      <h1 style="color: #B89C62; fontWeight: 800;">
        My Event <span class="highlight" style="color: #B89C62;">Passes & Orders</span>
      </h1>
      <p style="color: #7A7061;">Verify active event booking tickets, print dynamic passes, and review store orders in MySQL</p>
    </div>
  </div>

  <div class="orders-stats" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 px-4 py-5 max-w-7xl mx-auto">
    <div class="stat-card" style="background: #FFFFFF; border: 1px solid rgba(189, 168, 131, 0.3); padding: 20px; border-radius: 12px; display: flex; align-items: center; gap: 15px;">
      <div class="stat-icon" style="font-size: 2rem;">📦</div>
      <div class="stat-info">
        <h3 id="statTotalOrders" style="color: #B89C62; margin: 0; font-size: 1.5rem; font-weight: bold;">0</h3>
        <p style="margin: 0; font-size: 0.85rem; color: #7A7061;">Total Bookings</p>
      </div>
    </div>

    <div class="stat-card" style="background: #FFFFFF; border: 1px solid rgba(189, 168, 131, 0.3); padding: 20px; border-radius: 12px; display: flex; align-items: center; gap: 15px;">
      <div class="stat-icon" style="font-size: 2rem;">✅</div>
      <div class="stat-info">
        <h3 id="statDeliveredOrders" style="color: #B89C62; margin: 0; font-size: 1.5rem; font-weight: bold;">0</h3>
        <p style="margin: 0; font-size: 0.85rem; color: #7A7061;">Delivered / Completed</p>
      </div>
    </div>

    <div class="stat-card" style="background: #FFFFFF; border: 1px solid rgba(189, 168, 131, 0.3); padding: 20px; border-radius: 12px; display: flex; align-items: center; gap: 15px;">
      <div class="stat-icon" style="font-size: 2rem;">⏳</div>
      <div class="stat-info">
        <h3 id="statActivePasses" style="color: #B89C62; margin: 0; font-size: 1.5rem; font-weight: bold;">0</h3>
        <p style="margin: 0; font-size: 0.85rem; color: #7A7061;">Active Passes</p>
      </div>
    </div>

    <div class="stat-card" style="background: #FFFFFF; border: 1px solid rgba(189, 168, 131, 0.3); padding: 20px; border-radius: 12px; display: flex; align-items: center; gap: 15px;">
      <div class="stat-icon" style="font-size: 2rem;">💰</div>
      <div class="stat-info">
        <h3 id="statTotalSpent" style="color: #B89C62; margin: 0; font-size: 1.5rem; font-weight: bold;">₹0</h3>
        <p style="margin: 0; font-size: 0.85rem; color: #7A7061;">Total Spending</p>
      </div>
    </div>
  </div>

  <div class="orders-filter" style="padding: 20px;">
    <div class="filter-tabs" id="filterTabs">
      <!-- Generated dynamically -->
    </div>
  </div>

  <div class="orders-list" id="ordersListContainer" style="padding: 20px;">
    <p style="text-align: center; color: #B89C62;">Syncing orders with server...</p>
  </div>

  <!-- Dynamic QR Event Pass Modal -->
  <div class="modal-overlay" id="qrPassModal" style="display: none; background: rgba(0,0,0,0.4); position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: 999; justify-content: center; align-items: center;">
    <div class="track-modal" style="background: #FFFFFF; border: 1px solid #B89C62; color: #3A342B; max-width: 480px; padding: 20px; border-radius: 16px; width: 90%; box-sizing: border-box; position: relative; box-shadow: 0 10px 40px rgba(138,122,95,0.2);">
      <div class="modal-header" style="border-bottom: 1px solid rgba(189,168,131,0.2); display: flex; justify-content: space-between; align-items: center; padding-bottom: 10px; margin-bottom: 15px;">
        <div style="display: flex; align-items: center; gap: 8px; font-weight: bold; color: #B89C62; font-size: 1.2rem;">
          <span>🎫</span>
          <span>Dynamic QR Entry Ticket</span>
        </div>
        <button class="modal-close" onclick="closeQrModal()" style="background: none; border: none; color: #7A7061; font-size: 1.5rem; cursor: pointer;">×</button>
      </div>

      <div class="modal-body" style="text-align: center; padding: 20px 0;">
        <p style="font-size: 0.9rem; color: #7A7061; margin-bottom: 15px;">
          Present this scanned pass at the GLOBAL SPORTS ARENA sports complex check-in desk for entry.
        </p>
        
        <div style="background: #F9F6F0; border: 1px dashed rgba(189,168,131,0.4); padding: 20px; border-radius: 16px; display: inline-block; margin: 10px auto;">
          <img 
            id="qrCodeImage"
            src=""
            alt="Entry Ticket QR"
            style="border: 2px solid #B89C62; border-radius: 10px; background: #FFFFFF; padding: 6px; width: 170px; height: 170px;"
          />
          <h4 id="qrCodeOrderRef" style="color: #B89C62; margin: 12px 0 4px 0; font-size: 1.1rem;">#ORD-0</h4>
          <span id="qrCodeTitle" style="font-size: 0.75rem; color: #7A7061; text-transform: uppercase;">Sports Event Pass</span>
        </div>

        <div style="text-align: left; background: #FFFFFF; border: 1px solid rgba(189,168,131,0.2); padding: 12px; border-radius: 8px; margin-top: 15px; font-size: 0.85rem; line-height: 1.5;">
          <div><strong>Registered Email:</strong> <span id="qrEmail">guest@globalsportsarena.com</span></div>
          <div><strong>Phone Number:</strong> <span id="qrPhone">N/A</span></div>
          <div><strong>Registration Date:</strong> <span id="qrDate">N/A</span></div>
        </div>
      </div>

      <div class="modal-footer" style="border-top: 1px solid rgba(189,168,131,0.2); text-align: right; padding-top: 15px;">
        <button class="close-modal-btn" onclick="closeQrModal()" style="background: linear-gradient(135deg, #CBB48A 0%, #A48951 100%); color: #FFFFFF; border: none; padding: 10px 20px; border-radius: 8px; font-weight: bold; cursor: pointer;">
          Close Event Pass
        </button>
      </div>
    </div>
  </div>

  <!-- Track Status Modal -->
  <div class="modal-overlay" id="trackStatusModal" style="display: none; background: rgba(0,0,0,0.4); position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: 999; justify-content: center; align-items: center;">
    <div class="track-modal" style="background: #FFFFFF; border: 1px solid #B89C62; color: #3A342B; max-width: 500px; padding: 20px; border-radius: 16px; width: 90%; box-sizing: border-box; position: relative; box-shadow: 0 10px 40px rgba(138,122,95,0.2);">
      <div class="modal-header" style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid rgba(189,168,131,0.2); padding-bottom: 10px; margin-bottom: 15px;">
        <div style="display: flex; align-items: center; gap: 8px; font-weight: bold; color: #B89C62; font-size: 1.2rem;">
          <span>🚚</span>
          <span id="trackModalHeader">Track Order</span>
        </div>
        <button class="modal-close" onclick="closeTrackModal()" style="background: none; border: none; color: #7A7061; font-size: 1.5rem; cursor: pointer;">×</button>
      </div>

      <div class="modal-body">
        <div class="tracking-info">
          <div class="tracking-id" style="color: #7A7061; font-size: 0.85rem; margin-bottom: 20px;">
            <strong>Courier ID:</strong> <span id="trackCourierId">GLOBAL-SPORTS-ARENA-LOGISTICS-0</span>
          </div>

          <div class="tracking-steps">
            <div class="step active" id="stepConfirmed">
              <div class="step-icon" style="background: #B89C62; color: #FFFFFF; display: flex; justify-content: center; align-items: center; border-radius: 50%; width: 28px; height: 28px; font-weight: bold;">✓</div>
              <div class="step-info" style="margin-left: 15px;">
                <h4 style="color: #B89C62; margin: 0 0 4px 0;">Order Confirmed</h4>
                <p style="color: #7A7061; margin: 0; font-size: 0.8rem;">Your transaction has been verified securely in MySQL</p>
              </div>
            </div>

            <div class="step" id="stepShipped" style="margin-top: 20px; display: flex; align-items: flex-start;">
              <div class="step-icon" id="stepShippedIcon" style="display: flex; justify-content: center; align-items: center; border-radius: 50%; width: 28px; height: 28px; background: #F9F6F0; border: 1px solid rgba(189,168,131,0.3); color: #3A342B;">📦</div>
              <div class="step-info" style="margin-left: 15px;">
                <h4 style="margin: 0 0 4px 0;" id="stepShippedTitle">Order Dispatched</h4>
                <p style="color: #7A7061; margin: 0; font-size: 0.8rem;">Your merchandise package has been handed over to courier</p>
              </div>
            </div>

            <div class="step" id="stepDelivered" style="margin-top: 20px; display: flex; align-items: flex-start;">
              <div class="step-icon" id="stepDeliveredIcon" style="display: flex; justify-content: center; align-items: center; border-radius: 50%; width: 28px; height: 28px; background: #F9F6F0; border: 1px solid rgba(189,168,131,0.3); color: #3A342B;">🎁</div>
              <div class="step-info" style="margin-left: 15px;">
                <h4 style="margin: 0 0 4px 0;" id="stepDeliveredTitle">Completed / Delivered</h4>
                <p style="color: #7A7061; margin: 0; font-size: 0.8rem;">Order delivered successfully or event checked-in</p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="modal-footer" style="text-align: right; padding-top: 15px; margin-top: 15px; border-top: 1px solid rgba(189,168,131,0.2);">
        <button class="close-modal-btn" onclick="closeTrackModal()" style="background: #F9F6F0; border: 1px solid rgba(189,168,131,0.3); color: #3A342B; padding: 8px 16px; border-radius: 8px; cursor: pointer;">
          Close
        </button>
      </div>
    </div>
  </div>
</div>

<script>
const userEmail = localStorage.getItem("userEmail") || "guest@globalsportsarena.com";
let orders = [];
let filterStatus = "all";

document.addEventListener("DOMContentLoaded", function() {
    fetchOrders();
});

async function fetchOrders() {
    const container = document.getElementById("ordersListContainer");
    try {
        const res = await fetch(`api/index.php/orders/my-orders?email=${encodeURIComponent(userEmail)}`);
        const data = await res.json();
        
        orders = data.map(order => {
            let itemsList = [];
            try {
                itemsList = order.items_json ? JSON.parse(order.items_json) : [];
            } catch(e) {}
            
            const isEvent = itemsList.some(i => i.name.toLowerCase().includes("champions") || i.name.toLowerCase().includes("basketball") || i.name.toLowerCase().includes("tennis") || i.name.toLowerCase().includes("tournament"));
            
            return {
                id: order.id,
                status: order.order_status || "confirmed",
                total: order.total_amount || order.totalAmount,
                items: itemsList,
                title: itemsList.length > 0 ? itemsList.map(i => i.name).join(", ") : "Sports Order",
                quantity: itemsList.reduce((acc, curr) => acc + (curr.quantity || 1), 0),
                type: isEvent ? "event" : "product",
                orderDate: order.order_date || new Date().toISOString(),
                shippingAddress: order.shipping_address || "N/A",
                customerPhone: order.customer_phone || "N/A",
                nxlCoinsEarned: order.nxl_coins_earned || 0,
                nxlCoinsUsed: order.nxl_coins_used || 0
            };
        });
        
    } catch(err) {
        console.warn("Failed to fetch orders from API, loading from localstorage...", err);
        const orderKey = `orders_${userEmail}`;
        orders = JSON.parse(localStorage.getItem(orderKey)) || [];
    }

    renderTabs();
    renderStats();
    renderOrders();
}

function renderStats() {
    const totalOrders = orders.length;
    const delivered = orders.filter(o => o.status === "delivered").length;
    const pending = orders.filter(o => o.status === "pending" || o.status === "shipped" || o.status === "confirmed").length;
    const totalSpent = orders.reduce((sum, o) => sum + Number(o.total || 0), 0);

    document.getElementById("statTotalOrders").textContent = totalOrders;
    document.getElementById("statDeliveredOrders").textContent = delivered;
    document.getElementById("statActivePasses").textContent = pending;
    document.getElementById("statTotalSpent").textContent = "₹" + totalSpent.toLocaleString();
}

function renderTabs() {
    const tabsContainer = document.getElementById("filterTabs");
    const statuses = ["all", "confirmed", "shipped", "delivered", "pending"];
    
    tabsContainer.innerHTML = statuses.map(status => {
        const isActive = filterStatus === status;
        const text = status === "all" ? "All Orders" : status.charAt(0).toUpperCase() + status.slice(1);
        
        return `
            <button
              class="filter-tab ${isActive ? 'active' : ''}"
              onclick="setFilterStatus('${status}')"
              style="border: 1px solid rgba(189,168,131,0.3); padding: 8px 16px; border-radius: 20px; font-weight: bold; cursor: pointer; transition: all 0.3s;
                background: ${isActive ? 'linear-gradient(135deg, #CBB48A 0%, #A48951 100%)' : 'transparent'};
                color: ${isActive ? '#FFFFFF' : '#7A7061'};"
            >
              ${text}
            </button>
        `;
    }).join('');
}

function setFilterStatus(status) {
    filterStatus = status;
    renderTabs();
    renderOrders();
}

function renderOrders() {
    const container = document.getElementById("ordersListContainer");
    const filtered = orders.filter(order => filterStatus === "all" || order.status === filterStatus);

    if (filtered.length === 0) {
        container.innerHTML = `
            <div class="empty-orders" style="background: #FFFFFF; border: 1px dashed rgba(189,168,131,0.4); text-align: center; padding: 40px; border-radius: 16px;">
              <div class="empty-icon" style="font-size: 3rem; margin-bottom: 15px;">📭</div>
              <h3>No Active Orders</h3>
              <p style="color: #7A7061;">No transactions or registered passes found for your account.</p>

              <div class="empty-actions" style="margin-top: 20px; display: flex; justify-content: center; gap: 15px;">
                <button onclick="window.location.href='sports-categories.php'" class="empty-btn" style="background: rgba(184,156,98,0.1); border: 1px solid #B89C62; color: #B89C62; padding: 10px 20px; border-radius: 8px; cursor: pointer; font-weight: bold;">
                  🎟️ Book Tournaments
                </button>
                <button onclick="window.location.href='products.php'" class="empty-btn" style="background: linear-gradient(135deg, #CBB48A 0%, #A48951 100%); border: none; color: #FFFFFF; padding: 10px 20px; border-radius: 8px; cursor: pointer; font-weight: bold;">
                  🛒 Shop Products
                </button>
              </div>
            </div>
        `;
        return;
    }

    container.innerHTML = filtered.map(order => {
        const isCancelled = order.status === "cancelled";
        
        // Status Badge details
        let badgeHtml = "";
        if (order.status === "confirmed") {
            badgeHtml = `<span class="status-badge success">✅ Confirmed</span>`;
        } else if (order.status === "shipped") {
            badgeHtml = `<span class="status-badge warning">📦 Shipped</span>`;
        } else if (order.status === "delivered") {
            badgeHtml = `<span class="status-badge success">🎁 Delivered</span>`;
        } else if (order.status === "cancelled") {
            badgeHtml = `<span class="status-badge danger">❌ Cancelled</span>`;
        } else {
            badgeHtml = `<span class="status-badge info">⏳ Pending</span>`;
        }

        return `
            <div class="order-card" style="background: #FFFFFF; border: 1px solid rgba(189,168,131,0.3); box-shadow: 0 8px 25px rgba(138,122,95,0.08); border-radius: 16px; padding: 20px; margin-bottom: 20px;">
              <div class="order-header" style="border-bottom: 1px solid rgba(189,168,131,0.2); padding-bottom: 15px; display: flex; justify-content: space-between; align-items: center;">
                <div class="order-id">
                  <span class="id-label" style="color: #7A7061; font-size: 0.9rem;">Order Ref:</span>
                  <span class="id-value" style="color: #B89C62; font-weight: bold;">#ORD-${order.id}</span>
                </div>
                ${badgeHtml}
              </div>

              <div class="order-body" style="padding: 20px 0; display: flex; align-items: center; gap: 20px; flex-wrap: wrap;">
                <div class="order-image" style="width: 80px; height: 80px; border-radius: 12px; overflow: hidden; display: flex; justify-content: center; align-items: center; background: rgba(189,168,131,0.2); flex-shrink: 0;">
                  ${order.items && order.items.length > 0 && order.items[0].image ? 
                    `<img src="${order.items[0].image}" alt="${order.items[0].name}" style="width: 100%; height: 100%; object-fit: cover;" onerror="this.outerHTML='<span class=\\'product-icon\\' style=\\'font-size: 2.5rem;\\'>👟</span>'"/>` 
                  : 
                    `<span class="product-icon" style="font-size: 2.5rem;">${order.type === 'event' ? '🎫' : '👟'}</span>`
                  }
                </div>

                <div class="order-details" style="flex: 1; min-width: 250px;">
                  <h3 style="color: #3A342B; fontSize: 1.2rem; margin: 0 0 10px 0;">${order.title}</h3>
                  <div class="order-info" style="display: flex; align-items: center; gap: 8px; color: #7A7061; font-size: 0.85rem; margin-top: 4px;">
                    <span class="info-icon">📅</span>
                    <span>Order Date: ${new Date(order.orderDate).toLocaleString()}</span>
                  </div>
                  <div class="order-info" style="display: flex; align-items: center; gap: 8px; color: #7A7061; font-size: 0.85rem; margin-top: 4px;">
                    <span class="info-icon">📍</span>
                    <span>Shipping Address: ${order.shippingAddress}</span>
                  </div>
                  <div class="order-info" style="display: flex; align-items: center; gap: 8px; color: #7A7061; font-size: 0.85rem; margin-top: 4px;">
                    <span class="info-icon">📞</span>
                    <span>Contact Phone: ${order.customerPhone}</span>
                  </div>
                </div>

                <div class="order-price" style="text-align: right; min-width: 150px;">
                  <div class="total-amount" style="color: #B89C62; font-size: 1.4rem; font-weight: 800;">
                    ₹${Number(order.total || 0).toLocaleString()}
                  </div>
                  <div style="color: #22c55e; font-size: 0.8rem; margin-top: 4px;">
                    💎 Earned: ${order.nxlCoinsEarned} NXL
                  </div>
                  ${order.nxlCoinsUsed > 0 ? `
                    <div style="color: #ef4444; font-size: 0.8rem;">
                      💎 Redeemed: ${order.nxlCoinsUsed} NXL
                    </div>
                  ` : ''}
                </div>
              </div>

              <div class="order-footer" style="border-top: 1px solid rgba(189,168,131,0.2); padding-top: 15px; display: flex; gap: 10px; justify-content: flex-end; align-items: center; flex-wrap: wrap;">
                ${!isCancelled ? `
                    <button
                      class="footer-btn track-btn"
                      onclick="handleTrackOrder(${order.id})"
                      style="background: #F9F6F0; border: 1px solid rgba(189,168,131,0.3); color: #3A342B; padding: 8px 16px; border-radius: 6px; cursor: pointer;"
                    >
                      🚚 Track Status
                    </button>

                    ${order.type === "event" ? `
                      <button
                        class="footer-btn track-btn"
                        onclick="handleShowTicket(${order.id})"
                        style="background: rgba(184, 156, 98, 0.1); border: 1px solid #B89C62; color: #B89C62; font-weight: bold; padding: 8px 16px; border-radius: 6px; cursor: pointer;"
                      >
                        🎟️ Print QR Pass
                      </button>
                    ` : ''}

                    ${order.status === "pending" ? `
                      <button
                        class="footer-btn cancel-btn"
                        onclick="handleCancelOrder(${order.id})"
                        style="background: rgba(239, 68, 68, 0.1); border: 1px solid #ef4444; color: #ef4444; padding: 8px 16px; border-radius: 6px; cursor: pointer;"
                      >
                        ❌ Cancel Booking
                      </button>
                    ` : ''}

                    <button
                      class="footer-btn reorder-btn"
                      onclick="handleReorder('${order.type}')"
                      style="background: linear-gradient(135deg, #CBB48A 0%, #A48951 100%); color: #FFFFFF; border: none; font-weight: bold; padding: 8px 16px; border-radius: 6px; cursor: pointer;"
                    >
                      🔄 Reorder
                    </button>
                ` : `
                  <div class="cancelled-info" style="color: #ef4444; font-size: 0.85rem;">
                    ❌ Order Cancelled. Refund processed to customer account.
                  </div>
                `}
              </div>
            </div>
        `;
    }).join('');
}

function handleTrackOrder(orderId) {
    const order = orders.find(o => o.id == orderId);
    if (!order) return;

    document.getElementById("trackModalHeader").textContent = `Track Order - #ORD-${order.id}`;
    document.getElementById("trackCourierId").textContent = `GLOBAL-SPORTS-ARENA-LOGISTICS-${order.id}`;

    // Reset styles
    const stepShipped = document.getElementById("stepShipped");
    const stepDelivered = document.getElementById("stepDelivered");
    const stepShippedIcon = document.getElementById("stepShippedIcon");
    const stepDeliveredIcon = document.getElementById("stepDeliveredIcon");

    stepShipped.className = "step";
    stepDelivered.className = "step";
    stepShippedIcon.style.background = "#F9F6F0";
    stepShippedIcon.style.border = "1px solid rgba(189,168,131,0.3)";
    stepShippedIcon.style.color = "#3A342B";
    stepDeliveredIcon.style.background = "#F9F6F0";
    stepDeliveredIcon.style.border = "1px solid rgba(189,168,131,0.3)";
    stepDeliveredIcon.style.color = "#3A342B";

    if (order.status === "shipped" || order.status === "delivered") {
        stepShipped.className = "step active";
        stepShippedIcon.style.background = "#B89C62";
        stepShippedIcon.style.color = "#FFFFFF";
        stepShippedIcon.style.border = "none";
        stepShippedIcon.textContent = "✓";
    } else {
        stepShippedIcon.textContent = "📦";
    }

    if (order.status === "delivered") {
        stepDelivered.className = "step active";
        stepDeliveredIcon.style.background = "#B89C62";
        stepDeliveredIcon.style.color = "#FFFFFF";
        stepDeliveredIcon.style.border = "none";
        stepDeliveredIcon.textContent = "✓";
    } else {
        stepDeliveredIcon.textContent = "🎁";
    }

    document.getElementById("trackStatusModal").style.display = "flex";
}

function closeTrackModal() {
    document.getElementById("trackStatusModal").style.display = "none";
}

function handleShowTicket(orderId) {
    const order = orders.find(o => o.id == orderId);
    if (!order) return;

    document.getElementById("qrCodeOrderRef").textContent = `#ORD-${order.id}`;
    document.getElementById("qrCodeTitle").textContent = order.title;
    document.getElementById("qrEmail").textContent = userEmail;
    document.getElementById("qrPhone").textContent = order.customerPhone;
    document.getElementById("qrDate").textContent = new Date(order.orderDate).toLocaleDateString();

    const qrData = {
        orderId: order.id,
        address: order.shippingAddress,
        phone: order.customerPhone,
        status: "VALID_ENTRY_PASS",
        platform: "GLOBAL_SPORTS_ARENA"
    };

    document.getElementById("qrCodeImage").src = `https://api.qrserver.com/v1/create-qr-code/?size=170x170&color=B89C62&bgcolor=FFFFFF&data=${encodeURIComponent(JSON.stringify(qrData))}`;
    
    document.getElementById("qrPassModal").style.display = "flex";
}

function closeQrModal() {
    document.getElementById("qrPassModal").style.display = "none";
}

async function handleCancelOrder(orderId) {
    if (!window.confirm("Are you sure you want to cancel this order?")) return;

    try {
        const token = localStorage.getItem("token");
        const res = await fetch(`api/index.php/orders/${orderId}/status`, {
            method: "PUT",
            headers: {
                "Content-Type": "application/json",
                "Authorization": "Bearer " + token
            },
            body: JSON.stringify({ status: "cancelled" })
        });
        
        alert("Order cancelled successfully.");
        fetchOrders();
    } catch(err) {
        console.error(err);
        alert("Failed to cancel order via API.");
    }
}

function handleReorder(type) {
    if (type === "event") {
        window.location.href = "event-registration.php";
    } else {
        window.location.href = "products.php";
    }
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

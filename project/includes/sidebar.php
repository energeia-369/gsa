<?php
// Sidebar layout helper
// Determines menu options based on current user role
$sidebarRole = $_SESSION['userRole'] ?? 'USER';
?>
<div class="dashboard-sidebar">
    <div class="sidebar-brand">
        <h3>Menu Panel</h3>
    </div>
    <ul class="sidebar-menu-list">
        <?php if ($sidebarRole === 'ADMIN'): ?>
            <li><a href="admin-dashboard.php" class="sidebar-item">📊 Admin Overview</a></li>
            <li><a href="admin-events.php" class="sidebar-item">🌐 Manage Events</a></li>
            <li><a href="admin-dashboard.php#products-section" class="sidebar-item">📦 Manage Products</a></li>
            <li><a href="admin-dashboard.php#tournaments-section" class="sidebar-item">🏆 Manage Tournaments</a></li>
            <li><a href="admin-dashboard.php#registrations-section" class="sidebar-item">📝 Event Registrations</a></li>
            <li><a href="admin-dashboard.php#orders-section" class="sidebar-item">🛒 View Orders</a></li>
            <li><a href="admin-dashboard.php#users-section" class="sidebar-item">👥 Manage Users</a></li>
        <?php else: ?>
            <li><a href="user-dashboard.php" class="sidebar-item">👤 My Profile</a></li>
            <li><a href="wallet.php" class="sidebar-item">🪙 NXL Wallet</a></li>
            <li><a href="credits.php" class="sidebar-item">💳 Credits System</a></li>
            <li><a href="orders.php" class="sidebar-item">📦 My Orders</a></li>
            <li><a href="event-registration.php" class="sidebar-item">⚽ Register Event</a></li>
        <?php endif; ?>
        <li><a href="#" onclick="handleLogout(); return false;" class="sidebar-item logout-item">🚪 Logout</a></li>
    </ul>
</div>

<style>
.dashboard-sidebar {
    background: rgba(22, 24, 38, 0.95);
    border-right: 1px solid rgba(197, 168, 92, 0.2);
    min-width: 250px;
    padding: 2rem 1.5rem;
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}
.sidebar-brand h3 {
    color: #c5a85c;
    font-size: 1.2rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 1px;
}
.sidebar-menu-list {
    list-style: none;
    display: flex;
    flex-direction: column;
    gap: 0.8rem;
}
.sidebar-item {
    display: block;
    padding: 0.8rem 1rem;
    border-radius: 8px;
    color: #f5f6fa;
    font-weight: 500;
    transition: all 0.3s ease;
}
.sidebar-item:hover {
    background: rgba(197, 168, 92, 0.1);
    color: #c5a85c;
    transform: translateX(5px);
}
.logout-item {
    color: #ff6b6b;
}
.logout-item:hover {
    background: rgba(255, 107, 107, 0.1);
    color: #ff6b6b;
}
</style>

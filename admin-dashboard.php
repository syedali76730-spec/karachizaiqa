<?php
require_once 'config.php';
requireLogin();

$adminName = $_SESSION['admin_name'] ?? 'Admin';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Karachi Zaiqa</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --burgundy: #8B1A1A;
            --forest-green: #2C5F2D;
            --warm-brown: #654321;
            --mustard: #D4A574;
            --cream: #F5E6D3;
            --text-dark: #2A1810;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background: #f5f5f5;
            color: var(--text-dark);
        }
        
        /* Header */
        .header {
            background: linear-gradient(135deg, var(--burgundy) 0%, #6B1414 100%);
            color: white;
            padding: 1.5rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        
        .logo {
            font-family: 'Playfair Display', serif;
            font-size: 1.75rem;
            font-weight: 700;
        }
        
        .user-info {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        
        .logout-btn {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            border: 1px solid rgba(255, 255, 255, 0.3);
            padding: 0.5rem 1rem;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .logout-btn:hover {
            background: rgba(255, 255, 255, 0.3);
        }
        
        /* Navigation Tabs */
        .nav-tabs {
            background: white;
            padding: 0 2rem;
            display: flex;
            gap: 1rem;
            border-bottom: 2px solid #e0e0e0;
        }
        
        .nav-tab {
            padding: 1rem 1.5rem;
            cursor: pointer;
            border-bottom: 3px solid transparent;
            font-weight: 600;
            color: #666;
            transition: all 0.3s;
        }
        
        .nav-tab:hover {
            color: var(--burgundy);
        }
        
        .nav-tab.active {
            color: var(--burgundy);
            border-bottom-color: var(--burgundy);
        }
        
        /* Container */
        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 2rem;
        }
        
        /* Stats Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .stat-card {
            background: white;
            padding: 1.5rem;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            border-left: 4px solid var(--burgundy);
        }
        
        .stat-card.green {
            border-left-color: var(--forest-green);
        }
        
        .stat-card.brown {
            border-left-color: var(--warm-brown);
        }
        
        .stat-card.mustard {
            border-left-color: var(--mustard);
        }
        
        .stat-label {
            color: #666;
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
        }
        
        .stat-value {
            font-size: 2rem;
            font-weight: 700;
            color: var(--text-dark);
        }
        
        /* Content Card */
        .content-card {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            margin-bottom: 1.5rem;
        }
        
        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid #f0f0f0;
        }
        
        .card-title {
            font-family: 'Playfair Display', serif;
            font-size: 1.5rem;
            color: var(--burgundy);
        }
        
        /* Filters */
        .filters {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
            margin-bottom: 1.5rem;
        }
        
        .filter-group {
            display: flex;
            flex-direction: column;
            gap: 0.25rem;
        }
        
        .filter-group label {
            font-size: 0.85rem;
            color: #666;
        }
        
        select, input[type="date"] {
            padding: 0.5rem;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-family: 'Poppins', sans-serif;
        }
        
        /* Button */
        .btn {
            padding: 0.625rem 1.25rem;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            font-family: 'Poppins', sans-serif;
        }
        
        .btn-primary {
            background: var(--burgundy);
            color: white;
        }
        
        .btn-primary:hover {
            background: #6B1414;
        }
        
        .btn-success {
            background: var(--forest-green);
            color: white;
        }
        
        .btn-secondary {
            background: #666;
            color: white;
        }

        .btn-danger {
            background: #dc3545;
            color: white;
        }

        .btn-danger:hover {
            background: #c82333;
        }

        /* Notification Toast */
        .notification-container {
            position: fixed;
            top: 1.5rem;
            right: 1.5rem;
            z-index: 9999;
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .notification {
            padding: 0.875rem 1.25rem;
            border-radius: 8px;
            color: white;
            font-weight: 500;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            max-width: 320px;
            animation: notifSlideIn 0.3s ease, notifFadeOut 0.4s ease 2.6s forwards;
        }

        .notification.success { background: #28a745; }
        .notification.error   { background: #dc3545; }

        @keyframes notifSlideIn {
            from { transform: translateX(110%); opacity: 0; }
            to   { transform: translateX(0);    opacity: 1; }
        }

        @keyframes notifFadeOut {
            from { opacity: 1; }
            to   { opacity: 0; pointer-events: none; }
        }

        /* Order row delete fade */
        @keyframes rowFadeOut {
            from { opacity: 1; }
            to   { opacity: 0; }
        }

        .row-deleting td {
            animation: rowFadeOut 0.4s ease forwards;
        }
        
        .btn-sm {
            padding: 0.4rem 0.8rem;
            font-size: 0.85rem;
        }
        
        /* Table */
        .table-responsive {
            overflow-x: auto;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        th {
            background: var(--cream);
            padding: 1rem;
            text-align: left;
            font-weight: 600;
            color: var(--text-dark);
            border-bottom: 2px solid var(--burgundy);
        }
        
        td {
            padding: 1rem;
            border-bottom: 1px solid #e0e0e0;
        }
        
        tr:hover {
            background: #fafafa;
        }
        
        /* Status Badge */
        .status-badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
        }
        
        .status-received {
            background: #e3f2fd;
            color: #1565c0;
        }
        
        .status-preparing {
            background: #fff3e0;
            color: #e65100;
        }
        
        .status-ready {
            background: #f3e5f5;
            color: #6a1b9a;
        }
        
        .status-picked_up {
            background: #e8f5e9;
            color: #2e7d32;
        }
        
        /* Tab Content */
        .tab-content {
            display: none;
        }
        
        .tab-content.active {
            display: block;
        }
        
        /* Form Grid */
        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1rem;
        }
        
        .form-group {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }
        
        .form-group label {
            font-weight: 600;
            color: var(--text-dark);
        }
        
        .form-group input {
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-family: 'Poppins', sans-serif;
        }
        
        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 3rem;
            color: #999;
        }
        
        /* Modal */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }
        
        .modal.active {
            display: flex;
        }
        
        .modal-content {
            background: white;
            border-radius: 12px;
            padding: 2rem;
            max-width: 600px;
            width: 90%;
            max-height: 90vh;
            overflow-y: auto;
        }
        
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid #f0f0f0;
        }
        
        .modal-title {
            font-family: 'Playfair Display', serif;
            font-size: 1.5rem;
            color: var(--burgundy);
        }
        
        .close-btn {
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: #999;
        }
        
        /* Toggle Switch */
        .toggle-container {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-top: 0.5rem;
        }

        .toggle-switch {
            position: relative;
            display: inline-block;
            width: 50px;
            height: 26px;
            flex-shrink: 0;
        }

        .toggle-switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .toggle-slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            border-radius: 26px;
            transition: 0.3s;
        }

        .toggle-slider::before {
            content: '';
            position: absolute;
            height: 18px;
            width: 18px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            border-radius: 50%;
            transition: 0.3s;
        }

        .toggle-switch input:checked + .toggle-slider {
            background-color: var(--forest-green);
        }

        .toggle-switch input:checked + .toggle-slider::before {
            transform: translateX(24px);
        }

        .toggle-label {
            font-size: 0.85rem;
            font-weight: 600;
            color: #666;
        }

        /* Inventory Section */
        .inventory-section {
            margin-top: 2rem;
            padding-top: 1.5rem;
            border-top: 2px solid #f0f0f0;
        }

        .inventory-item {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 0.875rem 1rem;
            background: #fafafa;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            margin-bottom: 0.75rem;
        }

        .inventory-item-name {
            flex: 1;
            font-weight: 600;
            color: var(--text-dark);
        }

        .inventory-current {
            font-size: 0.875rem;
            color: #666;
            white-space: nowrap;
        }

        .qty-input {
            width: 80px;
            padding: 0.5rem;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-family: 'Poppins', sans-serif;
            text-align: center;
            font-size: 1rem;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .header {
                flex-direction: column;
                gap: 1rem;
                text-align: center;
            }
            
            .stats-grid {
                grid-template-columns: 1fr;
            }
            
            .filters {
                flex-direction: column;
            }
            
            .nav-tabs {
                overflow-x: auto;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <div class="logo">Karachi Zaiqa Admin</div>
        <div class="user-info">
            <span>👋 <?php echo htmlspecialchars($adminName); ?></span>
            <button class="logout-btn" onclick="logout()">Logout</button>
        </div>
    </div>
    
    <!-- Navigation Tabs -->
    <div class="nav-tabs">
        <div class="nav-tab active" onclick="switchTab('dashboard')">Dashboard</div>
        <div class="nav-tab" onclick="switchTab('orders')">Orders</div>
        <div class="nav-tab" onclick="switchTab('pricing')">Pricing</div>
        <div class="nav-tab" onclick="switchTab('cashflow')">Cash Flow</div>
        <div class="nav-tab" onclick="switchTab('whatsapp')">💬 WhatsApp</div>
    </div>
    
    <div class="container">
        <!-- Dashboard Tab -->
        <div id="dashboard" class="tab-content active">
            <!-- Stats Cards -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-label">Total Orders</div>
                    <div class="stat-value" id="totalOrders">0</div>
                </div>
                
                <div class="stat-card green">
                    <div class="stat-label">Today's Orders</div>
                    <div class="stat-value" id="todayOrders">0</div>
                </div>
                
                <div class="stat-card brown">
                    <div class="stat-label">Pending Pickup</div>
                    <div class="stat-value" id="pendingOrders">0</div>
                </div>
                
                <div class="stat-card mustard">
                    <div class="stat-label">Total Revenue</div>
                    <div class="stat-value" id="totalRevenue">A$0</div>
                </div>
            </div>
            
            <!-- Recent Orders -->
            <div class="content-card">
                <div class="card-header">
                    <h2 class="card-title">Recent Orders</h2>
                </div>
                <div class="table-responsive" id="recentOrdersTable">
                    <!-- Loaded via JS -->
                </div>
            </div>
        </div>
        
        <!-- Orders Tab -->
        <div id="orders" class="tab-content">
            <div class="content-card">
                <div class="card-header">
                    <h2 class="card-title">All Orders <span id="refreshIndicator" style="font-size: 0.8rem; color: #787878; font-weight: 400;"></span></h2>
                    <button class="btn btn-secondary" onclick="loadOrders(); loadDashboard();" style="margin-right: 0.5rem;">
                        🔄 Refresh Now
                    </button>
                    <button class="btn btn-primary" onclick="exportOrders()">📥 Export CSV</button>
                </div>
                
                <!-- Filters -->
                <div class="filters">
                    <div class="filter-group">
                        <label>Status</label>
                        <select id="statusFilter" onchange="loadOrders()">
                            <option value="">All Statuses</option>
                            <option value="received">Received</option>
                            <option value="preparing">Preparing</option>
                            <option value="ready">Ready</option>
                            <option value="picked_up">Picked Up</option>
                        </select>
                    </div>
                    
                    <div class="filter-group">
                        <label>From Date</label>
                        <input type="date" id="dateFrom" onchange="loadOrders()">
                    </div>
                    
                    <div class="filter-group">
                        <label>To Date</label>
                        <input type="date" id="dateTo" onchange="loadOrders()">
                    </div>
                    
                    <div class="filter-group">
                        <label>&nbsp;</label>
                        <button class="btn btn-secondary" onclick="clearFilters()">Clear Filters</button>
                    </div>
                </div>
                
                <div class="table-responsive" id="ordersTable">
                    <!-- Loaded via JS -->
                </div>
            </div>
        </div>
        
        <!-- Pricing Tab -->
        <div id="pricing" class="tab-content">
            <div class="content-card">
                <div class="card-header">
                    <h2 class="card-title">Manage Pricing</h2>
                </div>
                
                <h3 style="margin-bottom: 1rem; color: var(--warm-brown);">Main Products</h3>
                <div class="form-grid" id="productsGrid">
                    <!-- Loaded via JS -->
                </div>

                <div class="inventory-section">
                    <h3 style="margin-bottom: 0.5rem; color: var(--warm-brown);">📦 Inventory Management</h3>
                    <p style="color: #666; font-size: 0.875rem; margin-bottom: 1rem;">Set available quantity per product. Customers cannot order when quantity reaches 0.</p>
                    <div id="inventoryGrid">
                        <!-- Loaded via JS -->
                    </div>
                </div>

                <h3 style="margin: 2rem 0 1rem; color: var(--warm-brown);">Add-ons</h3>
                <div class="form-grid" id="addonsGrid">
                    <!-- Loaded via JS -->
                </div>

                <button class="btn btn-success" onclick="savePricing()" style="margin-top: 2rem;">💾 Save All Prices</button>
            </div>
        </div>
        
        <!-- Cash Flow Tab -->
        <div id="cashflow" class="tab-content">
            <div class="content-card">
                <div class="card-header">
                    <h2 class="card-title">Cash Flow Tracking</h2>
                </div>
                
                <div class="filters">
                    <div class="filter-group">
                        <label>Period</label>
                        <select id="cashflowPeriod" onchange="loadCashFlow()">
                            <option value="today">Today</option>
                            <option value="week">This Week</option>
                            <option value="month" selected>This Month</option>
                            <option value="all">All Time</option>
                        </select>
                    </div>
                </div>
                
                <div class="stats-grid">
                    <div class="stat-card green">
                        <div class="stat-label">Total Revenue</div>
                        <div class="stat-value" id="cashflowRevenue">A$0</div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-label">Number of Orders</div>
                        <div class="stat-value" id="cashflowOrders">0</div>
                    </div>
                    
                    <div class="stat-card brown">
                        <div class="stat-label">Average Order Value</div>
                        <div class="stat-value" id="cashflowAverage">A$0</div>
                    </div>
                </div>
                
                <div class="table-responsive" id="cashflowTable">
                    <!-- Loaded via JS -->
                </div>
            </div>
        </div>

        <!-- WhatsApp Tab -->
        <div id="whatsapp" class="tab-content">
            <div style="max-width:700px;margin:40px auto;padding:20px;text-align:center;">
                <div style="background:linear-gradient(135deg,#25D366 0%,#128C7E 100%);padding:50px 40px;border-radius:20px;color:white;box-shadow:0 10px 40px rgba(37,211,102,0.3);margin-bottom:30px;">
                    <div style="font-size:72px;margin-bottom:16px;">💬</div>
                    <h2 style="margin:0 0 12px;font-size:28px;">WhatsApp Web</h2>
                    <p style="margin:0 0 30px;opacity:0.9;font-size:16px;">Open WhatsApp Web to reply to customer messages</p>
                    <a href="https://web.whatsapp.com/"
                       target="_blank"
                       style="display:inline-block;background:white;color:#25D366;padding:16px 40px;border-radius:12px;text-decoration:none;font-weight:700;font-size:18px;transition:all 0.3s ease;"
                       onmouseover="this.style.transform='translateY(-3px)';this.style.boxShadow='0 8px 25px rgba(0,0,0,0.2)'"
                       onmouseout="this.style.transform='';this.style.boxShadow=''">
                        🚀 Open WhatsApp Web
                    </a>
                    <div style="margin-top:30px;background:rgba(255,255,255,0.2);padding:14px 20px;border-radius:10px;display:inline-flex;align-items:center;gap:14px;flex-wrap:wrap;justify-content:center;">
                        <span style="opacity:0.9;">Business Number:</span>
                        <strong style="font-size:18px;letter-spacing:1px;">+61 449 693 094</strong>
                        <button onclick="copyWhatsAppPhone()"
                                style="background:white;color:#25D366;border:none;padding:8px 14px;border-radius:6px;cursor:pointer;font-weight:600;transition:transform 0.2s ease;"
                                onmouseover="this.style.transform='scale(1.05)'"
                                onmouseout="this.style.transform=''">📋 Copy</button>
                    </div>
                </div>
                <div style="background:rgba(255,255,255,0.05);padding:25px;border-radius:12px;border-left:4px solid #25D366;text-align:left;">
                    <h3 style="color:#D4A574;margin-top:0;">💡 Tips</h3>
                    <ul style="list-style:none;padding:0;margin:0;">
                        <li style="padding:8px 0;border-bottom:1px solid rgba(255,255,255,0.1);">✅ Respond within 5 minutes for best customer satisfaction</li>
                        <li style="padding:8px 0;border-bottom:1px solid rgba(255,255,255,0.1);">✅ Send order confirmations immediately after receiving orders</li>
                        <li style="padding:8px 0;border-bottom:1px solid rgba(255,255,255,0.1);">✅ Keep WhatsApp Web open in a separate browser window</li>
                        <li style="padding:8px 0;">✅ Enable desktop notifications for instant alerts</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal" id="deleteModal">
        <div class="modal-content" style="max-width: 420px;">
            <div class="modal-header">
                <h2 class="modal-title" style="color: #dc3545;">🗑️ Delete Order</h2>
                <button class="close-btn" onclick="closeDeleteModal()">&times;</button>
            </div>
            <p id="deleteConfirmMessage" style="margin-bottom: 1.5rem; line-height: 1.6;"></p>
            <div style="display: flex; gap: 1rem; justify-content: flex-end;">
                <button class="btn btn-secondary" onclick="closeDeleteModal()">Cancel</button>
                <button class="btn btn-danger" onclick="confirmDelete()">🗑️ Delete</button>
            </div>
        </div>
    </div>

    <!-- Order Details Modal -->
    <div class="modal" id="orderModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title">Order Details</h2>
                <button class="close-btn" onclick="closeModal()">&times;</button>
            </div>
            <div id="orderDetails">
                <!-- Loaded via JS -->
            </div>
        </div>
    </div>

    <script>
        // State
        let currentOrders = [];
        let lastOrderCount = 0;
        
        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            loadDashboard();
            loadOrders();
            loadPricing();
            startAutoRefresh(); // Start auto-refresh
        });
        
        // Tab Switching
        function switchTab(tabName) {
            // Update tab buttons
            document.querySelectorAll('.nav-tab').forEach(tab => {
                tab.classList.remove('active');
            });
            event.target.classList.add('active');
            
            // Update content
            document.querySelectorAll('.tab-content').forEach(content => {
                content.classList.remove('active');
            });
            document.getElementById(tabName).classList.add('active');
            
            // Load data
            if (tabName === 'dashboard') loadDashboard();
            if (tabName === 'orders') loadOrders();
            if (tabName === 'pricing') loadPricing();
            if (tabName === 'cashflow') loadCashFlow();
        }
        
        // Load Dashboard
        async function loadDashboard() {
            try {
                const response = await fetch('admin-api.php?action=dashboard');
                const result = await response.json();
                
                if (result.success) {
                    document.getElementById('totalOrders').textContent = result.data.totalOrders;
                    document.getElementById('todayOrders').textContent = result.data.todayOrders;
                    document.getElementById('pendingOrders').textContent = result.data.pendingOrders;
                    document.getElementById('totalRevenue').textContent = 'A$' + result.data.totalRevenue.toFixed(2);
                    
                    renderOrdersTable(result.data.recentOrders, 'recentOrdersTable');
                }
            } catch (error) {
                console.error('Failed to load dashboard:', error);
            }
        }
        
        // Load Orders
        async function loadOrders() {
            const indicator = document.getElementById('refreshIndicator');
            if (indicator) {
                indicator.textContent = '🔄 Refreshing...';
                indicator.style.color = '#D4A574';
            }

            const status = document.getElementById('statusFilter')?.value || '';
            const dateFrom = document.getElementById('dateFrom')?.value || '';
            const dateTo = document.getElementById('dateTo')?.value || '';

            try {
                const params = new URLSearchParams({
                    action: 'orders',
                    status,
                    dateFrom,
                    dateTo
                });

                const response = await fetch(`admin-api.php?${params}`);
                const result = await response.json();

                if (result.success) {
                    if (result.data.length > lastOrderCount && lastOrderCount > 0) {
                        // New order detected
                        console.log('[New Order] Detected!');

                        // Optional: Play notification sound
                        try {
                            const audio = new Audio('data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQoGAACBhYqFbF1fdJivrJBhNjVgodDbq2EcBj+a2/LDciUFLIHO8tiJNwgZaLvt559NEAxQp+PwtmMcBjiR1/LMeSwFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBSuBzvLZiTYIG2m98OScTgwOUKfj8LJoHAU2jdXyz3kqBSF1xe/glEILElyx6OyrWBELRp/g8r1rIAUsgs/y2Yg2BRxrvOzu');
                            audio.play();
                        } catch (e) {
                            // Ignore if sound fails
                        }
                    }
                    lastOrderCount = result.data.length;

                    currentOrders = result.data;
                    renderOrdersTable(result.data, 'ordersTable');

                    if (indicator) {
                        const now = new Date();
                        indicator.textContent = `(Updated: ${now.toLocaleTimeString()})`;
                        indicator.style.color = '#787878';
                    }
                }
            } catch (error) {
                console.error('Failed to load orders:', error);
            }
        }

        // Auto-refresh timer
        let autoRefreshInterval = null;

        // Start auto-refresh
        function startAutoRefresh() {
            // Refresh every 30 seconds
            autoRefreshInterval = setInterval(function() {
                console.log('[Auto-Refresh] Reloading orders...');

                // Only refresh if we're on Orders or Dashboard tab
                const activeTab = document.querySelector('.tab-content.active');
                if (activeTab && (activeTab.id === 'orders' || activeTab.id === 'dashboard')) {
                    if (activeTab.id === 'orders') {
                        loadOrders();
                    } else {
                        loadDashboard();
                    }
                }
            }, 30000); // 30 seconds

            console.log('[Auto-Refresh] Started (every 30 seconds)');
        }

        // Stop auto-refresh
        function stopAutoRefresh() {
            if (autoRefreshInterval) {
                clearInterval(autoRefreshInterval);
                autoRefreshInterval = null;
                console.log('[Auto-Refresh] Stopped');
            }
        }
        
        // Render Orders Table
        function renderOrdersTable(orders, containerId) {
            const container = document.getElementById(containerId);
            
            if (orders.length === 0) {
                container.innerHTML = '<div class="empty-state">No orders found</div>';
                return;
            }
            
            let html = `
                <table>
                    <thead>
                        <tr>
                            <th>Order #</th>
                            <th>Customer</th>
                            <th>Items</th>
                            <th>Pickup</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
            `;
            
            const deletableStatuses = ['picked_up', 'cancelled'];
            orders.forEach(order => {
                html += `
                    <tr id="order-row-${order.id}">
                        <td><strong>${order.order_number}</strong></td>
                        <td>${order.customer_name}<br><small>${order.customer_phone || order.customer_email || ''}</small></td>
                        <td>${order.items_summary}</td>
                        <td>${order.pickup_date} ${order.pickup_time}</td>
                        <td><strong>A$${parseFloat(order.total_amount).toFixed(2)}</strong></td>
                        <td><span class="status-badge status-${order.status}">${order.status.replace('_', ' ')}</span></td>
                        <td>
                            <button class="btn btn-sm btn-primary" onclick="viewOrder(${order.id})">View</button>
                            ${order.status !== 'picked_up' ? `<button class="btn btn-sm btn-success" onclick="updateStatus(${order.id}, '${getNextStatus(order.status)}')">Next</button>` : ''}
                            ${deletableStatuses.includes(order.status) ? `<button class="btn btn-sm btn-danger" onclick="deleteOrder(${order.id}, '${order.order_number}')">🗑️ Delete</button>` : ''}
                        </td>
                    </tr>
                `;
            });
            
            html += '</tbody></table>';
            container.innerHTML = html;
        }
        
        // Get Next Status
        function getNextStatus(currentStatus) {
            const statusFlow = {
                'received': 'preparing',
                'preparing': 'ready',
                'ready': 'picked_up'
            };
            return statusFlow[currentStatus] || currentStatus;
        }
        
        // Update Order Status
        async function updateStatus(orderId, newStatus) {
            if (!confirm(`Update order status to "${newStatus.replace('_', ' ')}"?`)) return;
            
            console.log('Updating status:', { orderId, newStatus });
            
            try {
                const response = await fetch('admin-api.php', {
                    method: 'POST',
                    headers: { 
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        action: 'updateStatus',
                        orderId: orderId,
                        status: newStatus
                    })
                });
                
                console.log('Response status:', response.status);
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                const result = await response.json();
                console.log('Response data:', result);
                
                if (result.success) {
                    alert('Status updated successfully!');
                    loadDashboard();
                    loadOrders();
                } else {
                    alert('Failed to update status: ' + (result.message || 'Unknown error'));
                }
            } catch (error) {
                console.error('Update status error:', error);
                alert('Failed to update status: ' + error.message);
            }
        }
        
        // View Order Details
        async function viewOrder(orderId) {
            try {
                const response = await fetch(`admin-api.php?action=orderDetails&orderId=${orderId}`);
                const result = await response.json();
                
                if (result.success) {
                    const order = result.data;
                    
                    let html = `
                        <div style="line-height: 1.8;">
                            <p><strong>Order Number:</strong> ${order.order_number}</p>
                            <p><strong>Customer:</strong> ${order.customer_name}</p>
                            <p><strong>Phone:</strong> ${order.customer_phone || 'Not provided'}</p>
                            <p><strong>Email:</strong> ${order.customer_email || 'Not provided'}</p>
                            <p><strong>Pickup Date:</strong> ${order.pickup_date}</p>
                            <p><strong>Pickup Time:</strong> ${order.pickup_time}</p>
                            <p><strong>Status:</strong> <span class="status-badge status-${order.status}">${order.status.replace('_', ' ')}</span></p>
                            
                            <hr style="margin: 1.5rem 0;">
                            
                            <h3 style="color: var(--burgundy); margin-bottom: 1rem;">Order Items</h3>
                            <p>${order.items_detail}</p>
                            
                            ${order.special_instructions ? `
                                <hr style="margin: 1.5rem 0;">
                                <h3 style="color: var(--burgundy); margin-bottom: 0.5rem;">Special Instructions</h3>
                                <p>${order.special_instructions}</p>
                            ` : ''}
                            
                            <hr style="margin: 1.5rem 0;">
                            
                            <p><strong>Subtotal:</strong> A$${parseFloat(order.subtotal).toFixed(2)}</p>
                            <p style="font-size: 1.25rem;"><strong>Total:</strong> A$${parseFloat(order.total_amount).toFixed(2)}</p>
                            
                            <hr style="margin: 1.5rem 0;">
                            
                            <p><small>Order placed: ${order.created_at}</small></p>
                        </div>
                    `;
                    
                    document.getElementById('orderDetails').innerHTML = html;
                    document.getElementById('orderModal').classList.add('active');
                }
            } catch (error) {
                alert('Failed to load order details');
            }
        }
        
        // Close Modal
        function closeModal() {
            document.getElementById('orderModal').classList.remove('active');
        }
        
        // Load Pricing (FIX 3 & 4)
        async function loadPricing() {
            try {
                const response = await fetch('admin-api.php?action=pricing');
                const result = await response.json();

                if (result.success) {
                    renderProductPricing(result.data.products, 'productsGrid');
                    renderInventory(result.data.products, 'inventoryGrid');
                    renderAddonPricing(result.data.addons, 'addonsGrid');
                }
            } catch (error) {
                console.error('Failed to load pricing:', error);
            }
        }

        // Render product price inputs
        function renderProductPricing(items, containerId) {
            const container = document.getElementById(containerId);
            let html = '';
            items.forEach(item => {
                html += `
                    <div class="form-group">
                        <label>${item.name}</label>
                        <input type="number"
                               step="0.01"
                               value="${item.price}"
                               data-id="${item.id}"
                               data-type="product"
                               class="price-input">
                    </div>
                `;
            });
            container.innerHTML = html;
        }

        // Render addon price inputs with ON/OFF toggle (FIX 3)
        function renderAddonPricing(items, containerId) {
            const container = document.getElementById(containerId);
            let html = '';
            items.forEach(item => {
                const isActive = item.is_active == 1 || item.active == 1 || item.is_active === true || item.active === true;
                html += `
                    <div class="form-group">
                        <label>${item.name}</label>
                        <input type="number"
                               step="0.01"
                               value="${item.price}"
                               data-id="${item.id}"
                               data-type="addon"
                               class="price-input">
                        <div class="toggle-container">
                            <label class="toggle-switch">
                                <input type="checkbox"
                                       id="addon-toggle-${item.id}"
                                       ${isActive ? 'checked' : ''}
                                       onchange="toggleAddon(${item.id}, this.checked)">
                                <span class="toggle-slider"></span>
                            </label>
                            <span class="toggle-label" id="addon-toggle-label-${item.id}">${isActive ? 'ON' : 'OFF'}</span>
                        </div>
                    </div>
                `;
            });
            container.innerHTML = html;
        }

        // Render inventory quantity inputs (FIX 4)
        function renderInventory(products, containerId) {
            const container = document.getElementById(containerId);
            if (!container) return;
            let html = '';
            products.forEach(product => {
                const qty = product.quantity !== undefined && product.quantity !== null ? product.quantity : 0;
                html += `
                    <div class="inventory-item">
                        <span class="inventory-item-name">${product.name}</span>
                        <span class="inventory-current">Current: ${qty}</span>
                        <input type="number"
                               class="qty-input"
                               id="inventory-${product.id}"
                               value="${qty}"
                               min="0"
                               placeholder="Qty">
                        <button class="btn btn-sm btn-primary" onclick="updateInventory(${product.id})">Update</button>
                    </div>
                `;
            });
            container.innerHTML = html || '<p style="color:#999;">No products found</p>';
        }

        // Toggle addon ON/OFF (FIX 3)
        async function toggleAddon(addonId, isActive) {
            console.log('[Admin] Toggling addon:', addonId, 'isActive:', isActive);
            const checkbox = document.getElementById(`addon-toggle-${addonId}`);
            const labelEl = document.getElementById(`addon-toggle-label-${addonId}`);

            try {
                const payload = {
                    action: 'toggleAddon',
                    addonId: parseInt(addonId),
                    isActive: isActive ? 1 : 0  // Changed from 'active' to 'isActive'
                };

                console.log('[Admin] Sending payload:', payload);

                const response = await fetch('admin-api.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload)
                });

                const result = await response.json();
                console.log('[Admin] Toggle result:', result);

                if (result.success) {
                    if (labelEl) {
                        labelEl.textContent = isActive ? 'ON' : 'OFF';
                        labelEl.style.color = isActive ? '#2C5F2D' : '#8B1A1A';
                    }
                    alert('Addon ' + (isActive ? 'enabled' : 'disabled') + ' successfully!');
                } else {
                    alert('Failed to toggle addon: ' + (result.message || 'Unknown error'));
                    if (checkbox) checkbox.checked = !isActive;
                    if (labelEl) labelEl.textContent = isActive ? 'OFF' : 'ON';
                }
            } catch (error) {
                console.error('[Admin] Toggle addon error:', error);
                alert('Failed to toggle addon: ' + error.message);
                if (checkbox) checkbox.checked = !isActive;
                if (labelEl) labelEl.textContent = isActive ? 'OFF' : 'ON';
            }
        }

        // Update product inventory quantity (FIX 4)
        async function updateInventory(productId) {
            const input = document.getElementById(`inventory-${productId}`);
            const quantity = parseInt(input.value);
            if (isNaN(quantity) || quantity < 0) {
                alert('Please enter a valid quantity (0 or more)');
                return;
            }
            console.log('[Admin] Updating inventory for product:', productId, 'qty:', quantity);
            try {
                const response = await fetch('admin-api.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ action: 'updateInventory', productId: productId, quantity: quantity })
                });
                const result = await response.json();
                console.log('[Admin] Inventory update result:', result);
                if (result.success) {
                    alert('Inventory updated successfully!');
                    loadPricing();
                } else {
                    alert('Failed to update inventory: ' + (result.message || 'Unknown error'));
                }
            } catch (error) {
                console.error('[Admin] Update inventory error:', error);
                alert('Failed to update inventory');
            }
        }
        
        // Save Pricing
        async function savePricing() {
            const prices = [];
            
            document.querySelectorAll('.price-input').forEach(input => {
                prices.push({
                    id: input.dataset.id,
                    type: input.dataset.type,
                    price: parseFloat(input.value)
                });
            });
            
            try {
                const response = await fetch('admin-api.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        action: 'updatePricing',
                        prices
                    })
                });
                
                const result = await response.json();
                
                if (result.success) {
                    alert('Prices updated successfully!');
                } else {
                    alert('Failed to update prices: ' + result.message);
                }
            } catch (error) {
                alert('Failed to update prices');
            }
        }
        
        // Load Cash Flow
        async function loadCashFlow() {
            const period = document.getElementById('cashflowPeriod').value;
            
            try {
                const response = await fetch(`admin-api.php?action=cashflow&period=${period}`);
                const result = await response.json();
                
                if (result.success) {
                    document.getElementById('cashflowRevenue').textContent = 'A$' + result.data.totalRevenue.toFixed(2);
                    document.getElementById('cashflowOrders').textContent = result.data.totalOrders;
                    document.getElementById('cashflowAverage').textContent = 'A$' + result.data.averageOrder.toFixed(2);
                    
                    renderCashFlowTable(result.data.orders);
                }
            } catch (error) {
                console.error('Failed to load cash flow:', error);
            }
        }
        
        // Render Cash Flow Table
        function renderCashFlowTable(orders) {
            const container = document.getElementById('cashflowTable');
            
            if (orders.length === 0) {
                container.innerHTML = '<div class="empty-state">No transactions found</div>';
                return;
            }
            
            let html = `
                <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Order #</th>
                            <th>Customer</th>
                            <th>Amount</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
            `;
            
            orders.forEach(order => {
                html += `
                    <tr>
                        <td>${order.created_at}</td>
                        <td>${order.order_number}</td>
                        <td>${order.customer_name}</td>
                        <td><strong>A$${parseFloat(order.total_amount).toFixed(2)}</strong></td>
                        <td><span class="status-badge status-${order.status}">${order.status.replace('_', ' ')}</span></td>
                    </tr>
                `;
            });
            
            html += '</tbody></table>';
            container.innerHTML = html;
        }
        
        // Export Orders
        async function exportOrders() {
            window.location.href = 'admin-api.php?action=export';
        }
        
        // Clear Filters
        function clearFilters() {
            document.getElementById('statusFilter').value = '';
            document.getElementById('dateFrom').value = '';
            document.getElementById('dateTo').value = '';
            loadOrders();
        }
        
        // Logout
        function logout() {
            if (confirm('Are you sure you want to logout?')) {
                stopAutoRefresh(); // Stop refresh before logout
                window.location.href = 'admin-logout.php';
            }
        }

        // WhatsApp
        function copyWhatsAppPhone() {
            navigator.clipboard.writeText('+61449693094').then(() => {
                showNotification('Phone number copied: +61449693094', 'success');
            });
        }

        // Delete Order
        let pendingDeleteId = null;
        let pendingDeleteNumber = null;

        function deleteOrder(orderId, orderNumber) {
            pendingDeleteId = orderId;
            pendingDeleteNumber = orderNumber;
            document.getElementById('deleteConfirmMessage').textContent =
                `Are you sure you want to permanently delete order #${orderNumber}? This action cannot be undone.`;
            document.getElementById('deleteModal').classList.add('active');
        }

        function closeDeleteModal() {
            document.getElementById('deleteModal').classList.remove('active');
            pendingDeleteId = null;
            pendingDeleteNumber = null;
        }

        async function confirmDelete() {
            if (!pendingDeleteId) return;

            const orderId = pendingDeleteId;
            const orderNumber = pendingDeleteNumber;
            closeDeleteModal();

            try {
                const response = await fetch('admin-api.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ action: 'deleteOrder', orderId: orderId })
                });
                const result = await response.json();

                if (result.success) {
                    const row = document.getElementById(`order-row-${orderId}`);
                    if (row) {
                        row.classList.add('row-deleting');
                        setTimeout(() => row.remove(), 450);
                    }
                    showNotification(`Order #${orderNumber} deleted successfully`, 'success');
                    loadDashboard();
                } else {
                    showNotification(result.message || 'Failed to delete order', 'error');
                }
            } catch (error) {
                showNotification('Failed to delete order: ' + error.message, 'error');
            }
        }

        function showNotification(message, type) {
            const container = document.getElementById('notificationContainer');
            const el = document.createElement('div');
            el.className = `notification ${type}`;
            el.textContent = message;
            container.appendChild(el);
            setTimeout(() => el.remove(), 3000);
        }
    </script>

    <div class="notification-container" id="notificationContainer"></div>
</body>
</html>

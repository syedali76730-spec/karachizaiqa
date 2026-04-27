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
                    <h2 class="card-title">All Orders</h2>
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
        
        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            loadDashboard();
            loadOrders();
            loadPricing();
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
                    currentOrders = result.data;
                    renderOrdersTable(result.data, 'ordersTable');
                }
            } catch (error) {
                console.error('Failed to load orders:', error);
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
            
            orders.forEach(order => {
                html += `
                    <tr>
                        <td><strong>${order.order_number}</strong></td>
                        <td>${order.customer_name}<br><small>${order.customer_phone || order.customer_email || ''}</small></td>
                        <td>${order.items_summary}</td>
                        <td>${order.pickup_date} ${order.pickup_time}</td>
                        <td><strong>A$${parseFloat(order.total_amount).toFixed(2)}</strong></td>
                        <td><span class="status-badge status-${order.status}">${order.status.replace('_', ' ')}</span></td>
                        <td>
                            <button class="btn btn-sm btn-primary" onclick="viewOrder(${order.id})">View</button>
                            ${order.status !== 'picked_up' ? `<button class="btn btn-sm btn-success" onclick="updateStatus(${order.id}, '${getNextStatus(order.status)}')">Next</button>` : ''}
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
        
        // Load Pricing
        async function loadPricing() {
            try {
                const response = await fetch('admin-api.php?action=pricing');
                const result = await response.json();
                
                if (result.success) {
                    renderPricingForm(result.data.products, 'productsGrid');
                    renderPricingForm(result.data.addons, 'addonsGrid');
                }
            } catch (error) {
                console.error('Failed to load pricing:', error);
            }
        }
        
        // Render Pricing Form
        function renderPricingForm(items, containerId) {
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
                               data-type="${containerId === 'productsGrid' ? 'product' : 'addon'}"
                               class="price-input">
                    </div>
                `;
            });
            
            container.innerHTML = html;
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
                window.location.href = 'admin-logout.php';
            }
        }
    </script>
</body>
</html>

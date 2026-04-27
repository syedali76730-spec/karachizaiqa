# Karachi Zaiqa - Food Ordering System
Complete ordering system for Karachi Zaiqa authentic Haleem business in Sydney.

## 🎯 Features

### Customer Features
- Beautiful mobile-first ordering interface
- Single & Double plate Haleem selection
- Add-ons (Roti, Adrak, Extra Spice, Fried Onions, Beverage)
- Pickup time scheduling (2:00 PM - 7:30 PM)
- Email notifications (optional)
- Order confirmation with order number

### Admin Features
- Secure admin panel with email-based login
- Dashboard with key metrics
- Order management (view, update status)
- Order status tracking (Received → Preparing → Ready → Picked Up)
- Price management for products and add-ons
- Cash flow tracking with period filters
- Order export to CSV/Excel
- Order history with date filters

## 📦 Installation

### Step 1: Database Setup

1. Import the database schema:
```bash
mysql -u root -p < database.sql
```

OR manually create the database using phpMyAdmin or MySQL Workbench:
- Create database: `karachi_zaiqa`
- Import `database.sql` file

2. Default admin login credentials:
   - Email: `syed.ali.76730@gmail.com`
   - Password: `admin123`

**IMPORTANT:** Change the password immediately after first login!

To change password, run this in MySQL:
```sql
USE karachi_zaiqa;
UPDATE admin_users 
SET password_hash = '$2y$10$YOUR_NEW_PASSWORD_HASH_HERE' 
WHERE email = 'syed.ali.76730@gmail.com';
```

Generate password hash using PHP:
```php
echo password_hash('your_new_password', PASSWORD_DEFAULT);
```

### Step 2: Configure Database Connection

Edit `config.php` and update these lines:
```php
define('DB_HOST', 'localhost');      // Your database host
define('DB_USER', 'your_db_user');   // Your database username
define('DB_PASS', 'your_db_pass');   // Your database password
define('DB_NAME', 'karachi_zaiqa');  // Database name
```

### Step 3: Email Notifications Setup (EmailJS)

This system uses EmailJS for sending email notifications. Follow these steps:

#### 3.1 Create EmailJS Account
1. Go to https://www.emailjs.com/
2. Sign up for a free account
3. Verify your email

#### 3.2 Add Email Service
1. In EmailJS dashboard, go to "Email Services"
2. Click "Add New Service"
3. Choose your email provider (Gmail recommended)
4. Connect your email account (`syed.ali.76730@gmail.com`)
5. Note down the **Service ID**

#### 3.3 Create Email Templates

**Template 1: Customer Order Confirmation**
1. Go to "Email Templates" → "Create New Template"
2. Template Name: `Customer Order Confirmation`
3. Template Content:
```
Subject: Order Confirmed - {{order_number}} - Karachi Zaiqa

Hi {{customer_name}},

Thank you for your order! Your authentic Karachi Haleem is being prepared.

ORDER DETAILS:
Order Number: {{order_number}}
Product: {{product}}
Add-ons: {{addons}}
Total: {{total}}

PICKUP INFORMATION:
Date: {{pickup_date}}
Time: {{pickup_time}}

Payment: Cash on Pickup or PayID

See you soon!

Karachi Zaiqa
Sydney's Authentic Haleem
```
4. Note down the **Template ID**

**Template 2: Owner Notification**
1. Create another template
2. Template Name: `Owner Order Notification`
3. Template Content:
```
Subject: New Order - {{order_number}}

NEW ORDER RECEIVED!

Order Number: {{order_number}}

CUSTOMER:
Name: {{customer_name}}
Phone: {{customer_phone}}
Email: {{customer_email}}

ORDER:
Product: {{product}}
Add-ons: {{addons}}
Special Instructions: {{special_instructions}}
Total: {{total}}

PICKUP:
Date: {{pickup_date}}
Time: {{pickup_time}}

Login to admin panel to manage this order.
```
4. Note down this **Template ID** too

#### 3.4 Get Public Key
1. Go to "Account" → "General"
2. Find your **Public Key**

#### 3.5 Update Configuration Files

**In `config.php`:**
```php
define('EMAILJS_SERVICE_ID', 'your_service_id');
define('EMAILJS_TEMPLATE_ID_CUSTOMER', 'your_customer_template_id');
define('EMAILJS_TEMPLATE_ID_OWNER', 'your_owner_template_id');
define('EMAILJS_PUBLIC_KEY', 'your_public_key');
```

**In `index.html`:**
Find these lines and replace with your keys:
```javascript
// Line ~540
emailjs.init('YOUR_EMAILJS_PUBLIC_KEY');  // Replace with your public key

// Line ~653
await emailjs.send('YOUR_SERVICE_ID', 'YOUR_OWNER_TEMPLATE_ID', {
    // Owner email notification
});

// Line ~673
await emailjs.send('YOUR_SERVICE_ID', 'YOUR_CUSTOMER_TEMPLATE_ID', {
    // Customer email notification
});
```

### Step 4: Upload Files to Server

Upload all files to your web server:
```
/public_html/
├── config.php
├── database.sql
├── index.html (Customer ordering page)
├── process-order.php
├── admin-login.php
├── admin-auth.php
├── admin-dashboard.php
├── admin-api.php
└── admin-logout.php
```

### Step 5: Set Permissions

```bash
chmod 644 *.php
chmod 644 *.html
chmod 600 config.php  # Protect configuration file
```

### Step 6: Test the System

1. **Customer Ordering:**
   - Visit: `https://yourdomain.com/`
   - Place a test order
   - Check email notifications

2. **Admin Panel:**
   - Visit: `https://yourdomain.com/admin-login.php`
   - Login with: `syed.ali.76730@gmail.com` / `admin123`
   - **CHANGE PASSWORD IMMEDIATELY**

## 🎨 Brand Colors

The system uses Karachi Zaiqa's brand colors:
- **Burgundy Red:** #8B1A1A
- **Forest Green:** #2C5F2D
- **Warm Brown:** #654321
- **Muted Mustard:** #D4A574
- **Cream:** #F5E6D3

## 💰 Current Pricing

- **Single Plate Haleem:** A$14
- **Double Plate Haleem:** A$22
- **All Add-ons:** A$2 each
  - Roti
  - Adrak (Ginger)
  - Extra Spice
  - Fried Onions
  - Beverage (Pepsi/7UP)

Prices can be updated via Admin Panel → Pricing tab.

## 📱 Mobile Optimization

The system is fully responsive and mobile-first:
- Optimized for smartphone ordering
- Touch-friendly buttons and controls
- Fast loading times
- Works on all screen sizes

## 🔐 Security Features

- SQL injection protection (PDO prepared statements)
- XSS prevention (htmlspecialchars)
- CSRF protection (session-based)
- Password hashing (bcrypt)
- Admin-only access control

## 📊 Admin Panel Sections

### 1. Dashboard
- Total orders count
- Today's orders
- Pending pickups
- Total revenue
- Recent orders list

### 2. Orders
- View all orders
- Filter by status, date range
- Update order status
- View detailed order information

### 3. Pricing
- Update product prices
- Update add-on prices
- Save changes instantly

### 4. Cash Flow
- Revenue tracking
- Order statistics
- Period filters (Today, Week, Month, All Time)
- Transaction history

## 📥 Order Export

Export orders to CSV/Excel format:
1. Go to Admin Panel → Orders
2. Click "Export CSV"
3. File downloads with all order details

## 🔄 Order Status Flow

```
Received → Preparing → Ready → Picked Up
```

Click "Next" button to move order to next status.

## 🚨 Troubleshooting

### Emails Not Sending
1. Check EmailJS configuration in `config.php` and `index.html`
2. Verify EmailJS service is active
3. Check browser console for errors
4. Ensure email templates are correctly set up

### Database Connection Error
1. Check database credentials in `config.php`
2. Ensure MySQL service is running
3. Verify database exists and tables are created

### Can't Login to Admin
1. Check email address is correct: `syed.ali.76730@gmail.com`
2. Try default password: `admin123`
3. Clear browser cookies/cache
4. Check database has admin_users table

### Orders Not Appearing
1. Check database connection
2. View browser console for JavaScript errors
3. Ensure `process-order.php` has correct permissions

## 🔮 Future Enhancements

These features are planned for future updates:

### Phase 2 (WhatsApp Integration)
- Evolution API integration
- WhatsApp notifications to owner
- WhatsApp notifications to customers
- Configuration in admin panel

### Phase 3 (Advanced Features)
- SMS notifications
- Online payment integration (Stripe/PayPal)
- Customer accounts & order history
- Loyalty program
- Promotional codes/discounts
- Multi-language support (English/Urdu)

## 📞 Support

For technical support or questions:
- Email: syed.ali.76730@gmail.com
- Phone: +92 309 7480397

## 📄 License

Proprietary - © 2025 Karachi Zaiqa. All rights reserved.

---

**Built with ❤️ for Karachi Zaiqa - Bringing the authentic taste of Karachi to Sydney**

# 🚀 QUICK START GUIDE - Karachi Zaiqa Ordering System

## ⚡ 5-Minute Setup

### 1️⃣ Upload Files (2 minutes)
Upload ALL files to your web hosting:
- index.html
- config.php
- process-order.php
- admin-login.php
- admin-auth.php
- admin-dashboard.php
- admin-api.php
- admin-logout.php
- database.sql
- .htaccess
- install-check.php

### 2️⃣ Create Database (1 minute)
```sql
1. Login to phpMyAdmin or MySQL
2. Create new database: "karachi_zaiqa"
3. Import database.sql file
4. Done!
```

### 3️⃣ Configure Database (1 minute)
Edit **config.php**:
```php
Line 12: define('DB_HOST', 'localhost');      // Usually 'localhost'
Line 13: define('DB_USER', 'your_username');  // Your MySQL username
Line 14: define('DB_PASS', 'your_password');  // Your MySQL password
Line 15: define('DB_NAME', 'karachi_zaiqa');  // Keep as is
```

### 4️⃣ Setup EmailJS (5 minutes)
See README.md for detailed EmailJS setup, or:

**Quick Steps:**
1. Go to https://emailjs.com → Sign up
2. Add Email Service → Connect Gmail
3. Create 2 templates (customer & owner)
4. Copy Service ID, Template IDs, Public Key
5. Update in config.php and index.html

### 5️⃣ Test Installation
Visit: **your-domain.com/install-check.php**
- This will verify everything is working
- **DELETE this file after setup!**

---

## 🔑 Default Login

**Admin Panel:** your-domain.com/admin-login.php

Email: syed.ali.76730@gmail.com
Password: admin123

**⚠️ CHANGE PASSWORD IMMEDIATELY!**

---

## 📍 Important URLs

- **Customer Ordering:** your-domain.com/
- **Admin Login:** your-domain.com/admin-login.php
- **Installation Check:** your-domain.com/install-check.php

---

## 💡 Quick Tips

1. **Test First:** Place a test order before going live
2. **Change Password:** First thing after logging in
3. **Email Setup:** EmailJS is FREE for 200 emails/month
4. **Backup:** Keep a copy of database.sql safe
5. **Delete install-check.php:** After setup is complete

---

## 🆘 Common Issues

**Can't connect to database?**
→ Check config.php credentials

**Emails not sending?**
→ Configure EmailJS properly (see README.md)

**Can't login to admin?**
→ Email: syed.ali.76730@gmail.com
→ Password: admin123

**Orders not showing?**
→ Check browser console for errors
→ Verify database connection

---

## 📱 Contact Support

Email: syed.ali.76730@gmail.com
Phone: +92 309 7480397

---

## ✅ Post-Setup Checklist

- [ ] Database created and imported
- [ ] config.php updated with DB credentials
- [ ] EmailJS configured
- [ ] Test order placed successfully
- [ ] Admin login working
- [ ] Admin password changed
- [ ] install-check.php deleted
- [ ] Email notifications working
- [ ] Prices verified in admin panel

**You're ready to go! 🎉**

---

Built with ❤️ for Karachi Zaiqa

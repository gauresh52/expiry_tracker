# 🧾 Expiry Tracker System

Expiry Tracker is a lightweight PHP web application that allows **salesmen** to record and manage expired products and enables **admins** to monitor submissions, generate reports, and manage user accounts.

---

## 🚀 Features

### 👤 For Salesmen

* Secure login and session-based authentication.
* Add expired products with fields:

  * Product Name
  * Retailer Name
  * Quantity
  * Expiry Date
  * Category (optional)
  * Remarks (optional)
* Form validation with confirmation modal.
* View your own product submissions.
* Account blocking handled by the admin.
* Forgot password with email reset link.

### 🛠️ For Admin

* Dashboard with key stats:

  * Total expiry submissions
  * Expiring within 30 days
  * Products submitted today
* Filter products by date range and export CSV reports.
* Manage and block/unblock salesmen accounts.
* Session timeout and secure logout handling.
* Prevents browser back navigation after logout.

---

## 🧹 Technologies Used

| Component     | Technology                             |
| ------------- | -------------------------------------- |
| Backend       | PHP (PDO, OOP)                         |
| Frontend      | Bootstrap 5                            |
| Database      | MySQL                                  |
| Email Service | PHPMailer (Gmail SMTP or Mailtrap)     |
| Hosting       | InfinityFree / Any PHP-compatible host |

---

## 🗁️ Folder Structure

```
project_root/
│
├── admin/
│   ├── dashboard.php
│   ├── manage_users.php
│   └── export_csv.php
│
├── auth/
│   ├── login.php
│   ├── logout.php
│   ├── forgot_password.php
│   └── reset_password.php
│
├── salesman/
│   ├── add_product.php
│   └── my_products.php
│
├── includes/
│   ├── header.php
│   ├── footer.php
│   └── phpmailer/
│       ├── PHPMailer.php
│       ├── SMTP.php
│       └── Exception.php
│
├── config.php
├── database.sql
└── README.md
```

---

## ⚙️ Setup Instructions

### 1️⃣ Upload Files

* Upload all project files to your web hosting (e.g., InfinityFree `htdocs/expiry_tracker`).

### 2️⃣ Database Setup

1. Create a new MySQL database (e.g., `if0_xxxxxxx_expiry_tracker`).
2. Import the `database.sql` file using phpMyAdmin.
3. Update database credentials in `config.php`:

   ```php
   $host = "sql202.infinityfree.com";
   $dbname = "if0_xxxxxxx_expiry_tracker";
   $username = "if0_xxxxxxx";
   $password = "YourPasswordHere";
   ```

### 3️⃣ Admin Seed (Default Login)

```
Email: admin@company.com
Password: Admin@123
```

---

## 📬 Email Setup (Password Reset)

### Option 1: Gmail SMTP (Recommended)

1. Enable **2-Step Verification** in your Google Account.
2. Generate an **App Password** for “Mail”.
3. In `forgot_password.php`, update:

   ```php
   $mail->Username = 'yourgmail@gmail.com';
   $mail->Password = 'your-app-password';
   ```

### Option 2: Mailtrap (for Testing)

1. Create a free account at [https://mailtrap.io](https://mailtrap.io).
2. Use your SMTP credentials:

   ```php
   $mail->Host = 'smtp.mailtrap.io';
   $mail->Username = 'your-mailtrap-username';
   $mail->Password = 'your-mailtrap-password';
   $mail->Port = 2525;
   ```

---

## 🔒 Security Features

* Session-based authentication.
* Prevents back navigation after logout.
* Auto logout after 15 minutes of inactivity.
* Prepared statements (PDO) to prevent SQL Injection.
* Passwords hashed using `password_hash()`.
* Secure password reset token (valid for 30 minutes).

---

## 🧰 Admin Functions

| Function         | Description                               |
| ---------------- | ----------------------------------------- |
| Manage Users     | View, block/unblock salesmen              |
| View Stats       | Total, expiring soon, today’s submissions |
| Export CSV       | Filter by Added Date or Expiry Date       |
| Monitor Products | View latest 200 entries                   |

---

## 🤓 Developer Notes

* Tested on **PHP 8.2+**
* Compatible with **InfinityFree**, **000WebHost**, **XAMPP**, or **Localhost**
* To avoid email send errors on free hosting, prefer **Mailtrap** for development.

---

## 📄 License

This project is open-source and available under the **MIT License**.
You can freely use, modify, and distribute it with attribution.

---

## ❤️ Author

**Gauresh Rekdo**
🎓 MCA Student | 💻 Developer | Tester
📧 [gaureshrekdo@gmail.com](mailto:gaureshrekdo@gmail.com)

---

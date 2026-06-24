# AgroDrop 🌱

AgroDrop is a web-based agricultural marketplace that connects farmers directly with consumers. The platform helps farmers sell their products online while allowing customers to purchase fresh agricultural products easily and efficiently.

## 📖 Project Overview

Agriculture plays a vital role in Bangladesh's economy. However, farmers often face difficulties in selling products at fair prices due to intermediaries. AgroDrop aims to solve this problem by creating a digital marketplace where farmers and consumers can interact directly.

## 🎯 Objectives

* Connect farmers directly with consumers.
* Reduce dependency on middlemen.
* Ensure fair pricing.
* Provide fresh agricultural products.
* Create a transparent online marketplace.

## 🚀 Features

### Farmer Module

* Registration & Login
* Add Products
* Update Products
* Delete Products
* Manage Inventory
* View Orders

### Customer Module

* Registration & Login
* Browse Products
* Search Products
* View Product Details
* Place Orders
* Order History

### Admin Module

* Admin Dashboard
* User Management
* Product Monitoring
* Order Management
* Reports & Statistics

## 🛠️ Technologies Used

### Frontend

* HTML5
* CSS3
* Bootstrap 5
* JavaScript

### Backend

* PHP

### Database

* MySQL

### Development Tools

* XAMPP
* Git
* GitHub

## 🗄️ Database Structure

### Users

* id
* name
* email
* password
* role
* status

### Products

* id
* farmer_id
* product_name
* category
* price
* quantity
* image

### Orders

* id
* customer_id
* product_id
* quantity
* total_price
* order_date
* status

## ⚙️ Installation

1. Clone the repository

```bash
git clone https://github.com/your-username/agrodrop.git
```

2. Move to project directory

```bash
cd agrodrop
```

3. Import the database into MySQL.

4. Configure database connection.

5. Start Apache and MySQL using XAMPP.

6. Run the project in your browser.

## 👥 Team Members

### Fayaz Wahid and Md.Shihab Uddin

* Frontend Development
* Farmer Module
* Database Design
* Customer Module
* Admin Module
* Documentation

## 📌 Future Improvements

* Online Payment Gateway
* Mobile Application
* Real-time Order Tracking
* Product Reviews and Ratings
* SMS Notifications

## 📄 License

This project is developed for educational purposes.

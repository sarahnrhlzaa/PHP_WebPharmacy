# PharmaCare – Web Pharmacy Management System

## 🎓 Academic Project Overview
**PharmaCare** is a full-stack web application developed as a comprehensive academic project to simulate the digital operations of a modern pharmacy. This system is designed to facilitate seamless interactions between customers seeking healthcare products and administrators managing inventory, transactions, and supplier relationships.

Built using **Native PHP (8.2)** and **MySQL (8.0)**, and fully containerised with **Docker**, PharmaCare demonstrates the implementation of core web development concepts including session management, database normalisation, and CRUD operations within a responsive user interface.

## 🚀 Key Features

### Customer Interface (Front-End)
* **Product Catalogue:** An intuitive interface for browsing medicinal and wellness products, categorised for easy navigation.
* **Smart Search & Filtering:** Real-time search functionality to locate specific medicines or filter by category (Medicine/Wellness).
* **Shopping Cart & Checkout:** A dynamic shopping cart system with session-based storage, supporting a secure checkout process with multiple payment options (Bank Transfer, COD, E-Wallet).
* **Order Tracking:** Customers can view their order history and track the status of current purchases (Pending, Processing, Completed).
* **Prescription Upload:** A dedicated feature allowing users to upload doctor prescriptions for specific medication requests.
* **Outlet Locator:** Integration with maps to display physical pharmacy outlet locations.
* **User Profile Management:** Users can manage their personal details and shipping addresses.

### Administrator Dashboard (Back-End)
* **Analytical Dashboard:** Visualisation of key metrics including weekly transaction charts, recent activity, and stock summaries.
* **Inventory Management:** Complete CRUD capabilities for medicine stock, including image handling, pricing, and expiration date tracking.
* **Automated Stock Alerts:** Intelligent notifications for low-stock and critically low-stock items to prevent shortages.
* **Transaction Management:** A centralised hub to monitor incoming purchases from suppliers and outgoing orders to customers.
* **Supplier & User Administration:** Modules to manage supplier data and registered customer accounts.

## Technology Stack

* **Back-End:** PHP 8.2 (FPM) via Docker.
* **Front-End:** HTML5, CSS3, Vanilla JavaScript.
* **Database:** MySQL 8.0.
* **Web Server:** Nginx.
* **Containerisation:** Docker & Docker Compose.
* **Database Management:** phpMyAdmin.

## 📂 Project Structure

* `Connection/`: Database connection configuration using environment variables.
* `ViewUser/`: Contains front-end source code (CSS, JS, PHP pages) for the customer interface.
* `ViewAdmin/`: Contains back-end source code for the administrator dashboard.
* `uploads/`: Directory for storing dynamic content such as medicine images.
* `docker-compose.yml`: Service orchestration for Nginx, PHP, MySQL, and phpMyAdmin.

---
*Created for Web Application Project & Project Management Project at CEP CCIT FTUI.*

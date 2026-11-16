# The Gentlemen’s Bar – Web & Mobile Management System

**The Gentlemen’s Bar** is a full-stack, cross-platform appointment booking, retail store, and barbershop management ecosystem developed by **SiboSync Solutions** for module `XISD6329MM`. Built with a **PHP/MySQL** web platform (hosted on InfinityFree) and a native **Kotlin Android** mobile application, both platforms synchronize in real time via **Firebase Authentication** and **Cloud Firestore**.

* **Live Website:** http://gentlemensbarwebsite.infinityfreeapp.com
* **Website GitHub Repository:** https://github.com/sibongilee/GentlemensBarWebsite
* **Mobile App GitHub Repository:** https://github.com/ST10440580/The_Gentlemens_Barber


---

## Problem Statement & Purpose
Traditional barbershop operations rely heavily on manual phone bookings, paper walk-in logs, and disconnected payment tracking. *The Gentlemen’s Bar Management System* solves this by unifying customer self-service, barber scheduling, and administrative analytics into one synchronized ecosystem:

* **Customers:** Browse grooming services, pricing (in South African Rands), and durations before registering, book or reschedule appointments, purchase grooming products, and pay online via **Paystack**.
* **Staff (Barbers):** View daily schedules in real time, log in-store walk-in clients without overlapping online bookings, and mark unavailable days off.
* **Administrators (Managers):** Monitor shop-wide appointments, revenue metrics, staff rosters, service pricing, and product stock.

---

## Key Features & Design Considerations

### 1. Customer Experience (Web & Android App)
* **Role-Based Single Sign-On (SSO):** Secure authentication via Firebase Auth automatically routes users to the Customer, Staff, or Admin dashboard based on their Firestore role.
* **Smart Appointment Booking:** Guided step-by-step booking flow (`Service → Barber → Date & Time → Confirmation`) with client-side and server-side past-date blocking, slot locking (`slotLocks`) to prevent double bookings, and live South African public holiday detection (`api.zanode.co.za`).
* **Product Store & Cart:** Interactive shopping cart for grooming products (Shaving Cream, Beard Oil Kit, Premium Hair Wax, Professional Clippers, Hair Conditioner, Facial Cleanser) with automatic total calculation and checkout.
* **Integrated Paystack Payments:** Supports secure card payments via the Paystack Inline JS SDK (Web) and Paystack Android SDK (Mobile), alongside an in-store payment option.

### 2. Staff & Administrative Operations
* **Staff Dashboard (`staff_dashboard.php` & Mobile Staff View):** Displays daily bookings, expected vs. collected revenue, and an interactive **Walk-In Modal** that records in-store clients directly into Firestore under the logged-in barber.
* **Admin Dashboard (`admin_dashboard.php` & Mobile Admin View):** Full oversight of shop schedules, staff management, services, inventory, and reporting.
* **Security & Session Hardening:** Enforces a 15-minute inactivity timeout (`$_SESSION['last_activity']`), strict HTTP `Cache-Control: no-store, no-cache` headers to block post-logout browser Back-button access, and Firestore role-based security rules.

---

## Tech Stack & Architecture
* **Web Frontend:** HTML5, CSS3 (Black, Beige & White luxury barbershop theme), JavaScript (ES6+)
* **Web Backend & Hosting:** PHP 8.x, cURL API verification, MySQL, InfinityFree Cloud Hosting
* **Mobile Application:** Kotlin, Android Studio, Material Design, SQLite (`barber_cache.db`) for offline caching
* **Cloud Database & Auth:** Firebase Authentication, Firebase Cloud Firestore (`africa-south1`)
* **Third-Party APIs:** Paystack Payment Gateway API, ZANode South African Public Holidays API (`api.zanode.co.za`)
* **DevOps, CI/CD & Testing:** Azure DevOps (7 Agile Sprints), GitHub & GitHub Actions, JUnit (Unit Testing), Espresso (Instrumented UI Testing)

---

## GitHub & GitHub Actions Utilization
* **Version Control Workflow:** Source code is structured cleanly across dedicated repositories with meaningful commit histories from all team members (no compressed `.zip` archives).
* **Automated CI/CD (GitHub Actions):** Configured continuous integration workflows to automatically build the Android Gradle project, validate Kotlin compilation, and execute automated JUnit test suites (`SlotsTest`, `CommonUtilsTest`, `CartLogicTest`) on every push to `main`/`master` before generating release builds.

---

## Release Notes (v2.0 – Final Production Release)
Upgrades implemented since the Formative 2 prototype:

* **[New] Paystack Payment Gateway Integration:** Added Paystack Android SDK (`co.paystack.android:paystack:3.3.2`) in `PaymentActivity.kt` and server-side cURL transaction verification in `process_payment.php`, automatically updating booking `paymentStatus` to `paid` in Firestore.
* **[New] Server-Side Past-Date & Time Validation:** Added PHP server-side timestamp checks (`strtotime($appointment_date . ' ' . $appointment_time) < time()`) in `booking_appointment.php` so historical bookings are rejected even if browser date pickers are bypassed, while preserving user form inputs on validation errors.
* **[New] Session Inactivity Timeout & Anti-Caching Headers:** Implemented a 15-minute idle session timeout and HTTP `no-store, no-cache` headers across `staff_dashboard.php` and `admin_dashboard.php` to prevent unauthorized access on shared shop devices.
* **[New] Staff Walk-In Modal & Role Separation:** Separated managerial oversight (`admin_dashboard.php`) from barber operations (`staff_dashboard.php`), introducing a real-time Walk-In booking modal locked to the signed-in barber with `source: "Walk-In"` tracking.
* **[Fixed] Cross-Platform Firestore Schema Sync:** Aligned `customer_id`, `barber`, and `paymentStatus` field names between PHP and Kotlin so appointments created on the web immediately appear in the Android app and vice versa.

---

## Team Contact Details (SiboSync Solutions)
For inquiries or technical support, please contact our project team:

* **Sibongile Nhlapo** (Project Manager) – `ST10449136@rcconnect.edu.za`
* **Natasha Mvundlela** (Software Designer) – `ST10447508@rcconnect.edu.za`
* **Molebogeng Thupi** (System Analyst) – `ST10349188@rcconnect.edu.za`
* **Tshepang Ramohapi** (Lead Software Developer) – `ST10440580@rcconnect.edu.za`
* **Ronewa Nemubvumoni** (Quality Assurance) – `ST10445772@rcconnect.edu.za`

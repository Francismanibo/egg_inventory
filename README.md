# Egg Production and Inventory Management System

Laravel + MySQL + Blade + Tailwind CSS capstone implementation.

Included: public landing page, product browsing, basic customer inquiry, confirmation, customer order tracking, staff login and roles, dashboard, pending/outgoing/done order management, production recording, inventory transactions and history, low-stock indicators, reports, product management, and user management.

Default accounts:
- admin@egg.local / password
- production@egg.local / password
- inventory@egg.local / password

## Setup in VS Code on Windows/XAMPP

1. Install PHP, Composer, Node.js, and npm. Laravel 12 supports PHP 8.2+, while Laravel 13 requires PHP 8.3+; the composer constraint in this project allows Composer to select a compatible Laravel release. See the official Laravel installation docs.
2. Start Apache and MySQL in XAMPP.
3. Create a MySQL database named `egg_management` in phpMyAdmin.
4. In VS Code, open the project folder.
5. Copy `.env.example` to `.env`.
6. Run `composer install`.
7. Run `php artisan key:generate`.
8. Run `php artisan migrate --seed`.
9. Run `npm install`.
10. Run `npm run dev` in one terminal.
11. Run `php artisan serve` in a second terminal.
12. Open http://127.0.0.1:8000

Customer flow: Home -> Products -> Submit Inquiry -> Confirmation -> My Orders -> Pending/Outgoing/Done.
Staff flow: Login -> Dashboard -> Orders / Production / Inventory / Reports / Products / Users.

Important behavior: production adds the saleable quantity to inventory automatically; accepting a customer order checks stock, records a deduction, and moves the order to Outgoing; Mark Done only changes the status to Done.

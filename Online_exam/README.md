# Online Examination and Result Processing System

A dynamic web-based online examination system built with PHP, MySQL, HTML5, CSS3, and JavaScript (ES6).

## Features
- **Admin Module:** Manage exams, add MCQ questions, and view student performance reports.
- **Student Module:** Register, take timed exams, and receive instant automated results.
- **Secure Authentication:** Role-based access control with secure password hashing.
- **Responsive UI:** Modern, clean, and responsive design for all devices.

## Requirements
- XAMPP / WAMP / MAMP stack (Apache & MySQL)
- PHP 7.4 or higher recommended

## Installation Instructions (for XAMPP)

1. **Clone or Extract the Project:**
   Extract the project folder and place it in your XAMPP `htdocs` directory.
   Usually: `C:\xampp\htdocs\Online_exam`

2. **Database Setup:**
   - Open your XAMPP Control Panel and start **Apache** and **MySQL**.
   - Open your browser and go to `http://localhost/phpmyadmin/`.
   - Go to the **Import** tab.
   - Choose the file `database/schema.sql` from the project folder and click **Import** (or **Go**).
   - This will automatically create the `online_exam_db` database, tables, and insert a default Admin user.

3. **Database Configuration (If necessary):**
   - If your MySQL uses a password or a different username than `root`, open `php/db_connect.php` in a text editor and update the `$username` and `$password` variables accordingly.

4. **Run the Application:**
   - Open your browser and navigate to the project folder:
     `http://localhost/Online_exam/` (or matching your folder name in `htdocs` if different).

## Default Credentials

**Admin Login:**
- **Email:** `admin@example.com`
- **Password:** `admin123`

*(Note: Students can create their own accounts via the Registration page).*

## Project Structure
- `database/`: Contains the SQL schema file.
- `php/`: Contains backend logic and database connection.
- `css/`: Styling files.
- `js/`: Client-side scripts (timer logic, validation).
- `admin/`: Admin module pages.
- `student/`: Student module pages.
- Root directory: Public pages (Login, Registration, Home).

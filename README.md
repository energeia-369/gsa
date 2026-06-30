# ENERGEIA 🚀

**ENERGEIA** is a dynamic, fully-featured Sports Event and E-Commerce Platform built with modern PHP. Designed to manage large-scale sporting events (like the GSA Championship Series), it provides a seamless experience for both attendees and administrators.

## 🌟 Key Features

### 1. Robust Admin Dashboard
- **Event Management**: Create, edit, and manage sporting events dynamically. Control hero banners, logos, event dates, and descriptions directly from the dashboard.
- **Home Event Cards**: Easily manage and link the visual event cards displayed on the homepage to their respective dynamic event pages.
- **Exhibitor & Sponsor Tools**: Add customizable exhibitor pricing packages (e.g., Standard, Premium, Corner, Pavilion) and manage sponsors for each event.
- **Registrations & Passes**: Monitor and manage Team Registrations, Exhibitor Registrations, and Visitor Passes in real-time.
- **Inquiries**: Review and respond to Business Inquiries and general Contact form submissions.

### 2. Dynamic Frontend
- **Database-Driven Pages**: Event details are dynamically generated from the MySQL database using clean PDO connections, ensuring fast and secure data retrieval.
- **Modern UI/UX**: Features rich aesthetics, glassmorphism elements, dynamic micro-animations, and responsive layouts tailored for an engaging user experience.
- **Dark/Light Mode Ready**: Custom CSS structure designed to support modern themes and visual excellence.

## 🛠️ Technology Stack
- **Backend**: PHP (Vanilla, Object-Oriented with PDO)
- **Database**: MySQL (Relational schema for events, cards, settings, and registrations)
- **Frontend**: HTML5, Vanilla CSS3 (Custom Design System), JavaScript
- **Version Control**: Git / GitHub

## 📂 Project Structure Highlights
- `/project/admin-dashboard.php`: The central hub for administrators.
- `/project/event-details.php`: The dynamic template rendering specific event data.
- `/project/config/Database.php`: Secure PDO database connection configuration.
- `/project/assets/css/`: Organized vanilla CSS files enforcing the project's premium design aesthetics.

## 🚀 Getting Started (Local Development)

1. Clone the repository:
   ```bash
   git clone https://github.com/energeia-369/gsa.git
   ```
2. Place the project folder into your local server environment (e.g., `htdocs` for XAMPP).
3. Import the provided SQL database file into your MySQL server to set up the tables (events, home_event_cards, system_settings, etc.).
4. Update the database credentials in `/project/config/Database.php` if necessary.
5. Open the project in your browser (e.g., `http://localhost/Mithraa_E_Project/project/`).

---
*Built with ❤️ to power the next generation of sports event commerce.*

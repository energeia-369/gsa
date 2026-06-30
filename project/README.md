# 🏟️ ENERGEIA-S: Global Sports Arena

A comprehensive, state-of-the-art **Sports Event & E-Commerce Platform** that seamlessly bridges the gap between athletes, merchants, sponsors, and spectators. Designed with a premium aesthetic and scalable backend architecture.

## ✨ Key Features

- **Multi-Role User Ecosystem:** Secure login and distinct dashboards for Users/Athletes, Merchants, and System Administrators.
- **Dynamic Event Ticketing & QR Codes:** Explore global/national tournaments (e.g., GSA Pune 2026). Purchase visitor passes, athlete registrations, or exclusive Gala Dinner passes. Instant QR code ticket generation for seamless event entry.
- **Virtual Currency (NXL Credits):** An integrated loyalty ledger where users earn NXL credits and merchants can issue coins. Credits can be spent seamlessly on registrations, merchandise, and food courts.
- **E-Commerce Store & Gift Cards:** A fully functional digital storefront allowing merchants to list inventory, and users to add items to their cart and checkout securely. Purchase and redeem digital gift cards.
- **Exhibitor & Sponsorship Portal:** Direct booking system for vendors and exhibitors to reserve standard, premium, corner, or pavilion stalls at upcoming global events.
- **AI-Powered Virtual Assistant (E.V.A.):** An interactive chatbot built with the Google Gemini API to instantly answer queries regarding venues, ticketing, and platform rules.
- **Advanced Admin Dashboard:** A central command center for managing user accounts, sports categories, store inventory, registrations, global destinations carousels, and NXL allocations.

## 🎨 Design & Aesthetics

- **Premium UI/UX:** Built with a sophisticated "Beige & Gold" luxury theme.
- **Dynamic Interactions:** Features extensive glassmorphism, micro-animations, and fluid hover effects.
- **Responsive Layout:** 100% mobile, tablet, and desktop friendly using custom CSS flexbox and grid architectures.

## 🛠️ Technology Stack

- **Frontend:** HTML5, Vanilla CSS3, Vanilla JavaScript (No frameworks overhead)
- **Backend:** PHP 8 (Object-Oriented Architecture, PDO for secure database access)
- **Database:** MySQL (Relational Schema with robust foreign key constraints)
- **AI Integration:** Google Gemini 1.5 Flash API
- **Utilities:** phpqrcode (for instant ticketing)

## 🚀 Installation & Setup

1. **Clone the Repository**
   ```bash
   git clone https://github.com/Sankey75/PlayArena-Sports-Event-E-Commerce-Platform.git
   ```

2. **Database Configuration**
   - Create a MySQL database named `playarena_db`.
   - Setup the core database using the provided `check_db.php`, `create_passes_db.php`, and `setup_gift_cards.sql` scripts located in the root.
   - Update your local credentials (host, username, password) inside `config/Config.php` and `config/Database.php`.

3. **API Keys**
   - Head over to Google Cloud Console or Google AI Studio to generate a Gemini API Key.
   - Insert your API key into `api/chatbot_backend.php` replacing the `YOUR_GEMINI_API_KEY_HERE` placeholder.

4. **Run the Project**
   - Host the project directory on a local server stack (like XAMPP, WAMP, or MAMP).
   - Navigate to `http://localhost/Mithraa_E_Project/project` in your browser.

## 📱 Contributing

If you wish to contribute, please fork the repository, create a new branch for your feature, and submit a Pull Request! All UI additions must strictly adhere to the premium Beige/Gold aesthetic and Vanilla CSS principles.

## 📄 License
This project is proprietary and built exclusively for the Mithraa E-Project.

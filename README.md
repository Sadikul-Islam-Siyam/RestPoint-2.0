<p align="center">
  <img src="public/favicon.ico" alt="RestPoint Logo" width="100" />
</p>

<h1 align="center">RestPoint</h1>

<p align="center">
  <strong>The Ultimate Community Hub for Gamers</strong>
</p>

<p align="center">
  RestPoint is a modern, responsive web application built for gaming communities. It serves as a unified tavern where players can gather, discuss boss strategies, share builds, read the latest gaming news, and vote on the best community content. Built on top of the robust Laravel framework with a sleek Tailwind CSS interface.
</p>

---

## 🌟 Features

### 🎮 Game Hubs (Communities)
- **Centralized Game Directories:** Each game has its own dedicated hub.
- **Categorized Discussions:** Browse posts by categories like *Boss Strategy*, *Builds & Loadouts*, *Item Locations*, *Lore & Story*, and more.
- **Tags & Filters:** Filter posts efficiently by category, tag, or sort by newest/popular.
- **Dynamic Data:** Integrates with the RAWG API to dynamically seed real game data, genres, and platforms.

### 📰 Tavern News Hub
- **Live News Aggregator:** Pulls real-time RSS feeds from IGN, GameSpot, and PC Gamer into a beautifully formatted, unified timeline.

### 🔥 Popular Highlights
- **Weekly Trending:** Automatically calculates an activity score based on upvotes and replies to highlight the hottest community discussions of the week.
- **Voting System:** Upvote/Downvote functionality on both posts and comments.

### 🎲 Explore & Discover
- **Tavern Roulette:** Don't know what to play? Let the Tavern spin a random game community for you to explore!
- **Top Adventurers Leaderboard:** Spotlights the most active contributors based on their total thread creations and replies.
- **Follow System:** Join game hubs to have them appear on your personalized dashboard.

### 🛡️ Moderation & User Accounts
- **Profiles:** Customizable user profiles with dynamic statistics and avatars.
- **Roles:** Includes User, Moderator, and Admin roles.
- **Admin Dashboard:** Moderators can review user reports, ban offenders, and manage community integrity.

---

## 🛠️ Technology Stack

- **Backend:** Laravel (PHP)
- **Frontend:** Blade Templates, Tailwind CSS, Alpine.js
- **Database:** MySQL / SQLite
- **Asset Bundling:** Vite

---

## 🚀 Getting Started

Follow these instructions to get a copy of the project up and running on your local machine for development and testing.

### Prerequisites

- PHP >= 8.2
- Composer
- Node.js & npm
- A relational database (MySQL, PostgreSQL, or SQLite)

### Installation

1. **Clone the repository:**
   ```bash
   git clone https://github.com/Sadikul-Islam-Siyam/RestPoint.git
   cd RestPoint
   ```

2. **Install PHP dependencies:**
   ```bash
   composer install
   ```

3. **Install NPM dependencies:**
   ```bash
   npm install
   ```

4. **Environment Setup:**
   Copy the `.env.example` file and configure your environment variables:
   ```bash
   cp .env.example .env
   ```
   *Make sure to update your `DB_DATABASE`, `DB_USERNAME`, and `DB_PASSWORD` variables.*

5. **Generate Application Key:**
   ```bash
   php artisan key:generate
   ```

6. **Run Migrations & Seed the Database:**
   This command will run all database migrations and seed the initial `Grandmaster Admin` account alongside the RAWG game library data.
   ```bash
   php artisan migrate:fresh --seed
   ```

7. **Compile Assets:**
   ```bash
   npm run dev
   ```

8. **Start the Development Server:**
   ```bash
   php artisan serve
   ```

You can now access the application at `http://localhost:8000`.

### Admin Access
The seeder provisions a default admin account:
- **Email:** `admin@questhive.com`
- **Password:** `password`

---

## 📸 Screenshots

*(To add screenshots later, place images in the `/public/images` directory and reference them here!)*

---

## 🛡️ License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

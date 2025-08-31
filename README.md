### Project Description: GitHub Repository Search CMS

The GitHub Repository Search CMS is a lightweight, modern content management system developed in PHP, designed to facilitate advanced searches for GitHub repositories while offering a visually stunning and user-friendly experience. Inspired by Apple's clean and minimalist design principles, the CMS features a sleek interface with a centered, rounded search box, a dynamic abstract network animation that follows the cursor, and a responsive layout optimized for both desktop and mobile devices. It leverages SQLite for efficient data storage and includes a robust admin panel for tracking user searches and managing Google AdSense integration for monetization.

#### Core Features
- **Advanced GitHub Repository Search**:
  - Users can search GitHub repositories using keywords, with filters for programming language, minimum stars, sort options (stars, forks, or last updated), and order (ascending or descending).
  - Results are fetched via the GitHub API, displayed in a clean list with pagination (10 results per page) to handle large result sets. Pagination controls include "Previous" and "Next" buttons, with a clear display of the current page and total pages (capped at 100 due to GitHub API limits).
  - Each result shows the repository's full name, description, star count, fork count, and primary language, with links to the repository's GitHub page.

- **Apple-Inspired Design**:
  - The interface features a white, rounded search container with subtle shadows, a semi-transparent background for readability, and a toggleable dark mode for accessibility.
  - A 160x600 sidebar on the homepage displays Google AdSense ads, styled to blend seamlessly with the minimalist aesthetic.
  - The design is responsive, with the sidebar stacking above the search container on smaller screens (below 768px) for optimal usability.

- **Abstract Network Animation**:
  - A canvas-based animation creates an abstract network of 20 nodes that move gently across the screen, connecting with lines when near each other or the cursor (within 150 pixels).
  - The animation is semi-transparent (opacity: 0.3) and uses colors (`#0071e3` for nodes/lines) that complement the Apple-like theme, ensuring it enhances rather than distracts from the user experience.

- **Admin Panel**:
  - A secure admin panel (protected by basic HTTP authentication; username: `admin`, password: `password123` – recommended to upgrade for production) provides:
    - **Search Analytics**: A table of all user searches (ID, query, timestamp, IP address) and a Chart.js bar chart visualizing the top 10 most frequent search queries.
    - **AdSense Management**: A form to input or update a Google AdSense code (e.g., for a 160x600 skyscraper ad), stored in the SQLite database and displayed in the homepage sidebar.
  - The panel is styled consistently with the homepage, using rounded corners, shadows, and a clean layout.

- **Monetization with Google AdSense**:
  - The homepage includes a fixed 160x600 sidebar for displaying AdSense ads, retrieved from the database.
  - Admins can manage the ad code via the admin panel, with a placeholder message shown if no code is set.
  - The sidebar is optimized for the skyscraper ad format, ensuring proper alignment and visibility.

- **SQLite Database**:
  - Uses SQLite for lightweight storage, with tables for:
    - `searches`: Logs user search queries, timestamps, and IP addresses.
    - `adsense`: Stores the Google AdSense code (single record, ID=1).
  - No external database server is required, making deployment straightforward.

#### Technical Details
- **Tech Stack**: PHP (with cURL and PDO SQLite extensions), HTML, CSS, JavaScript, GitHub API, Chart.js (for admin analytics).
- **Files**:
  - `index.php`: Homepage with search form, results, pagination, sidebar, and abstract network animation.
  - `admin.php`: Admin panel for search analytics and AdSense management.
  - `db.php`: Database connection and functions for search logging and AdSense storage.
  - `github_api.php`: Handles GitHub API requests for repository searches, including pagination.
- **GitHub API**: Uses unauthenticated requests (60 requests/hour limit). For production, a Personal Access Token can be added to increase the limit to 5,000 requests/hour.
- **Deployment**: Runs on any PHP-enabled server (e.g., `php -S localhost:8000` for local testing). SQLite database (`searches.db`) is created automatically.

#### Purpose and Use Cases
The GitHub Repository Search CMS is ideal for developers, tech communities, or content creators who want a dedicated platform to explore GitHub repositories with advanced filtering. It serves as:
- A tool for discovering open-source projects by specific criteria (e.g., language or popularity).
- A monetizable platform through AdSense integration, suitable for tech blogs or community sites.
- A showcase of modern web design, combining functionality with an engaging, Apple-inspired UI.

#### Setup and Usage
1. Place `index.php`, `admin.php`, `db.php`, and `github_api.php` in a directory.
2. Run a PHP server: `php -S localhost:8000`.
3. Access the homepage (`http://localhost:8000/index.php`) to search repositories.
4. Log in to the admin panel (`http://localhost:8000/admin.php`) to view search analytics or set the AdSense code.
5. Optionally, add a GitHub API token in `github_api.php` for higher rate limits and secure the admin panel with proper authentication.

#### Future Enhancements
- Implement OAuth or session-based authentication for the admin panel.
- Add input validation for AdSense code to prevent XSS attacks.
- Support multiple ad slots or dynamic ad sizes.
- Enhance search analytics with more metrics (e.g., search frequency over time).
- Integrate additional GitHub API features, like user or issue searches.

This CMS combines powerful search functionality, a visually captivating interface, and practical monetization features, making it a versatile solution for tech-focused websites or developer tools.

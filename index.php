<?php
// index.php - Homepage with GitHub advanced search, abstract design network, and pagination
include 'db.php';
include 'github_api.php'; // Include the GitHub API functions

// Handle search
$results = [];
$error = '';
$totalPages = 1;
$currentPage = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;

if (isset($_GET['q'])) {
    $query = trim($_GET['q']);
    if (!empty($query)) {
        logSearch($query);

        // Get search parameters
        $language = $_GET['language'] ?? '';
        $stars = $_GET['stars'] ?? '';
        $sort = $_GET['sort'] ?? 'stars';
        $order = $_GET['order'] ?? 'desc';

        // Call GitHub API with pagination
        $response = searchGitHubRepos($query, $language, $stars, $sort, $order, $currentPage);
        $results = $response['results'];
        $error = $response['error'];
        $totalCount = $response['total_count'] ?? 0;
        $totalPages = min(ceil($totalCount / 10), 100); // GitHub API limits to 1000 results (100 pages at 10 per page)
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GitHub Repo Search</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            background-color: #f5f5f7;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            color: #1d1d1f;
            overflow: auto;
        }
        canvas {
            position: fixed;
            top: 0;
            left: 0;
            z-index: -1;
            opacity: 0.3;
        }
        .search-container {
            background-color: white;
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            width: 600px;
            text-align: center;
            z-index: 1;
        }
        .search-container h1 {
            font-size: 32px;
            margin-bottom: 20px;
        }
        form {
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        input[type="text"], select {
            width: 100%;
            padding: 12px;
            margin: 8px 0;
            border: 1px solid #d2d2d7;
            border-radius: 8px;
            font-size: 16px;
        }
        input[type="hidden"] {
            display: none;
        }
        button {
            background-color: #0071e3;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
            margin-top: 16px;
        }
        button:hover {
            background-color: #0077ed;
        }
        .results {
            margin-top: 40px;
            text-align: left;
        }
        .repo {
            background-color: #f5f5f7;
            padding: 16px;
            margin: 16px 0;
            border-radius: 12px;
        }
        .repo h3 {
            margin: 0 0 8px;
        }
        .repo p {
            margin: 4px 0;
        }
        .error {
            color: red;
            margin-top: 20px;
        }
        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-top: 20px;
        }
        .pagination button {
            margin: 0 10px;
        }
        .pagination span {
            font-size: 16px;
        }
        /* Dark mode styles */
        .dark-mode {
            background-color: #1d1d1f;
            color: #f5f5f7;
        }
        .dark-mode .search-container {
            background-color: #2c2c2e;
            box-shadow: 0 4px 12px rgba(0,0,0,0.5);
        }
        .dark-mode input[type="text"], .dark-mode select {
            background-color: #3a3a3c;
            border-color: #3a3a3c;
            color: #f5f5f7;
        }
        .dark-mode .repo {
            background-color: #2c2c2e;
        }
        .toggle-dark {
            position: absolute;
            top: 20px;
            right: 20px;
            background: none;
            border: none;
            cursor: pointer;
            font-size: 24px;
            z-index: 2;
        }
    </style>
</head>
<body>
    <canvas id="networkCanvas"></canvas>
    <button class="toggle-dark" onclick="toggleDarkMode()">🌙</button>
    <div class="search-container">
        <h1>Search GitHub Repositories</h1>
        <form method="GET">
            <input type="text" name="q" placeholder="Enter keywords (e.g., tetris language:assembly)" value="<?php echo isset($_GET['q']) ? htmlspecialchars($_GET['q']) : ''; ?>" required>
            <input type="text" name="language" placeholder="Language (e.g., php)" value="<?php echo isset($_GET['language']) ? htmlspecialchars($_GET['language']) : ''; ?>">
            <input type="number" name="stars" placeholder="Minimum stars (e.g., 100)" value="<?php echo isset($_GET['stars']) ? htmlspecialchars($_GET['stars']) : ''; ?>">
            <select name="sort">
                <option value="stars" <?php echo (isset($_GET['sort']) && $_GET['sort'] === 'stars') ? 'selected' : ''; ?>>Sort by Stars</option>
                <option value="forks" <?php echo (isset($_GET['sort']) && $_GET['sort'] === 'forks') ? 'selected' : ''; ?>>Sort by Forks</option>
                <option value="updated" <?php echo (isset($_GET['sort']) && $_GET['sort'] === 'updated') ? 'selected' : ''; ?>>Sort by Updated</option>
            </select>
            <select name="order">
                <option value="desc" <?php echo (isset($_GET['order']) && $_GET['order'] === 'desc') ? 'selected' : ''; ?>>Descending</option>
                <option value="asc" <?php echo (isset($_GET['order']) && $_GET['order'] === 'asc') ? 'selected' : ''; ?>>Ascending</option>
            </select>
            <input type="hidden" name="page" value="<?php echo $currentPage; ?>">
            <button type="submit">Search</button>
        </form>
        <?php if ($error): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <div class="results">
            <?php foreach ($results as $repo): ?>
                <div class="repo">
                    <h3><a href="<?php echo htmlspecialchars($repo['html_url']); ?>" target="_blank"><?php echo htmlspecialchars($repo['full_name']); ?></a></h3>
                    <p><?php echo htmlspecialchars($repo['description'] ?? 'No description'); ?></p>
                    <p>⭐ Stars: <?php echo $repo['stargazers_count']; ?> | 🍴 Forks: <?php echo $repo['forks_count']; ?> | Language: <?php echo htmlspecialchars($repo['language'] ?? 'N/A'); ?></p>
                </div>
            <?php endforeach; ?>
        </div>
        <?php if ($totalPages > 1): ?>
            <div class="pagination">
                <?php if ($currentPage > 1): ?>
                    <button onclick="changePage(<?php echo $currentPage - 1; ?>)">Previous</button>
                <?php endif; ?>
                <span>Page <?php echo $currentPage; ?> of <?php echo $totalPages; ?></span>
                <?php if ($currentPage < $totalPages): ?>
                    <button onclick="changePage(<?php echo $currentPage + 1; ?>)">Next</button>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
    <script>
        // Abstract design network
        const canvas = document.getElementById('networkCanvas');
        const ctx = canvas.getContext('2d');
        let width, height;

        function resizeCanvas() {
            width = window.innerWidth;
            height = window.innerHeight;
            canvas.width = width;
            canvas.height = height;
        }
        resizeCanvas();
        window.addEventListener('resize', resizeCanvas);

        const nodes = [];
        const nodeCount = 20;
        const mouse = { x: 0, y: 0 };

        // Create nodes
        for (let i = 0; i < nodeCount; i++) {
            nodes.push({
                x: Math.random() * width,
                y: Math.random() * height,
                vx: (Math.random() - 0.5) * 2,
                vy: (Math.random() - 0.5) * 2,
                radius: 4
            });
        }

        function animate() {
            ctx.clearRect(0, 0, width, height);
            ctx.fillStyle = 'rgba(0, 113, 227, 0.5)';
            ctx.strokeStyle = 'rgba(0, 113, 227, 0.3)';

            // Update and draw nodes
            nodes.forEach(node => {
                node.x += node.vx;
                node.y += node.vy;

                // Bounce off edges
                if (node.x < 0 || node.x > width) node.vx *= -1;
                if (node.y < 0 || node.y > height) node.vy *= -1;

                // Attraction to mouse
                const dx = mouse.x - node.x;
                const dy = mouse.y - node.y;
                const distance = Math.sqrt(dx * dx + dy * dy);
                if (distance < 150) {
                    node.vx += dx * 0.001;
                    node.vy += dy * 0.001;
                }

                // Draw node
                ctx.beginPath();
                ctx.arc(node.x, node.y, node.radius, 0, Math.PI * 2);
                ctx.fill();
            });

            // Draw connections
            ctx.beginPath();
            for (let i = 0; i < nodes.length; i++) {
                for (let j = i + 1; j < nodes.length; j++) {
                    const dx = nodes[i].x - nodes[j].x;
                    const dy = nodes[i].y - nodes[j].y;
                    const distance = Math.sqrt(dx * dx + dy * dy);
                    if (distance < 100) {
                        ctx.moveTo(nodes[i].x, nodes[i].y);
                        ctx.lineTo(nodes[j].x, nodes[j].y);
                    }
                }
                const dx = nodes[i].x - mouse.x;
                const dy = nodes[i].y - mouse.y;
                const distance = Math.sqrt(dx * dx + dy * dy);
                if (distance < 150) {
                    ctx.moveTo(nodes[i].x, nodes[i].y);
                    ctx.lineTo(mouse.x, mouse.y);
                }
            }
            ctx.stroke();

            requestAnimationFrame(animate);
        }

        // Update mouse position
        document.addEventListener('mousemove', (e) => {
            mouse.x = e.clientX;
            mouse.y = e.clientY;
        });

        animate();

        // Pagination function
        function changePage(page) {
            document.querySelector('input[name="page"]').value = page;
            document.querySelector('form').submit();
        }

        // Dark mode toggle
        function toggleDarkMode() {
            document.body.classList.toggle('dark-mode');
        }
    </script>
</body>
</html>
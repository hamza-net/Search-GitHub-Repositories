<?php
// admin.php - Admin panel to view searches
include 'db.php';

// Simple auth - In production, use proper auth like sessions or OAuth
$username = 'admin';
$password = 'password123'; // Change this!

if (!isset($_SERVER['PHP_AUTH_USER']) || !isset($_SERVER['PHP_AUTH_PW']) ||
    $_SERVER['PHP_AUTH_USER'] !== $username || $_SERVER['PHP_AUTH_PW'] !== $password) {
    header('WWW-Authenticate: Basic realm="Admin Panel"');
    header('HTTP/1.0 401 Unauthorized');
    echo 'Unauthorized';
    exit;
}

$searches = getAllSearches();
$topSearches = getTopSearches();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            background-color: #f5f5f7;
            padding: 40px;
            color: #1d1d1f;
        }
        h1, h2 {
            text-align: center;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background-color: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #d2d2d7;
        }
        th {
            background-color: #f5f5f7;
        }
        /* Surprise: Chart for top searches */
        canvas {
            margin: 40px auto;
            display: block;
            max-width: 600px;
        }
    </style>
</head>
<body>
    <h1>Admin Panel: User Searches</h1>
    
    <h2>Top Searches</h2>
    <canvas id="topSearchesChart"></canvas>
    
    <h2>All Searches</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Query</th>
                <th>Timestamp</th>
                <th>IP</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($searches as $search): ?>
                <tr>
                    <td><?php echo $search['id']; ?></td>
                    <td><?php echo htmlspecialchars($search['query']); ?></td>
                    <td><?php echo $search['timestamp']; ?></td>
                    <td><?php echo htmlspecialchars($search['ip']); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const topSearches = <?php echo json_encode($topSearches); ?>;
        const labels = topSearches.map(s => s.query);
        const data = topSearches.map(s => s.count);
        
        new Chart(document.getElementById('topSearchesChart'), {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Search Count',
                    data: data,
                    backgroundColor: '#0071e3'
                }]
            },
            options: {
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
    </script>
</body>
</html>
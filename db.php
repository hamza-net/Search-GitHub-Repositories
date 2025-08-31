<?php
// db.php - Database connection and functions
function getDb() {
    $db = new PDO('sqlite:searches.db');
    $db->exec("CREATE TABLE IF NOT EXISTS searches (id INTEGER PRIMARY KEY, query TEXT, timestamp DATETIME DEFAULT CURRENT_TIMESTAMP, ip TEXT)");
    return $db;
}

function logSearch($query) {
    $db = getDb();
    $ip = $_SERVER['REMOTE_ADDR'];
    $stmt = $db->prepare("INSERT INTO searches (query, ip) VALUES (?, ?)");
    $stmt->execute([$query, $ip]);
}

function getAllSearches() {
    $db = getDb();
    $stmt = $db->query("SELECT * FROM searches ORDER BY timestamp DESC");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getTopSearches($limit = 10) {
    $db = getDb();
    $stmt = $db->query("SELECT query, COUNT(*) as count FROM searches GROUP BY query ORDER BY count DESC LIMIT $limit");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
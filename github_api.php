<?php
// github_api.php
function searchGitHubRepos($query, $language = '', $stars = '', $sort = 'stars', $order = 'desc', $page = 1) {
    $apiQuery = urlencode($query);
    if (!empty($language)) {
        $apiQuery .= '+language:' . urlencode($language);
    }
    if (!empty($stars)) {
        $apiQuery .= '+stars:>' . urlencode($stars);
    }

    $url = "https://api.github.com/search/repositories?q=$apiQuery&sort=$sort&order=$order&per_page=10&page=$page";
    
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERAGENT, 'PHP-CMS-Search');
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($response && $httpCode === 200) {
        $data = json_decode($response, true);
        if (isset($data['items'])) {
            return [
                'results' => $data['items'],
                'total_count' => $data['total_count'] ?? 0,
                'error' => ''
            ];
        }
        return [
            'results' => [],
            'total_count' => 0,
            'error' => 'Error fetching results: ' . ($data['message'] ?? 'Unknown error')
        ];
    }
    return [
        'results' => [],
        'total_count' => 0,
        'error' => 'Failed to connect to GitHub API. HTTP Code: ' . $httpCode
    ];
}
?>
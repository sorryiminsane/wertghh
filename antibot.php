<?php
// Bypass antibot check for local development
function validateBot($ip, $userAgent, $apiKey) {
    // Always return false in development to bypass the check
    return false;
    
    /* Original code for reference:
    $url = 'https://antibot.pw/api/v2-blockers?ip=' . urlencode($ip) . '&apikey=' . urlencode($apiKey) . '&ua=' . urlencode($userAgent);
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);

    curl_close($ch);
    $responseData = json_decode($response, true);

    if ($responseData && isset($responseData['is_bot'])) {
        return $responseData['is_bot'];
    } else {
        return false;
    }
    */
}

$apiKey = '3617dc46dcf4635ed22eccfefd434031'; // acc 1

$ip = $_SERVER['REMOTE_ADDR'];
$userAgent = $_SERVER['HTTP_USER_AGENT'];

// Skip bot check for localhost
if ($ip !== '127.0.0.1' && $ip !== '::1') {
    $isBot = validateBot($ip, $userAgent, $apiKey);
    
    if ($isBot) {
        header("HTTP/1.1 403 Forbidden");
        header('Location: finish.php');
        exit();
    }
}
?>
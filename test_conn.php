<?php
require_once 'config.php';

$url = SUPABASE_URL . '/rest/v1/companies?select=*&limit=1';
$headers = [
    'apikey: ' . SUPABASE_ANON_KEY,
    'Authorization: Bearer ' . SUPABASE_ANON_KEY,
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true); // set to false if SSL issues

$response = curl_exec($ch);
if (curl_errno($ch)) {
    echo '❌ cURL error: ' . curl_error($ch);
} else {
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    if ($http_code === 200) {
        echo '✅ Connection successful! Data: ';
        var_dump(json_decode($response, true));
    } else {
        echo "❌ HTTP error: $http_code\nResponse: $response";
    }
}
curl_close($ch);
?>

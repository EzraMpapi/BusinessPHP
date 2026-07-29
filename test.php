<?php
// Use the credentials from your config.php
$url = 'https://bqrpiookucsdjvcvjrul.supabase.co/rest/v1/companies?select=*&limit=1';
$apikey = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6ImJxcnBpb29rdWNzZGp2Y3ZqcnVsIiwicm9sZSI6ImFub24iLCJpYXQiOjE3ODMyNjAxOTgsImV4cCI6MjA5ODgzNjE5OH0.qfjK9-OTsRJFuywvZFWsAFsOgMWzLIvx8Fc5-xeQuqA';

$headers = [
    'apikey: ' . $apikey,
    'Authorization: Bearer ' . $apikey,
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // TEMPORARY: ignore SSL for testing

$response = curl_exec($ch);
$error = curl_error($ch);
$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Code: $httpcode\n";
if ($error) {
    echo "cURL Error: $error\n";
}
echo "Response: $response\n";
?>

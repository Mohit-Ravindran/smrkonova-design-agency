<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["error" => "Method not allowed"]);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);

$name = isset($data['name']) ? $data['name'] : 'Unknown';
$email = isset($data['email']) ? $data['email'] : 'Unknown';
$phone = isset($data['phone']) ? $data['phone'] : 'Not provided';
$message = isset($data['message']) ? $data['message'] : '';

$htmlBody = "
<div style='font-family: sans-serif; padding: 20px;'>
    <h2>New Lead from Healthcare Page</h2>
    <p><strong>Name:</strong> " . htmlspecialchars($name) . "</p>
    <p><strong>Email:</strong> " . htmlspecialchars($email) . "</p>
    <p><strong>Phone:</strong> " . htmlspecialchars($phone) . "</p>
    <p><strong>Message:</strong><br>" . nl2br(htmlspecialchars($message)) . "</p>
</div>
";

$url = "https://api.zeptomail.in/v1.1/email";
$token = "Zoho-enczapikey PHtE6r0KQL/o3TEo8BNU5aPtQ8T3PYorr+xmfwREtopEW/BXS01Qoo8vkTCyok98AKFARvfPmo1rtLzK4uKMd2rkNTsaXGqyqK3sx/VYSPOZsbq6x00YuFUddkTeUYLse99i0yLSvNnYNA==";

$postData = [
    "from" => [ "address" => "noreply@smrkonova.com" ],
    "to" => [
        [ "email_address" => [ "address" => "design@smrkonova.com", "name" => "Design" ] ]
    ],
    "subject" => "New Lead from $name - Healthcare Page",
    "htmlbody" => $htmlBody
];

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Accept: application/json",
    "Content-Type: application/json",
    "Authorization: " . $token
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

if ($httpCode >= 200 && $httpCode < 300) {
    http_response_code(200);
    echo json_encode(["success" => true, "message" => "Email sent successfully", "response" => json_decode($response)]);
} else {
    http_response_code(500);
    echo json_encode(["success" => false, "error" => $error, "zepto_response" => json_decode($response)]);
}

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

function sendZeptoMail($url, $token, $postData) {
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
    
    return [
        "success" => ($httpCode >= 200 && $httpCode < 300),
        "httpCode" => $httpCode,
        "error" => $error,
        "response" => json_decode($response)
    ];
}

// 1. Send notification to Design team
$adminPostData = [
    "from" => [ "address" => "noreply@smrkonova.com" ],
    "to" => [
        [ "email_address" => [ "address" => "design@smrkonova.com", "name" => "Design" ] ]
    ],
    "subject" => "New Lead from $name - Healthcare Page",
    "htmlbody" => $htmlBody
];

$adminResult = sendZeptoMail($url, $token, $adminPostData);

if (!$adminResult['success']) {
    http_response_code(500);
    echo json_encode(["success" => false, "error" => "Failed to send notification email", "details" => $adminResult]);
    exit();
}

// 2. Send auto-reply to the user (only if email is valid)
if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $userHtmlBody = "
    <!DOCTYPE html>
    <html lang='en'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>We've Received Your Enquiry</title>
    </head>
    <body style='margin: 0; padding: 0; background-color: #000000; font-family: -apple-system, BlinkMacSystemFont, \"Segoe UI\", Roboto, \"Helvetica Neue\", Arial, sans-serif; -webkit-font-smoothing: antialiased;'>
        <table border='0' cellpadding='0' cellspacing='0' width='100%' style='background-color: #000000; width: 100%; margin: 0 auto; padding: 40px 20px;'>
            <tr>
                <td align='center'>
                    <table border='0' cellpadding='0' cellspacing='0' width='100%' style='width: 100%; max-width: 600px; background-color: #111111; border: 1px solid #222222; border-radius: 12px; overflow: hidden;'>
                        <!-- Header -->
                        <tr>
                            <td align='center' style='padding: 40px 20px; border-bottom: 1px solid #222222; background-color: #000000;'>
                                <img src='https://smrkonova.com/assets/img/industries/healthcare/logo.svg' alt='Smrkonova' width='180' style='display: block; width: 180px; max-width: 100%; height: auto;'>
                            </td>
                        </tr>
                        <!-- Body -->
                        <tr>
                            <td style='padding: 40px; color: #a3a3a3; font-size: 15px; line-height: 1.6;'>
                                <p style='margin-top: 0; color: #ffffff; font-size: 18px; font-weight: 600;'>Hi " . htmlspecialchars($name) . ",</p>
                                <p>Thank you for reaching out to Smrkonova.</p>
                                <p>We're excited to learn more about your idea.</p>
                                <p>If you've contacted us, there's a good chance you're ready to build something extraordinary—and that's exactly what we love helping businesses do.</p>
                                <p>Our team has received your enquiry and will review the details carefully. One of our team members will get back to you as soon as possible to discuss your requirements and the best way forward.</p>
                                <p>Whether you're planning a website, mobile app, custom software, branding, or a complete digital transformation, we're excited to explore the possibilities with you.</p>
                                <p style='margin-bottom: 30px;'>Thank you for considering Smrkonova. We look forward to building something remarkable together.</p>
                                
                                <p style='margin: 0; color: #ffffff; font-weight: 500;'>Best regards,<br>Smrkonova</p>
                            </td>
                        </tr>
                        <!-- Footer -->
                        <tr>
                            <td align='center' style='padding: 20px 40px 40px; color: #666666; font-size: 11px; letter-spacing: 1px; text-transform: uppercase;'>
                                &copy; 2026 Smrkonova. All rights reserved.
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </body>
    </html>
    ";
    
    $userPostData = [
        "from" => [ "address" => "noreply@smrkonova.com" ],
        "to" => [
            [ "email_address" => [ "address" => $email, "name" => $name ] ]
        ],
        "subject" => "We've Received Your Enquiry 🚀",
        "htmlbody" => $userHtmlBody
    ];
    
    sendZeptoMail($url, $token, $userPostData); // We don't block if auto-reply fails
}

http_response_code(200);
echo json_encode(["success" => true, "message" => "Emails processed successfully"]);


<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name    = isset($_POST['name']) ? trim($_POST['name']) : '';
    $phone   = isset($_POST['phone']) ? trim($_POST['phone']) : '';
    $email   = isset($_POST['email']) ? trim($_POST['email']) : '';
    $message = isset($_POST['message']) ? trim($_POST['message']) : '';

    if (empty($name) || empty($phone) || empty($email)) {
        echo "Please fill all required fields.";
        exit;
    }

    $to = "kalyani.smrkonova@gmail.com"; // 👉 your email
    $subject = "New Contact Form Submission";

    // HTML email body
    $body = "
    <html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; color: #333; }
            .container { padding: 20px; border: 1px solid #ddd; border-radius: 8px; }
            h2 { color: #0066cc; }
            p { margin: 10px 0;    font-size: 20px; }
            .label { font-weight: bold; color: #444; }
        </style>
    </head>
    <body>
        <div class='container'>
            <h2>📩 New Contact Form Submission</h2>
            <p><span class='label'>Name:</span> {$name}</p>
            <p><span class='label'>Phone:</span> {$phone}</p>
            <p><span class='label'>Email:</span> {$email}</p>
            <p><span class='label'>Message:</span><br>" . nl2br($message) . "</p>
        </div>
    </body>
    </html>
    ";

    // Headers for HTML email
    $headers  = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type: text/html; charset=UTF-8" . "\r\n";
    $headers .= "From: {$email}\r\n";
    $headers .= "Reply-To: {$email}\r\n";

    if (mail($to, $subject, $body, $headers)) {
        echo "success";
    } else {
        echo "Something went wrong. Please try again later.";
    }
} else {
    echo "Invalid request.";
}

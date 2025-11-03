<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name      = isset($_POST['name']) ? trim($_POST['name']) : '';
    $phone     = isset($_POST['phone']) ? trim($_POST['phone']) : '';
    $fullPhone = isset($_POST['full_phone']) ? trim($_POST['full_phone']) : '';
    $email     = isset($_POST['email']) ? trim($_POST['email']) : '';
    $message   = isset($_POST['message']) ? trim($_POST['message']) : '';
    $budget    = isset($_POST['budget']) ? trim($_POST['budget']) : '';
    $startTime = isset($_POST['start_time']) ? trim($_POST['start_time']) : '';

    // Validation check
    if (empty($name) || empty($phone) || empty($email) || empty($budget) || empty($startTime)) {
        echo "<script>alert('Please fill all required fields.'); window.location.href=document.referrer;</script>";
        exit;
    }

    $to = "kalyani.smrkonova@gmail.com"; // 👉 your email
    $subject = "Contact Form ";

    // Email Body
    $body = "
    <html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; color: #333; }
            .container { padding: 20px; border: 1px solid #ddd; border-radius: 8px; }
            h2 { color: #0066cc; }
            p { margin: 10px 0; font-size: 16px; }
            .label { font-weight: bold; color: #444; }
        </style>
    </head>
    <body>
        <div class='container'>
            <h2>📩 Contact Form </h2>
            <p><span class='label'>Name:</span> {$name}</p>
            <p><span class='label'>Phone (with code):</span> {$fullPhone}</p>
            <p><span class='label'>Phone (typed):</span> {$phone}</p>
            <p><span class='label'>Email:</span> {$email}</p>
            <p><span class='label'>Budget Range:</span> {$budget}</p>
            <p><span class='label'>Start Time:</span> {$startTime}</p>
            <p><span class='label'>Message:</span><br>" . nl2br($message) . "</p>
        </div>
    </body>
    </html>
    ";

    // Headers
    $headers  = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type: text/html; charset=UTF-8" . "\r\n";
    $headers .= "From: {$email}\r\n";
    $headers .= "Reply-To: {$email}\r\n";

    // Send email
    if (mail($to, $subject, $body, $headers)) {
        header("Location: ./thank-you.html"); // ✅ Redirect to thank-you page
        exit;
    } else {
        echo "<script>alert('Something went wrong. Please try again later.'); window.location.href=document.referrer;</script>";
    }
} else {
    echo "Invalid request."; // ✅ this part was missing earlier
}

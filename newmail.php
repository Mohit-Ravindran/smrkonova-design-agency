<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize inputs
    $name      = isset($_POST['name']) ? trim($_POST['name']) : '';
    $phone     = isset($_POST['phone']) ? trim($_POST['phone']) : '';
    $fullPhone = isset($_POST['full_phone']) ? trim($_POST['full_phone']) : '';
    $email     = isset($_POST['email']) ? trim($_POST['email']) : '';
    $message   = isset($_POST['message']) ? trim($_POST['message']) : '';

    // Validation check
    if (empty($name) || empty($phone) || empty($email)) {
        echo "<script>alert('Please fill all required fields.'); window.location.href=document.referrer;</script>";
        exit;
    }

    // Recipient email
    $to = "kalyani.smrkonova@gmail.com"; // 👉 your email address
    $subject = "New Contact Form Submission";

    // Email body (HTML format)
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
            <h2>📩 New Contact Form Submission</h2>
            <p><span class='label'>Name:</span> {$name}</p>
            <p><span class='label'>Phone (typed):</span> {$phone}</p>
            <p><span class='label'>Phone (with code):</span> {$fullPhone}</p>
            <p><span class='label'>Email:</span> {$email}</p>
            <p><span class='label'>Message:</span><br>" . nl2br($message) . "</p>
        </div>
    </body>
    </html>
    ";

    // Email headers
    $headers  = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type: text/html; charset=UTF-8" . "\r\n";
    $headers .= "From: {$email}\r\n";
    $headers .= "Reply-To: {$email}\r\n";

    // Send the email
    if (mail($to, $subject, $body, $headers)) {
        header("Location: ./thank-you.html"); // ✅ Redirect to thank-you page
        exit;
    } else {
        echo "<script>alert('Something went wrong. Please try again later.'); window.location.href=document.referrer;</script>";
    }
} else {
    echo "Invalid request.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Your Quote Request - Simplytronix</title>
    <style>
        body {
            background-color: #f9f9f9;
            color: #333;
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        .email-wrapper {
            max-width: 650px;
            margin: 20px auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 0 10px rgba(0,0,0,0.05);
        }
        .email-header {
            background-color: #004080;
            color: #fff;
            padding: 20px;
            text-align: center;
        }
        .email-body {
            padding: 30px;
        }
        .email-body p {
            line-height: 1.6;
            font-size: 16px;
        }
        .details-table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
        }
        .details-table td {
            padding: 8px;
            border-bottom: 1px solid #eee;
        }
        .footer {
            background-color: #f0f0f0;
            text-align: center;
            padding: 20px;
            font-size: 14px;
            color: #666;
        }
        .footer a {
            color: #004080;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="email-wrapper">
        <div class="email-header">
            <h2>Quote Request Received</h2>
        </div>
        <div class="email-body">
            <p>Hi {{ $name ?? 'Valued Customer' }},</p>

            <p>Thank you for reaching out to <strong>Simplytronix</strong>. We've received your quote request and our team is reviewing it. You will hear from us shortly!</p>


            <p>If you have any questions or additional details to share, feel free to reply to this email or contact us at <a href="mailto:sales@simplytronix.com">sales@simplytronix.com</a>.</p>

            <p>We appreciate your interest in Simplytronix and look forward to assisting you!</p>
        </div>
        <div class="footer">
            <p>
                Simplytronix LLC<br>
                2055 Limestone Rd STE 200-C, Wilmington, DE 19808<br>
                <strong>Phone:</strong> +1 302-613-4473<br>
                <a href="https://www.simplytronix.com">www.simplytronix.com</a>
            </p>
        </div>
    </div>
</body>
</html>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>New Quote Request</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f4f4;
            color: #333;
            padding: 20px;
        }
        .container {
            background: #ffffff;
            padding: 20px;
            border-radius: 6px;
            box-shadow: 0px 0px 8px rgba(0, 0, 0, 0.05);
            max-width: 600px;
            margin: 0 auto;
        }
        h2 {
            color: #2a2a2a;
            margin-bottom: 20px;
        }
        p {
            margin: 6px 0;
        }
        strong {
            display: inline-block;
            width: 150px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>New Quote Request Received</h2>

        <p><strong>Name:</strong> {{ $name ?? 'N/A' }}</p>
        <p><strong>Email:</strong> {{ $email ?? 'N/A' }}</p>
        <p><strong>Phone:</strong> {{ $phone ?? 'N/A' }}</p>
        <p><strong>Company:</strong> {{ $company ?? 'N/A' }}</p>
        <p><strong>Part Number:</strong> {{ $part_number ?? 'N/A' }}</p>
        <p><strong>Quantity:</strong> {{ $quantity ?? 'N/A' }}</p>
        <p><strong>Comments:</strong><br> {!! nl2br(e($comments ?? 'None')) !!}</p>

        <br><hr>
        <p>This message was generated automatically by the Simplytronix website.</p>
    </div>
</body>
</html>

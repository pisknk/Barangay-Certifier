<!DOCTYPE html>
<html>
<head>
    <title>Welcome to BarangayCertify</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background: #f9f9f9;
            padding: 20px;
            border-radius: 5px;
            border: 1px solid #ddd;
        }
        h1 {
            color: #28a745;
        }
        .highlight-box {
            background: #e9f7ef;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
            border-left: 4px solid #28a745;
        }
        .footer {
            margin-top: 30px;
            font-size: 12px;
            color: #777;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Welcome to BarangayCertify!</h1>
        
        <p>Hello {{ $tenant->name }},</p>
        
        <p>Thank you for signing up for BarangayCertify! Your account has been created and is now pending activation by our administrators.</p>
        
        <div class="highlight-box">
            <p><strong>What happens next?</strong></p>
            <ol>
                <li>Our administrators will review your account details</li>
                <li>Once approved, your tenant account will be activated</li>
                <li>You'll receive an activation email with your temporary password</li>
                <li>Your barangay-specific database will be created</li>
            </ol>
        </div>
        
        <p>Once your account is activated, you will receive another email with your temporary password and login instructions.</p>
        
        <p>Your subscription details:</p>
        <ul>
            <li><strong>Barangay:</strong> {{ $tenant->barangay }}</li>
            <li><strong>Subscription Plan:</strong> {{ $tenant->subscription_plan }}</li>
        </ul>
        
        <p>If you have any questions, please don't hesitate to contact our support team.</p>
        
        <p>Best regards,<br>
        The BarangayCertify Team</p>
        
        <div class="footer">
            <p>This is an automated message, please do not reply directly to this email.</p>
        </div>
    </div>
</body>
</html> 
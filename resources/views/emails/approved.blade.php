<!DOCTYPE html>
<html>
<head>
    <title>Account Approved - BarangayCertify</title>
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
        .credentials {
            background: #e9f7ef;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
            border-left: 4px solid #28a745;
        }
        .button {
            display: inline-block;
            background-color: #28a745;
            color: #ffffff !important;
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 5px;
            margin: 20px 0;
            font-weight: bold;
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
        <h1>Your Account Has Been Approved!</h1>
        
        <p>Hello {{ $tenant->name }},</p>
        
        <p>Great news! Your BarangayCertify account has been approved and activated. You can now access your dashboard and start using our services.</p>
        
        <p>Your domain has been set up at:</p>
        
        <div style="text-align: center;">
            <a href="{{ $domain_url }}" class="button">Access Your Dashboard</a>
            <p><strong>{{ $domain_url }}</strong></p>
        </div>
        
        <div class="credentials">
            <p><strong>Your Login Details:</strong></p>
            <p>Email: {{ $tenant->email }}</p>
            <div style="text-align: center; margin: 20px 0;">
                @if(isset($setup_token) && $setup_token)
                <a href="{{ url('/setup-password/' . $tenant->id . '/' . $setup_token) }}" class="button">Finish Setting Up Your Domain</a>
                @else
                <p><strong>Error: Setup token is missing. Please contact support.</strong></p>
                @endif
            </div>
            <p>Click the button above to create your password and complete your account setup.</p>
        </div>
        
        <p>For security reasons, please change your password immediately after your first login by visiting your account settings.</p>
        
        <p>Your subscription details:</p>
        <ul>
            <li><strong>Barangay:</strong> {{ $tenant->barangay }}</li>
            <li><strong>Subscription Plan:</strong> {{ $tenant->subscription_plan }}</li>
        </ul>
        
        <p>If you have any questions or need assistance, please contact our support team.</p>
        
        <p>Best regards,<br>
        The BarangayCertify Team</p>
        
        <div class="footer">
            <p>This is an automated message, please do not reply directly to this email.</p>
        </div>
    </div>
</body>
</html> 
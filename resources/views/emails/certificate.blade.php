<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Your Certificate</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }
        .logo {
            max-width: 120px;
            height: auto;
            margin-bottom: 10px;
        }
        h1 {
            color: #2c3e50;
            font-size: 24px;
            margin-bottom: 20px;
        }
        .content {
            margin-bottom: 30px;
        }
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            font-size: 12px;
            color: #777;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $barangayName }}</h1>
    </div>
    
    <div class="content">
        <p>Greetings,</p>
        
        <p>Your requested certificate from {{ $barangayName }} is attached to this email in PDF format.</p>
        
        @if(!empty($emailMessage))
            <p><strong>Message:</strong></p>
            <p>{{ $emailMessage }}</p>
        @endif
        
        <p>To view the certificate, please open the attached PDF file. You can print this certificate for your records.</p>
        
        <p>If you have any questions or need further assistance, please contact our barangay office.</p>
        
        <p>Thank you.</p>
    </div>
    
    <div class="footer">
        <p>This is an automated email. Please do not reply to this message.</p>
        <p>&copy; {{ date('Y') }} {{ $barangayName }}. All rights reserved.</p>
    </div>
</body>
</html> 
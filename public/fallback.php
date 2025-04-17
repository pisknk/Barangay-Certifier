<?php
// Simple fallback landing page in case of routing issues
header('Content-Type: text/html');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Barangay Certify - Fallback Page</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            background-color: #f7fafc;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            text-align: center;
        }
        .container {
            max-width: 800px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 2rem;
            margin: 1rem;
        }
        h1 {
            color: #4a5568;
            margin-bottom: 1rem;
        }
        p {
            color: #718096;
            line-height: 1.6;
            margin-bottom: 1.5rem;
        }
        .btn {
            display: inline-block;
            background-color: #4c51bf;
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 0.375rem;
            text-decoration: none;
            font-weight: bold;
            transition: background-color 0.2s;
        }
        .btn:hover {
            background-color: #434190;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Welcome to Barangay Certify</h1>
        <p>From cedulas to community documents, Barangay Certify makes local governance digital, efficient, and hassle-free.</p>
        <a href="/signup" class="btn">Sign Up Now</a>
    </div>
</body>
</html> 
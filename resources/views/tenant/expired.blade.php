<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Subscription Expired</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background-color: #f8f9fa;
    }
    .card {
      max-width: 600px;
      margin: 50px auto;
      border-radius: 20px;
      box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    }
    .card img {
      width: 100px;
      margin: 20px auto 0;
      display: block;
    }
    .title {
      font-size: 2.5rem;
      font-weight: bold;
    }
    .bottom-note {
      font-size: 0.95rem;
      color: #6c757d;
    }
    .expired-date {
      font-weight: bold;
      color: #dc3545;
    }
  </style>
</head>
<body>

  <div class="card text-center p-4">
    <img src="https://em-content.zobj.net/source/telegram/386/hourglass-done_231b.webp" alt="Hourglass">
    <h1 class="title mt-3"><b>Time's Up!</b></h1>
    <p class="mt-3 px-3">
      Your subscription has expired on <span class="expired-date">{{ $expirationDate ?? 'unknown date' }}</span> 
      and your domain has been automatically disabled.
    </p>
    <p class="px-3">
      Please contact the administrator to renew your subscription and regain access to your services.
    </p>
    <p class="text-warning fw-semibold px-3">
      If you don't renew within 30 days from expiration, your data stored in the database may be deleted.
    </p>
    <div class="mt-4 bottom-note">
      Your domain is <span class="fw-semibold">{{ $domain ?? 'Unknown' }}</span>, and your tenant ID is <span class="fw-semibold">{{ $tenantId ?? 'Unknown' }}</span>.
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 
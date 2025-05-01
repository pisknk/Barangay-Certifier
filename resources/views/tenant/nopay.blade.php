<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Domain Disabled</title>
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
  </style>
</head>
<body>

  <div class="card text-center p-4">
    <img src="https://em-content.zobj.net/source/telegram/386/alien-monster_1f47e.webp" alt="Alien Monster">
    <h1 class="title mt-3"><b>Oh, no!</b></h1>
    <p class="mt-3 px-3">
      Your domain has been disabled by the landlord. Please contact them for assistance on how to reactivate it.
    </p>
    <p class="text-danger fw-semibold px-3">
      If you don't settle this within 30 days from deactivation, your data that has been stored in the database will be deleted.
    </p>
    <div class="mt-4 bottom-note">
      Your domain is <span class="fw-semibold">{{ $domain ?? 'Unknown' }}</span>, and your tenant ID is <span class="fw-semibold">{{ $tenantId ?? 'Unknown' }}</span>.
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 
@extends('layouts.tenant')

@section('title', 'Dashboard')

@section('content')
<div class="container-fluid py-2">
  <!-- Display flash messages -->
  @if(session('error'))
  <div class="alert alert-danger alert-dismissible fade show" role="alert">
    {{ session('error') }}
    <button type="button" class="btn-close text-lg py-3 opacity-10" data-bs-dismiss="alert" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
  </div>
  @endif
  
  @if(session('success'))
  <div class="alert alert-success alert-dismissible fade show" role="alert">
    {{ session('success') }}
    <button type="button" class="btn-close text-lg py-3 opacity-10" data-bs-dismiss="alert" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
  </div>
  @endif

  <div class="row">
    <div class="ms-3 d-flex justify-content-between align-items-center">
      <div>
        <h3 class="mb-0 h4 font-weight-bolder">Dashboard</h3>
        <p class="mb-4">
          Quick glance at Active Members, Income, and More!
        </p>
      </div>
      @if(Auth::guard('tenant')->user()->isAdmin())
      <div>
        <a href="{{ route('tenant.users.index') }}" class="btn btn-dark mb-0">
          <i class="material-symbols-rounded">group</i> Manage Users
        </a>
      </div>
      @endif
    </div>
    <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
      <div class="card">
        <div class="card-header p-2 ps-3">
          <div class="d-flex justify-content-between">
            <div>
              <p class="text-sm mb-0 text-capitalize">Total Income</p>
              <!-- From subscriptions, calculate based on "subscription_plan" field in "tenants" table -->
              <h4 class="mb-0">₱{{ number_format($totalIncome, 0) }}</h4>
            </div>
            <div
              class="icon icon-md icon-shape bg-gradient-dark shadow-dark shadow text-center border-radius-lg"
            >
              <i class="material-symbols-rounded opacity-10">weekend</i>
            </div>
          </div>
        </div>
        <hr class="dark horizontal my-0" />
        <div class="card-footer p-2 ps-3">
          <p class="mb-0 text-sm">
            <span class="text-success font-weight-bolder">Monthly</span> active subscriptions
          </p>
        </div>
      </div>
    </div>
    <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
      <div class="card">
        <div class="card-header p-2 ps-3">
          <div class="d-flex justify-content-between">
            <div>
              <p class="text-sm mb-0 text-capitalize">
                Active Tenants
              </p>
              <!-- Based on "is_active" field in "tenants" table -->
              <h4 class="mb-0">{{ $activeTenants }}</h4>
            </div>
            <div
              class="icon icon-md icon-shape bg-gradient-dark shadow-dark shadow text-center border-radius-lg"
            >
              <i class="material-symbols-rounded opacity-10">person</i>
            </div>
          </div>
        </div>
        <hr class="dark horizontal my-0" />
        <div class="card-footer p-2 ps-3">
          <p class="mb-0 text-sm">
            <span class="text-success font-weight-bolder">Active</span> barangay accounts
          </p>
        </div>
      </div>
    </div>
    <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
      <div class="card">
        <div class="card-header p-2 ps-3">
          <div class="d-flex justify-content-between">
            <div>
              <p class="text-sm mb-0 text-capitalize">Server Status</p>
              <h4 class="mb-0">Running Fine</h4>
            </div>
            <div
              class="icon icon-md icon-shape bg-gradient-dark shadow-dark shadow text-center border-radius-lg"
            >
              <i class="material-symbols-rounded opacity-10"
                >leaderboard</i
              >
            </div>
          </div>
        </div>
        <hr class="dark horizontal my-0" />
        <div class="card-footer p-2 ps-3">
          <p class="mb-0 text-sm">
            <span class="text-success font-weight-bolder">100%</span> uptime
          </p>
        </div>
      </div>
    </div>
    <div class="col-xl-3 col-sm-6">
      <div class="card">
        <div class="card-header p-2 ps-3">
          <div class="d-flex justify-content-between">
            <div>
              <p class="text-sm mb-0 text-capitalize">Total Revenue</p>
              <h4 class="mb-0">₱{{ number_format($totalRevenue, 0) }}</h4>
            </div>
            <div
              class="icon icon-md icon-shape bg-gradient-dark shadow-dark shadow text-center border-radius-lg"
            >
              <i class="material-symbols-rounded opacity-10">weekend</i>
            </div>
          </div>
        </div>
        <hr class="dark horizontal my-0" />
        <div class="card-footer p-2 ps-3">
          <p class="mb-0 text-sm">
            <span class="text-success font-weight-bolder">All time</span> cumulative revenue
          </p>
        </div>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-lg-4 col-md-6 mt-4 mb-4">
      <div class="card">
        <div class="card-body">
          <h6 class="mb-0">Daily Website Activity</h6>
          <p class="text-sm">Visits by day of week</p>
          <div class="pe-2">
            <div class="chart">
              <canvas
                id="chart-bars"
                class="chart-canvas"
                height="170"
              ></canvas>
            </div>
          </div>
          <hr class="dark horizontal" />
          <div class="d-flex">
            <i class="material-symbols-rounded text-sm my-auto me-1"
              >schedule</i
            >
            <p class="mb-0 text-sm">updated just now</p>
          </div>
        </div>
      </div>
    </div>
    <div class="col-lg-4 col-md-6 mt-4 mb-4">
      <div class="card">
        <div class="card-body">
          <h6 class="mb-0">Monthly Income</h6>
          <p class="text-sm">
            Subscription revenue by month
          </p>
          <div class="pe-2">
            <div class="chart">
              <canvas
                id="chart-line"
                class="chart-canvas"
                height="170"
              ></canvas>
            </div>
          </div>
          <hr class="dark horizontal" />
          <div class="d-flex">
            <i class="material-symbols-rounded text-sm my-auto me-1"
              >schedule</i
            >
            <p class="mb-0 text-sm">updated just now</p>
          </div>
        </div>
      </div>
    </div>
    <div class="col-lg-4 mt-4 mb-3">
      <div class="card">
        <div class="card-body">
          <h6 class="mb-0">Certificates Issued</h6>
          <p class="text-sm">Monthly trend of certificates</p>
          <div class="pe-2">
            <div class="chart">
              <canvas
                id="chart-line-tasks"
                class="chart-canvas"
                height="170"
              ></canvas>
            </div>
          </div>
          <hr class="dark horizontal" />
          <div class="d-flex">
            <i class="material-symbols-rounded text-sm my-auto me-1"
              >schedule</i
            >
            <p class="mb-0 text-sm">just updated</p>
          </div>
        </div>
      </div>
    </div>
  </div>
  <footer class="footer py-4">
    <div class="container-fluid">
      <div class="row align-items-center justify-content-lg-between">
        <div class="col-lg-6 mb-lg-0 mb-4">
          <div
            class="copyright text-center text-sm text-muted text-lg-start"
          >
            ©
            <script>
              document.write(new Date().getFullYear());
            </script>
            , made with <i class="fa fa-heart"></i> by
            <a
              href="#"
              class="font-weight-bold"
              target="_blank"
              >Playpass Creative Labs</a
            >
          </div>
        </div>
      </div>
    </div>
  </footer>
</div>

@push('scripts')
<script>
  // Get current date information for dynamic charts
  const now = new Date();
  const currentMonth = now.getMonth();
  const currentYear = now.getFullYear();
  
  // Chart 1: Website Views - Generate data showing higher views for current month
  var ctx = document.getElementById("chart-bars").getContext("2d");
  
  // Create data with higher values for current days of the month
  const daysOfWeek = ["M", "T", "W", "T", "F", "S", "S"];
  const viewsData = daysOfWeek.map((day, index) => {
    // Generate random data with current day of week being higher
    const isCurrentDayOfWeek = index === now.getDay() - 1 || (now.getDay() === 0 && index === 6);
    return isCurrentDayOfWeek ? 
      Math.floor(Math.random() * 30) + 60 : // Higher value for current day
      Math.floor(Math.random() * 40) + 20;  // Lower values for other days
  });

  new Chart(ctx, {
    type: "bar",
    data: {
      labels: daysOfWeek,
      datasets: [
        {
          label: "Views",
          tension: 0.4,
          borderWidth: 0,
          borderRadius: 4,
          borderSkipped: false,
          backgroundColor: "#43A047",
          data: viewsData,
          barThickness: "flex",
        },
      ],
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          display: false,
        },
      },
      interaction: {
        intersect: false,
        mode: "index",
      },
      scales: {
        y: {
          grid: {
            drawBorder: false,
            display: true,
            drawOnChartArea: true,
            drawTicks: false,
            borderDash: [5, 5],
            color: "#e5e5e5",
          },
          ticks: {
            suggestedMin: 0,
            suggestedMax: 100,
            beginAtZero: true,
            padding: 10,
            font: {
              size: 14,
              lineHeight: 2,
            },
            color: "#737373",
          },
        },
        x: {
          grid: {
            drawBorder: false,
            display: false,
            drawOnChartArea: false,
            drawTicks: false,
            borderDash: [5, 5],
          },
          ticks: {
            display: true,
            color: "#737373",
            padding: 10,
            font: {
              size: 14,
              lineHeight: 2,
            },
          },
        },
      },
    },
  });

  // Chart 2: Monthly Sales - Highlight current month with higher value
  var ctx2 = document.getElementById("chart-line").getContext("2d");
  
  // Generate monthly data with current month being higher
  const months = ["J", "F", "M", "A", "M", "J", "J", "A", "S", "O", "N", "D"];
  const salesData = months.map((month, index) => {
    // Make current month higher
    return index === currentMonth ? 
      Math.floor(Math.random() * 100) + 350 : // Higher value for current month
      Math.floor(Math.random() * 200) + 100;  // Normal values for other months
  });

  new Chart(ctx2, {
    type: "line",
    data: {
      labels: months,
      datasets: [
        {
          label: "Income",
          tension: 0,
          borderWidth: 2,
          pointRadius: 3,
          pointBackgroundColor: "#43A047",
          pointBorderColor: "transparent",
          borderColor: "#43A047",
          backgroundColor: "transparent",
          fill: true,
          data: salesData,
          maxBarThickness: 6,
        },
      ],
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          display: false,
        },
        tooltip: {
          callbacks: {
            title: function (context) {
              const fullMonths = [
                "January",
                "February",
                "March",
                "April",
                "May",
                "June",
                "July",
                "August",
                "September",
                "October",
                "November",
                "December",
              ];
              return fullMonths[context[0].dataIndex];
            },
          },
        },
      },
      interaction: {
        intersect: false,
        mode: "index",
      },
      scales: {
        y: {
          grid: {
            drawBorder: false,
            display: true,
            drawOnChartArea: true,
            drawTicks: false,
            borderDash: [4, 4],
            color: "#e5e5e5",
          },
          ticks: {
            display: true,
            color: "#737373",
            padding: 10,
            font: {
              size: 12,
              lineHeight: 2,
            },
          },
        },
        x: {
          grid: {
            drawBorder: false,
            display: false,
            drawOnChartArea: false,
            drawTicks: false,
            borderDash: [5, 5],
          },
          ticks: {
            display: true,
            color: "#737373",
            padding: 10,
            font: {
              size: 12,
              lineHeight: 2,
            },
          },
        },
      },
    },
  });

  // Chart 3: Tasks - Show recent months with upward trend
  var ctx3 = document.getElementById("chart-line-tasks").getContext("2d");
  
  // Get the last 9 months (including current)
  const recentMonths = [];
  for (let i = 8; i >= 0; i--) {
    let monthIndex = (currentMonth - i + 12) % 12;
    recentMonths.push(months[monthIndex]);
  }
  
  // Generate increasing task data to show growth
  const taskData = [];
  for (let i = 0; i < 9; i++) {
    // Create an upward trend with some randomness
    let baseValue = 50 + (i * 50);
    taskData.push(baseValue + Math.floor(Math.random() * 70));
  }

  new Chart(ctx3, {
    type: "line",
    data: {
      labels: recentMonths,
      datasets: [
        {
          label: "Certificates",
          tension: 0,
          borderWidth: 2,
          pointRadius: 3,
          pointBackgroundColor: "#43A047",
          pointBorderColor: "transparent",
          borderColor: "#43A047",
          backgroundColor: "transparent",
          fill: true,
          data: taskData,
          maxBarThickness: 6,
        },
      ],
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          display: false,
        },
      },
      interaction: {
        intersect: false,
        mode: "index",
      },
      scales: {
        y: {
          grid: {
            drawBorder: false,
            display: true,
            drawOnChartArea: true,
            drawTicks: false,
            borderDash: [4, 4],
            color: "#e5e5e5",
          },
          ticks: {
            display: true,
            padding: 10,
            color: "#737373",
            font: {
              size: 14,
              lineHeight: 2,
            },
          },
        },
        x: {
          grid: {
            drawBorder: false,
            display: false,
            drawOnChartArea: false,
            drawTicks: false,
            borderDash: [4, 4],
          },
          ticks: {
            display: true,
            color: "#737373",
            padding: 10,
            font: {
              size: 14,
              lineHeight: 2,
            },
          },
        },
      },
    },
  });
</script>
@endpush
@endsection

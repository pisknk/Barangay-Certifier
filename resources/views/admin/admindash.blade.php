@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<div class="container-fluid py-2">
  <div class="row">
    <div class="ms-3">
      <h3 class="mb-0 h4 font-weight-bolder">Dashboard</h3>
      <p class="mb-4">
        Quick glance at Active Members, Income, and More!
      </p>
    </div>
    <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
      <div class="card">
        <div class="card-header p-2 ps-3">
          <div class="d-flex justify-content-between">
            <div>
              <p class="text-sm mb-0 text-capitalize">Total Income</p>
              <!-- From subscriptions, calculate based on "subscription_plan" field in "tenants" table -->
              <h4 class="mb-0">${{ $totalIncome ?? '53k' }}</h4>
            </div>
            <div
              class="icon icon-md icon-shape bg-gradient-dark shadow-dark shadow text-center border-radius-lg"
            >
              <i class="material-symbols-rounded opacity-10">weekend</i>
            </div>
          </div>
        </div>
        <hr class="dark horizontal my-0" />
        <div class="card-footer p-2 ps-3"></div>
      </div>
    </div>
    <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
      <div class="card">
        <div class="card-header p-2 ps-3">
          <div class="d-flex justify-content-between">
            <div>
              <p class="text-sm mb-0 text-capitalize">
                Paying Tenants
              </p>
              <!-- Based on "is_active" field in "tenants" table -->
              <h4 class="mb-0">{{ $activeTenants ?? '3' }}</h4>
            </div>
            <div
              class="icon icon-md icon-shape bg-gradient-dark shadow-dark shadow text-center border-radius-lg"
            >
              <i class="material-symbols-rounded opacity-10">person</i>
            </div>
          </div>
        </div>
        <hr class="dark horizontal my-0" />
        <div class="card-footer p-2 ps-3"></div>
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
        <div class="card-footer p-2 ps-3"></div>
      </div>
    </div>
    <div class="col-xl-3 col-sm-6">
      <div class="card">
        <div class="card-header p-2 ps-3">
          <div class="d-flex justify-content-between">
            <div>
              <p class="text-sm mb-0 text-capitalize">Total Revenue</p>
              <h4 class="mb-0">${{ $totalRevenue ?? '103,430' }}</h4>
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
            <span class="text-success font-weight-bolder">+5% </span>than
            yesterday
          </p>
        </div>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-lg-4 col-md-6 mt-4 mb-4">
      <div class="card">
        <div class="card-body">
          <h6 class="mb-0">Website Views</h6>
          <p class="text-sm">Last Campaign Performance</p>
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
            <p class="mb-0 text-sm">campaign sent 2 days ago</p>
          </div>
        </div>
      </div>
    </div>
    <div class="col-lg-4 col-md-6 mt-4 mb-4">
      <div class="card">
        <div class="card-body">
          <h6 class="mb-0">Daily Sales</h6>
          <p class="text-sm">
            (<span class="font-weight-bolder">+15%</span>) increase in
            today sales.
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
            <p class="mb-0 text-sm">updated 4 min ago</p>
          </div>
        </div>
      </div>
    </div>
    <div class="col-lg-4 mt-4 mb-3">
      <div class="card">
        <div class="card-body">
          <h6 class="mb-0">Completed Tasks</h6>
          <p class="text-sm">Last Campaign Performance</p>
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
  var ctx = document.getElementById("chart-bars").getContext("2d");

  new Chart(ctx, {
    type: "bar",
    data: {
      labels: ["M", "T", "W", "T", "F", "S", "S"],
      datasets: [
        {
          label: "Views",
          tension: 0.4,
          borderWidth: 0,
          borderRadius: 4,
          borderSkipped: false,
          backgroundColor: "#43A047",
          data: [50, 45, 22, 28, 50, 60, 76],
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
            suggestedMax: 500,
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

  var ctx2 = document.getElementById("chart-line").getContext("2d");

  new Chart(ctx2, {
    type: "line",
    data: {
      labels: ["J", "F", "M", "A", "M", "J", "J", "A", "S", "O", "N", "D"],
      datasets: [
        {
          label: "Sales",
          tension: 0,
          borderWidth: 2,
          pointRadius: 3,
          pointBackgroundColor: "#43A047",
          pointBorderColor: "transparent",
          borderColor: "#43A047",
          backgroundColor: "transparent",
          fill: true,
          data: [120, 230, 130, 440, 250, 360, 270, 180, 90, 300, 310, 220],
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

  var ctx3 = document.getElementById("chart-line-tasks").getContext("2d");

  new Chart(ctx3, {
    type: "line",
    data: {
      labels: [
        "Apr",
        "May",
        "Jun",
        "Jul",
        "Aug",
        "Sep",
        "Oct",
        "Nov",
        "Dec",
      ],
      datasets: [
        {
          label: "Tasks",
          tension: 0,
          borderWidth: 2,
          pointRadius: 3,
          pointBackgroundColor: "#43A047",
          pointBorderColor: "transparent",
          borderColor: "#43A047",
          backgroundColor: "transparent",
          fill: true,
          data: [50, 40, 300, 220, 500, 250, 400, 230, 500],
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

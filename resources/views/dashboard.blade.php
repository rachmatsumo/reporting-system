@extends('layouts.user_type.auth')
@section('title', 'Dashboard')
@section('content')

<div class="container-fluid">
  
  <div class="row">
    <div class="col-xl-4 col-sm-6 mb-xl-0 mb-4">
      <div class="card">
        <div class="card-body p-3">
          <div class="row">
            <div class="col-8">
              <div class="numbers">
                <p class="text-sm mb-0 text-capitalize font-weight-bold">Laporan Minggu Ini</p>
                <h5 class="font-weight-bolder mb-0">
                  {{ $weekly }} 
                  @if ($weekTrend >= 0)
                    <span class="text-success text-sm font-weight-bolder">+{{ $weekTrend }}%</span>
                  @else
                    <span class="text-danger text-sm font-weight-bolder">{{ $weekTrend }}%</span>
                  @endif
                </h5>
              </div>
            </div>
            <div class="col-4 text-end">
              <div class="icon icon-shape bg-gradient-primary shadow text-center d-flex justify-content-center align-items-center border-radius-md">
                <i class="bi bi-file-earmark-post opacity-10" aria-hidden="true"></i>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-xl-4 col-sm-6 mb-xl-0 mb-4">
      <div class="card">
        <div class="card-body p-3">
          <div class="row">
            <div class="col-8">
              <div class="numbers">
                <p class="text-sm mb-0 text-capitalize font-weight-bold">Laporan Bulan Ini</p>
                <h5 class="font-weight-bolder mb-0">
                  {{ $monthly }}
                  @if ($weekTrend >= 0)
                    <span class="text-success text-sm font-weight-bolder">+{{ $monthTrend }}%</span>
                  @else
                    <span class="text-danger text-sm font-weight-bolder">{{ $monthTrend }}%</span>
                  @endif
                </h5>
              </div>
            </div>
            <div class="col-4 text-end">
              <div class="icon icon-shape bg-gradient-primary shadow text-center d-flex justify-content-center align-items-center border-radius-md">
                <i class="bi bi-calendar-week text-lg opacity-10" aria-hidden="true"></i>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-xl-4 col-sm-6 mb-xl-0 mb-4">
      <div class="card">
        <div class="card-body p-3">
          <div class="row">
            <div class="col-8">
              <div class="numbers">
                <p class="text-sm mb-0 text-capitalize font-weight-bold">Laporan Tahun Ini</p>
                <h5 class="font-weight-bolder mb-0">
                  {{ $yearly }}
                 @if ($weekTrend >= 0)
                    <span class="text-success text-sm font-weight-bolder">+{{ $yearTrend }}%</span>
                  @else
                    <span class="text-danger text-sm font-weight-bolder">{{ $yearTrend }}%</span>
                  @endif
                </h5>
              </div>
            </div>
            <div class="col-4 text-end">
              <div class="icon icon-shape bg-gradient-primary shadow text-center d-flex justify-content-center align-items-center border-radius-md">
                <i class="bi bi-folder2-open text-lg opacity-10" aria-hidden="true"></i>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    {{-- <div class="col-xl-3 col-sm-6">
      <div class="card">
        <div class="card-body p-3">
          <div class="row">
            <div class="col-8">
              <div class="numbers">
                <p class="text-sm mb-0 text-capitalize font-weight-bold">Sales</p>
                <h5 class="font-weight-bolder mb-0">
                  $103,430
                  <span class="text-success text-sm font-weight-bolder">+5%</span>
                </h5>
              </div>
            </div>
            <div class="col-4 text-end">
              <div class="icon icon-shape bg-gradient-primary shadow text-center border-radius-md">
                <i class="ni ni-cart text-lg opacity-10" aria-hidden="true"></i>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div> --}}
  </div> 
  
  <div class="row mt-4">
    <div class="col-lg-5 mb-lg-0 mb-4">
      <div class="card z-index-2">
        <div class="card-body p-3">
          <div class="bg-gradient-dark border-radius-lg py-3 pe-1 mb-3">
            <div class="chart">
              <canvas id="chart-bars" class="chart-canvas" height="170"></canvas>
            </div>
          </div>
          <h6 class="ms-2 mt-4 mb-0"> Laporan Hari Ini </h6>
          <p class="text-sm ms-2"> 
            @if ($dailyTrend >= 0)
              <i class="fa fa-arrow-up text-success"></i>
              <span class="font-weight-bold">{{ $dailyTrend }}% lebih banyak</span> dari kemarin
            @else
              <i class="fa fa-arrow-down text-danger"></i>
              <span class="font-weight-bold">{{ abs($dailyTrend) }}% lebih sedikit</span> dari kemarin
            @endif 
          </p>
          <div class="container border-radius-lg">
            <div class="row">
              <div class="col-6 py-3 ps-0">
                <div class="d-flex mb-2">
                  <div class="icon icon-shape icon-xxs shadow border-radius-sm bg-gradient-primary text-center me-2 d-flex align-items-center justify-content-center">
                    <i class="bi bi-check-circle fs-7 mb-0"></i>                                       
                  </div>
                  <p class="text-xs mt-1 mb-0 font-weight-bold">Submited</p>
                </div>
                <h4 class="font-weight-bolder">{{ $todaySubmitted }}</h4>
                <div class="progress w-75">
                  <div class="progress-bar bg-dark w-100" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
              </div>
              <div class="col-6 py-3 ps-0">
                <div class="d-flex mb-2">
                  <div class="icon icon-shape icon-xxs shadow border-radius-sm bg-gradient-info text-center me-2 d-flex align-items-center justify-content-center">
                    <i class="bi bi-card-checklist fs-7 mb-0"></i>
                  </div>
                  <p class="text-xs mt-1 mb-0 font-weight-bold">Draft</p>
                </div>
                <h4 class="font-weight-bolder">{{ $todayDraft }}</h4>
                <div class="progress w-75">
                  <div class="progress-bar bg-dark w-100" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
              </div>
              {{-- <div class="col-3 py-3 ps-0">
                <div class="d-flex mb-2">
                  <div class="icon icon-shape icon-xxs shadow border-radius-sm bg-gradient-warning text-center me-2 d-flex align-items-center justify-content-center">
                    <svg width="10px" height="10px" viewBox="0 0 43 36" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                      <title>credit-card</title>
                      <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                        <g transform="translate(-2169.000000, -745.000000)" fill="#FFFFFF" fill-rule="nonzero">
                          <g transform="translate(1716.000000, 291.000000)">
                            <g transform="translate(453.000000, 454.000000)">
                              <path class="color-background" d="M43,10.7482083 L43,3.58333333 C43,1.60354167 41.3964583,0 39.4166667,0 L3.58333333,0 C1.60354167,0 0,1.60354167 0,3.58333333 L0,10.7482083 L43,10.7482083 Z" opacity="0.593633743"></path>
                              <path class="color-background" d="M0,16.125 L0,32.25 C0,34.2297917 1.60354167,35.8333333 3.58333333,35.8333333 L39.4166667,35.8333333 C41.3964583,35.8333333 43,34.2297917 43,32.25 L43,16.125 L0,16.125 Z M19.7083333,26.875 L7.16666667,26.875 L7.16666667,23.2916667 L19.7083333,23.2916667 L19.7083333,26.875 Z M35.8333333,26.875 L28.6666667,26.875 L28.6666667,23.2916667 L35.8333333,23.2916667 L35.8333333,26.875 Z"></path>
                            </g>
                          </g>
                        </g>
                      </g>
                    </svg>
                  </div>
                  <p class="text-xs mt-1 mb-0 font-weight-bold">Sales</p>
                </div>
                <h4 class="font-weight-bolder">435$</h4>
                <div class="progress w-75">
                  <div class="progress-bar bg-dark w-30" role="progressbar" aria-valuenow="30" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
              </div>
              <div class="col-3 py-3 ps-0">
                <div class="d-flex mb-2">
                  <div class="icon icon-shape icon-xxs shadow border-radius-sm bg-gradient-danger text-center me-2 d-flex align-items-center justify-content-center">
                    <svg width="10px" height="10px" viewBox="0 0 40 40" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                      <title>settings</title>
                      <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                        <g transform="translate(-2020.000000, -442.000000)" fill="#FFFFFF" fill-rule="nonzero">
                          <g transform="translate(1716.000000, 291.000000)">
                            <g transform="translate(304.000000, 151.000000)">
                              <polygon class="color-background" opacity="0.596981957" points="18.0883333 15.7316667 11.1783333 8.82166667 13.3333333 6.66666667 6.66666667 0 0 6.66666667 6.66666667 13.3333333 8.82166667 11.1783333 15.315 17.6716667"></polygon>
                              <path class="color-background" d="M31.5666667,23.2333333 C31.0516667,23.2933333 30.53,23.3333333 30,23.3333333 C29.4916667,23.3333333 28.9866667,23.3033333 28.48,23.245 L22.4116667,30.7433333 L29.9416667,38.2733333 C32.2433333,40.575 35.9733333,40.575 38.275,38.2733333 L38.275,38.2733333 C40.5766667,35.9716667 40.5766667,32.2416667 38.275,29.94 L31.5666667,23.2333333 Z" opacity="0.596981957"></path>
                              <path class="color-background" d="M33.785,11.285 L28.715,6.215 L34.0616667,0.868333333 C32.82,0.315 31.4483333,0 30,0 C24.4766667,0 20,4.47666667 20,10 C20,10.99 20.1483333,11.9433333 20.4166667,12.8466667 L2.435,27.3966667 C0.95,28.7083333 0.0633333333,30.595 0.00333333333,32.5733333 C-0.0583333333,34.5533333 0.71,36.4916667 2.11,37.89 C3.47,39.2516667 5.27833333,40 7.20166667,40 C9.26666667,40 11.2366667,39.1133333 12.6033333,37.565 L27.1533333,19.5833333 C28.0566667,19.8516667 29.01,20 30,20 C35.5233333,20 40,15.5233333 40,10 C40,8.55166667 39.685,7.18 39.1316667,5.93666667 L33.785,11.285 Z"></path>
                            </g>
                          </g>
                        </g>
                      </g>
                    </svg>
                  </div>
                  <p class="text-xs mt-1 mb-0 font-weight-bold">Items</p>
                </div>
                <h4 class="font-weight-bolder">43</h4>
                <div class="progress w-75">
                  <div class="progress-bar bg-dark w-50" role="progressbar" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
              </div> --}}
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-lg-7">
      <div class="card z-index-2">
        <div class="card-header pb-0">
          <h6>Laporan Harian</h6>
          <p class="text-sm">Tren Laporan Harian</p>
          {{-- <p class="text-sm">
            @if ($dailyTrend >= 0)
              <i class="fa fa-arrow-up text-success"></i>
              <span class="font-weight-bold">{{ $dailyTrend }}% lebih banyak</span> dari kemarin
            @else
              <i class="fa fa-arrow-down text-danger"></i>
              <span class="font-weight-bold">{{ abs($dailyTrend) }}% lebih sedikit</span> dari kemarin
            @endif
          </p>  --}}
        </div>
        <div class="card-body p-3">
          <div class="chart">
            <canvas id="chart-line" class="chart-canvas" height="300"></canvas>
          </div>
        </div>
      </div>
    </div>
  </div> 

</div>

@endsection
@push('scripts')
  <script>
    window.onload = function() {
      // === Data bulan chart-bar (masih sama) ===
      const monthlyData = @json($chartData ?? []);

      // === Data harian chart-line (dinamis) ===
      const dailyLabels = @json($dailyLabels);
      const dailyData = @json($dailyData);


      var ctx = document.getElementById("chart-bars").getContext("2d");

      new Chart(ctx, {
        type: "bar",
        data: {
          labels: ["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"],
          datasets: [{
            label: "Laporan",
            tension: 0.4,
            borderWidth: 0,
            borderRadius: 4,
            borderSkipped: false,
            backgroundColor: "#fff",
            data: monthlyData,
            maxBarThickness: 6
          }, ],
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: {
              display: false,
            }
          }, 
          scales: {
            y: {
              grid: {
                drawBorder: false,
                display: false,
                drawOnChartArea: false,
                drawTicks: false,
              },
              ticks: {
                suggestedMin: 0,
                suggestedMax: 500,
                beginAtZero: true,
                padding: 15,
                font: {
                  size: 14,
                  family: "Open Sans",
                  style: 'normal',
                  lineHeight: 2
                },
                color: "#fff"
              },
            },
            x: {
              grid: {
                drawBorder: false,
                display: false,
                drawOnChartArea: false,
                drawTicks: false
              },
              ticks: {
                display: true,
                ticks: { color: "#fff" }
              },
            },
          },
        },
      });


      var ctx2 = document.getElementById("chart-line").getContext("2d");

      var gradientStroke1 = ctx2.createLinearGradient(0, 230, 0, 50);

      gradientStroke1.addColorStop(1, 'rgba(37, 228, 40, 0.2)');
      gradientStroke1.addColorStop(0.2, 'rgba(72,72,176,0.0)');
      gradientStroke1.addColorStop(0, 'rgba(203,12,159,0)'); //purple colors

      var gradientStroke2 = ctx2.createLinearGradient(0, 230, 0, 50);

      gradientStroke2.addColorStop(1, 'rgba(20,23,39,0.2)');
      gradientStroke2.addColorStop(0.2, 'rgba(72,72,176,0.0)');
      gradientStroke2.addColorStop(0, 'rgba(20,23,39,0)'); //purple colors

      new Chart(ctx2, {
        type: "line",
        data: {
          labels: dailyLabels,
          datasets: [{
              label: "Total Laporan",
              tension: 0.4,
              borderWidth: 0,
              pointRadius: 0,
              borderColor: "#0ad622ff",
              borderWidth: 3,
              backgroundColor: gradientStroke1,
              fill: true,
              data: dailyData,
              maxBarThickness: 6

            },
            // {
            //   label: "Websites",
            //   tension: 0.4,
            //   borderWidth: 0,
            //   pointRadius: 0,
            //   borderColor: "#3A416F",
            //   borderWidth: 3,
            //   backgroundColor: gradientStroke2,
            //   fill: true,
            //   data: [30, 90, 40, 140, 290, 290, 340, 230, 400],
            //   maxBarThickness: 6
            // },
          ],
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: {
              display: false,
            }
          },
          interaction: {
            intersect: false,
            mode: 'index',
          },
          scales: {
            y: {
              grid: {
                drawBorder: false,
                display: true,
                drawOnChartArea: true,
                drawTicks: false,
                borderDash: [5, 5]
              },
              ticks: {
                display: true,
                padding: 10,
                color: '#b2b9bf',
                font: {
                  size: 11,
                  family: "Open Sans",
                  style: 'normal',
                  lineHeight: 2
                },
              }
            },
            x: {
              grid: {
                drawBorder: false,
                display: false,
                drawOnChartArea: false,
                drawTicks: false,
                borderDash: [5, 5]
              },
              ticks: {
                display: true,
                color: '#b2b9bf',
                padding: 20,
                font: {
                  size: 11,
                  family: "Open Sans",
                  style: 'normal',
                  lineHeight: 2
                },
              }
            },
          },
        },
      });
    }
  </script>
@endpush


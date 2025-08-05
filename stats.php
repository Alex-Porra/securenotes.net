<?php
require_once './config/config.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo APP_NAME; ?> - Statistics</title>
    <meta name="description" content="Real-time statistics and analytics for SecureNotes - See how our secure note sharing service is being used.">
    <meta name="keywords" content="secure notes, encrypted sharing, self-destructing messages, password sharing">
    <!-- Open Graph -->
    <meta property="og:title" content="<?php echo APP_NAME; ?> - Statistics">
    <meta property="og:description" content="Real-time statistics and analytics for SecureNotes - See how our secure note sharing service is being used.">
    <meta property="og:image" content="<?php echo APP_URL; ?>/assets/SecureNotes-Icon-sm.png">
    <meta property="og:url" content="<?php echo APP_URL; ?>/stats/">
    <meta property="og:type" content="website">
    <?php include "./includes/head.php" ?>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Additional CSS -->
    <style>
        body.custom-body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%) !important;
            min-height: 100vh !important;
        }

        .custom-card {
            border-radius: 12px !important;
            border: none !important;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1) !important;
            transition: all 0.3s ease !important;
        }

        .custom-card:hover {
            transform: translateY(-2px) !important;
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.15) !important;
        }

        .stat-card {
            background: white !important;
            border-radius: 12px !important;
            padding: 1.5rem !important;
            margin-bottom: 1rem !important;
            border-left: 4px solid transparent !important;
        }

        .stat-card-primary {
            border-left-color: #007bff !important;
        }

        .stat-card-success {
            border-left-color: #28a745 !important;
        }

        .stat-card-warning {
            border-left-color: #ffc107 !important;
        }

        .stat-card-danger {
            border-left-color: #dc3545 !important;
        }

        .stat-card-info {
            border-left-color: #17a2b8 !important;
        }

        .stat-number {
            font-size: 2.5rem !important;
            font-weight: 700 !important;
            line-height: 1 !important;
            margin-bottom: 0.5rem !important;
        }

        .stat-label {
            color: #6c757d !important;
            font-size: 0.9rem !important;
            font-weight: 500 !important;
            text-transform: uppercase !important;
            letter-spacing: 0.5px !important;
        }

        .stat-change {
            font-size: 0.8rem !important;
            font-weight: 500 !important;
        }

        .stat-change.positive {
            color: #28a745 !important;
        }

        .stat-change.negative {
            color: #dc3545 !important;
        }

        .chart-container {
            position: relative !important;
            height: 300px !important;
            margin: 1rem 0 !important;
        }

        .loading-spinner {
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            height: 200px !important;
        }

        .refresh-btn {
            position: fixed !important;
            bottom: 2rem !important;
            right: 2rem !important;
            z-index: 1000 !important;
        }

        .health-indicator {
            width: 12px !important;
            height: 12px !important;
            border-radius: 50% !important;
            display: inline-block !important;
            margin-right: 0.5rem !important;
        }

        .health-healthy {
            background-color: #28a745 !important;
        }

        .health-warning {
            background-color: #ffc107 !important;
        }

        .health-error {
            background-color: #dc3545 !important;
        }

        .last-updated {
            position: absolute !important;
            top: 1rem !important;
            right: 1rem !important;
            font-size: 0.8rem !important;
            color: #6c757d !important;
        }
    </style>

</head>

<body class="custom-body">
    <!-- Navigation -->
    <?php include "./includes/nav.php" ?>


    <div class="container py-5">
        <!-- Header -->
        <div class="row mb-5">
            <div class="col-12">
                <div class="card custom-card">
                    <div class="card-body text-center p-4 position-relative">
                        <div class="last-updated" id="lastUpdated">
                            <i class="bi bi-clock me-1"></i>
                            Loading...
                        </div>
                        <div class="infoDiv">
                            <i class="bi bi-bar-chart display-4 text-primary mb-3"></i>
                            <h1 class="h2 fw-bold">Real-Time Statistics</h1>
                            <p class="text-muted mb-0">Live analytics and usage metrics for <?php echo APP_NAME; ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Loading State -->
        <div id="loadingState" class="loading-spinner">
            <div class="text-center">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading statistics...</span>
                </div>
                <p class="mt-3 text-muted">Loading statistics...</p>
            </div>
        </div>

        <!-- Error State -->
        <div id="errorState" class="d-none">
            <div class="row justify-content-center">
                <div class="col-lg-6">
                    <div class="card custom-card">
                        <div class="card-body text-center p-5">
                            <i class="bi bi-exclamation-triangle display-4 text-warning mb-3"></i>
                            <h3>Unable to Load Statistics</h3>
                            <p class="text-muted mb-4">There was an error loading the statistics. Please try again.</p>
                            <button onclick="loadStats()" class="btn btn-primary">
                                <i class="bi bi-arrow-clockwise me-2"></i>Retry
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Statistics -->
        <div id="statsContent" class="d-none">
            <!-- Overview Stats -->
            <div class="row mb-5">
                <div class="col-md-3">
                    <div class="stat-card stat-card-primary">
                        <div class="stat-number text-primary" id="totalNotes">0</div>
                        <div class="stat-label">Total Notes Created</div>
                        <div class="stat-change" id="totalNotesChange"></div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card stat-card-success">
                        <div class="stat-number text-success" id="activeNotes">0</div>
                        <div class="stat-label">Active Notes</div>
                        <div class="stat-change" id="activeNotesChange"></div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card stat-card-warning">
                        <div class="stat-number text-warning" id="totalAccesses">0</div>
                        <div class="stat-label">Total Accesses</div>
                        <div class="stat-change" id="totalAccessesChange"></div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card stat-card-info">
                        <div class="stat-number text-info" id="successRate">0%</div>
                        <div class="stat-label">Success Rate</div>
                        <div class="stat-change" id="successRateChange"></div>
                    </div>
                </div>
            </div>

            <!-- Usage Charts -->
            <div class="row mb-5">
                <div class="col-lg-8">
                    <div class="card custom-card">
                        <div class="card-body">
                            <h5 class="card-title">
                                <i class="bi bi-graph-up text-primary me-2"></i>
                                Daily Note Creation (Last 30 Days)
                            </h5>
                            <div class="chart-container">
                                <canvas id="dailyChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 mobile-fix">
                    <div class="card custom-card">
                        <div class="card-body">
                            <h5 class="card-title">
                                <i class="bi bi-pie-chart text-primary me-2"></i>
                                Expiry Types
                            </h5>
                            <div class="chart-container">
                                <canvas id="expiryChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Detailed Statistics -->
            <div class="row mb-5">
                <div class="col-lg-6">
                    <div class="card custom-card">
                        <div class="card-body">
                            <h5 class="card-title">
                                <i class="bi bi-clock-history text-primary me-2"></i>
                                Time Period Statistics
                            </h5>
                            <div class="table-responsive">
                                <table class="table table-borderless">
                                    <tbody>
                                        <tr>
                                            <td><strong>Last 24 Hours</strong></td>
                                            <td class="text-end" id="notes24h">0</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Last 7 Days</strong></td>
                                            <td class="text-end" id="notes7d">0</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Last 30 Days</strong></td>
                                            <td class="text-end" id="notes30d">0</td>
                                        </tr>
                                        <tr class="border-top">
                                            <td><strong>Average Lifetime</strong></td>
                                            <td class="text-end" id="avgLifetime">0</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6  mobile-fix">
                    <div class="card custom-card">
                        <div class="card-body">
                            <h5 class="card-title">
                                <i class="bi bi-shield-check text-primary me-2"></i>
                                Security & Usage
                            </h5>
                            <div class="table-responsive">
                                <table class="table table-borderless">
                                    <tbody>
                                        <tr>
                                            <td><strong>Notes with Passcode</strong></td>
                                            <td class="text-end" id="notesWithPasscode">0</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Passcode Usage Rate</strong></td>
                                            <td class="text-end" id="passcodeRate">0%</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Destruction Rate</strong></td>
                                            <td class="text-end" id="destructionRate">0%</td>
                                        </tr>
                                        <tr class="border-top">
                                            <td><strong>Failed Access Attempts</strong></td>
                                            <td class="text-end" id="failedAccesses">0</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- System Health -->
            <div class="row mb-5">
                <div class="col-12">
                    <div class="card custom-card">
                        <div class="card-body">
                            <h5 class="card-title">
                                <i class="bi bi-cpu text-primary me-2"></i>
                                System Health & Performance
                            </h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <h6 class="mb-3">Health Status</h6>
                                    <div id="healthStatus">
                                        <!-- Health indicators will be populated here -->
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <h6 class="mb-3">Performance Metrics</h6>
                                    <div id="performanceMetrics">
                                        <!-- Performance metrics will be populated here -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Refresh Button -->
    <!-- <button class="btn btn-primary btn-lg refresh-btn" onclick="loadStats()" title="Refresh Statistics">
        <i class="bi bi-arrow-clockwise"></i>
    </button> -->

    <!-- Footer -->
    <?php include "./includes/footer.php" ?>


    <!-- Statistics JavaScript -->
    <script>
        let dailyChart, expiryChart;
        let statsData = null;

        // Load statistics on page load
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOM loaded, initializing stats page...');

            // Add a small delay to ensure everything is ready
            setTimeout(() => {
                loadStats();
            }, 100);

            // Auto-refresh every 5 minutes
            setInterval(loadStats, 300000);
        });

        async function loadStats() {
            showLoading();

            try {
                console.log('Loading statistics from API...');
                const response = await fetch('/api/stats.php');
                console.log('Response status:', response.status);

                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }

                const data = await response.json();
                console.log('API Response:', data);

                if (data.success && data.stats) {
                    statsData = data.stats;
                    displayStats(statsData);
                    showContent();
                    hideoading();
                    console.log('Statistics loaded successfully');
                } else {
                    throw new Error(data.error || 'Invalid response format from API');
                }
            } catch (error) {
                console.error('Error loading statistics:', error);
                showError(error.message);
            }
        }

        function hideoading() {
            var elem = document.getElementById('loadingState');
            if (elem) {
                elem.parentNode.removeChild(elem);
            }
        }

        function showLoading() {
            document.getElementById('loadingState').classList.remove('d-none');
            document.getElementById('errorState').classList.add('d-none');
            document.getElementById('statsContent').classList.add('d-none');
        }

        function showError(errorMessage = 'Unknown error occurred') {
            document.getElementById('loadingState').classList.add('d-none');
            document.getElementById('errorState').classList.remove('d-none');
            document.getElementById('statsContent').classList.add('d-none');

            // Update error message
            const errorState = document.getElementById('errorState');
            const errorText = errorState.querySelector('p');
            if (errorText) {
                errorText.textContent = `Error: ${errorMessage}`;
            }
        }

        function showContent() {
            document.getElementById('loadingState').classList.add('d-none');
            document.getElementById('errorState').classList.add('d-none');
            document.getElementById('statsContent').classList.remove('d-none');
        }

        function displayStats(stats) {
            try {
                console.log('Displaying stats:', stats);

                // Update last updated time
                if (stats.generated_at) {
                    const lastUpdated = new Date(stats.generated_at).toLocaleString();
                    document.getElementById('lastUpdated').innerHTML =
                        `<i class="bi bi-clock me-1"></i>Updated: ${lastUpdated}`;
                }

                // Update overview stats with fallbacks
                document.getElementById('totalNotes').textContent = formatNumber(stats.total_notes_created || 0);
                document.getElementById('activeNotes').textContent = formatNumber(stats.active_notes || 0);
                document.getElementById('totalAccesses').textContent = formatNumber(stats.total_access_attempts || 0);
                document.getElementById('successRate').textContent = (stats.success_rate || 0) + '%';

                // Update time period stats
                document.getElementById('notes24h').textContent = formatNumber(stats.notes_24h || 0);
                document.getElementById('notes7d').textContent = formatNumber(stats.notes_7d || 0);
                document.getElementById('notes30d').textContent = formatNumber(stats.notes_30d || 0);
                document.getElementById('avgLifetime').textContent = formatDuration(stats.average_note_lifetime_seconds || 0);

                // Update security stats
                document.getElementById('notesWithPasscode').textContent = formatNumber(stats.notes_with_passcode || 0);
                document.getElementById('passcodeRate').textContent = (stats.passcode_usage_rate || 0) + '%';
                document.getElementById('destructionRate').textContent = (stats.destruction_rate || 0) + '%';
                document.getElementById('failedAccesses').textContent = formatNumber(stats.failed_accesses || 0);

                // Update charts
                if (stats.daily_creation_stats) {
                    updateDailyChart(stats.daily_creation_stats);
                }
                if (stats.expiry_types) {
                    updateExpiryChart(stats.expiry_types);
                }

                // Update health status and performance
                if (stats.health_status) {
                    updateHealthStatus(stats.health_status);
                }
                if (stats.performance) {
                    updatePerformanceMetrics(stats.performance);
                }

                console.log('Stats display completed successfully');
            } catch (error) {
                console.error('Error displaying stats:', error);
                showError('Failed to display statistics: ' + error.message);
            }
        }

        function updateDailyChart(dailyStats) {
            const ctx = document.getElementById('dailyChart').getContext('2d');

            // Prepare data for last 30 days
            const dates = [];
            const counts = [];
            const now = new Date();

            for (let i = 29; i >= 0; i--) {
                const date = new Date(now);
                date.setDate(date.getDate() - i);
                const dateStr = date.toISOString().split('T')[0];
                dates.push(date.toLocaleDateString('en-US', {
                    month: 'short',
                    day: 'numeric'
                }));
                counts.push(dailyStats[dateStr] || 0);
            }

            if (dailyChart) {
                dailyChart.destroy();
            }

            dailyChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: dates,
                    datasets: [{
                        label: 'Notes Created',
                        data: counts,
                        borderColor: '#007bff',
                        backgroundColor: 'rgba(0, 123, 255, 0.1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1
                            }
                        },
                        x: {
                            ticks: {
                                maxTicksLimit: 10
                            }
                        }
                    }
                }
            });
        }

        function updateExpiryChart(expiryTypes) {
            const ctx = document.getElementById('expiryChart').getContext('2d');

            const labels = [];
            const data = [];
            const colors = ['#007bff', '#28a745', '#ffc107', '#dc3545', '#17a2b8'];

            Object.entries(expiryTypes).forEach(([type, count]) => {
                labels.push(type.charAt(0).toUpperCase() + type.slice(1));
                data.push(count);
            });

            if (expiryChart) {
                expiryChart.destroy();
            }

            expiryChart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: labels,
                    datasets: [{
                        data: data,
                        backgroundColor: colors.slice(0, labels.length),
                        borderWidth: 2,
                        borderColor: '#fff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                padding: 15,
                                usePointStyle: true
                            }
                        }
                    }
                }
            });
        }

        function updateHealthStatus(healthStatus) {
            const container = document.getElementById('healthStatus');
            container.innerHTML = '';

            Object.entries(healthStatus).forEach(([key, status]) => {
                const indicator = document.createElement('div');
                indicator.className = 'mb-2';

                let indicatorClass = 'health-healthy';
                let statusText = status;

                if (typeof status === 'boolean') {
                    indicatorClass = status ? 'health-healthy' : 'health-error';
                    statusText = status ? 'Available' : 'Unavailable';
                } else if (status === 'not_configured') {
                    indicatorClass = 'health-warning';
                    statusText = 'Not Configured';
                } else if (status === 'unavailable') {
                    indicatorClass = 'health-error';
                    statusText = 'Unavailable';
                }

                indicator.innerHTML = `
                    <span class="health-indicator ${indicatorClass}"></span>
                    <strong>${formatLabel(key)}:</strong> ${statusText}
                `;
                container.appendChild(indicator);
            });
        }

        function updatePerformanceMetrics(performance) {
            const container = document.getElementById('performanceMetrics');
            container.innerHTML = '';

            Object.entries(performance).forEach(([key, value]) => {
                const metric = document.createElement('div');
                metric.className = 'mb-2';

                let formattedValue = value;
                if (key.includes('memory')) {
                    formattedValue = value + ' MB';
                } else if (key.includes('time')) {
                    formattedValue = value + ' ms';
                }

                metric.innerHTML = `
                    <strong>${formatLabel(key)}:</strong> ${formattedValue}
                `;
                container.appendChild(metric);
            });
        }

        function formatNumber(num) {
            if (num >= 1000000) {
                return (num / 1000000).toFixed(1) + 'M';
            } else if (num >= 1000) {
                return (num / 1000).toFixed(1) + 'K';
            }
            return num.toLocaleString();
        }

        function formatDuration(seconds) {
            if (!seconds || seconds === 0) return '0s';

            const hours = Math.floor(seconds / 3600);
            const minutes = Math.floor((seconds % 3600) / 60);
            const secs = seconds % 60;

            if (hours > 24) {
                const days = Math.floor(hours / 24);
                return `${days}d ${hours % 24}h`;
            } else if (hours > 0) {
                return `${hours}h ${minutes}m`;
            } else if (minutes > 0) {
                return `${minutes}m ${secs}s`;
            } else {
                return `${secs}s`;
            }
        }

        function formatLabel(key) {
            return key
                .replace(/_/g, ' ')
                .replace(/\b\w/g, l => l.toUpperCase())
                .replace('Php', 'PHP')
                .replace('Mb', 'MB')
                .replace('Ms', 'MS');
        }

        // Add keyboard shortcut for refresh (R key)
        document.addEventListener('keydown', function(e) {
            if (e.key === 'r' || e.key === 'R') {
                if (!e.ctrlKey && !e.metaKey) {
                    e.preventDefault();
                    loadStats();
                }
            }
        });

        // Add page visibility API to pause/resume auto-refresh
        document.addEventListener('visibilitychange', function() {
            if (document.hidden) {
                // Page is hidden, could pause auto-refresh here
            } else {
                // Page is visible, could resume auto-refresh or refresh immediately
                loadStats();
            }
        });

        // Export stats data function (for debugging or analysis)
        function exportStats() {
            if (statsData) {
                const dataStr = JSON.stringify(statsData, null, 2);
                const dataBlob = new Blob([dataStr], {
                    type: 'application/json'
                });
                const url = URL.createObjectURL(dataBlob);
                const link = document.createElement('a');
                link.href = url;
                link.download = `securenotes-stats-${new Date().toISOString().split('T')[0]}.json`;
                link.click();
                URL.revokeObjectURL(url);
            }
        }

        // Add export button functionality (optional)
        function addExportButton() {
            const refreshBtn = document.querySelector('.refresh-btn');
            const exportBtn = document.createElement('button');
            exportBtn.className = 'btn btn-outline-primary btn-lg';
            exportBtn.style.cssText = 'position: fixed; bottom: 2rem; right: 6rem; z-index: 1000;';
            exportBtn.innerHTML = '<i class="bi bi-download"></i>';
            exportBtn.title = 'Export Statistics Data';
            exportBtn.onclick = exportStats;
            document.body.appendChild(exportBtn);
        }

        // Uncomment to add export functionality
        // document.addEventListener('DOMContentLoaded', addExportButton);
    </script>
</body>

</html>
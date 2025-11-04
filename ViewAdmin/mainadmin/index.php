<?php
session_start();
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home - PharmaCare Dashboard</title>
    <link rel="stylesheet" href="../cssadmin/index.css">
</head>
<body>
<?php include 'navbar.php';?>

    <main class="main-content">
        <!-- Slider -->
        <div class="slider-container">
            <div class="slider">
                <div class="slide active" style="background: linear-gradient(135deg, #3b82f6, #1d4ed8);">
                    <div class="slide-content">
                        <h3>Welcome to PharmaCare</h3>
                        <p>Sistem manajemen farmasi terpadu untuk kemudahan pengelolaan obat</p>
                    </div>
                </div>
                <div class="slide" style="background: linear-gradient(135deg, #10b981, #047857);">
                    <div class="slide-content">
                        <h3>Kelola Stok Dengan Mudah</h3>
                        <p>Monitor stok obat secara real-time dan dapatkan alert otomatis</p>
                    </div>
                </div>
                <div class="slide" style="background: linear-gradient(135deg, #8b5cf6, #6d28d9);">
                    <div class="slide-content">
                        <h3>Transaksi Cepat & Akurat</h3>
                        <p>Catat transaksi masuk dan keluar dengan sistem yang terintegrasi</p>
                    </div>
                </div>
            </div>
            <div class="slider-dots">
                <span class="dot active" data-slide="0"></span>
                <span class="dot" data-slide="1"></span>
                <span class="dot" data-slide="2"></span>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="stats-grid">
            <div class="stat-card red">
                <div class="stat-info">
                    <p class="stat-label">Stock Alerts</p>
                    <p class="stat-value">3</p>
                </div>
                <div class="stat-icon red-bg">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/>
                        <path d="M13.73 21a2 2 0 0 1-3.46 0"/>
                    </svg>
                </div>
            </div>

            <div class="stat-card blue">
                <div class="stat-info">
                    <p class="stat-label">Today's Transaction</p>
                    <p class="stat-value">24</p>
                </div>
                <div class="stat-icon blue-bg">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="9" cy="21" r="1"/>
                        <circle cx="20" cy="21" r="1"/>
                        <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/>
                    </svg>
                </div>
            </div>

            <div class="stat-card green">
                <div class="stat-info">
                    <p class="stat-label">User Online</p>
                    <p class="stat-value">8</p>
                </div>
                <div class="stat-icon green-bg">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                        <circle cx="9" cy="7" r="4"/>
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                    </svg>
                </div>
            </div>

            <div class="stat-card purple">
                <div class="stat-info">
                    <p class="stat-label">Total Medicine</p>
                    <p class="stat-value">1,247</p>
                </div>
                <div class="stat-icon purple-bg">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Stock Alerts -->
        <div class="card">
            <div class="card-header">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/>
                    <path d="M13.73 21a2 2 0 0 1-3.46 0"/>
                </svg>
                <h3>Stock Alerts</h3>
            </div>
            <div class="alert-list">
                <div class="alert-item">
                    <div class="alert-info">
                        <p class="alert-name">Paracetamol 500mg</p>
                        <p class="alert-min">Min: 100 unit</p>
                    </div>
                    <div class="alert-status">
                        <p class="alert-stock low">50 unit</p>
                        <span class="badge low">Low Stock</span>
                    </div>
                </div>
                <div class="alert-item">
                    <div class="alert-info">
                        <p class="alert-name">Amoxicillin 500mg</p>
                        <p class="alert-min">Min: 100 unit</p>
                    </div>
                    <div class="alert-status">
                        <p class="alert-stock critical">20 unit</p>
                        <span class="badge critical">Critical</span>
                    </div>
                </div>
                <div class="alert-item">
                    <div class="alert-info">
                        <p class="alert-name">Vitamin B Complex</p>
                        <p class="alert-min">Min: 100 unit</p>
                    </div>
                    <div class="alert-status">
                        <p class="alert-stock low">80 unit</p>
                        <span class="badge low">Low Stock</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Chart Transaction -->
        <div class="card">
            <div class="card-header">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/>
                </svg>
                <h3>Transaction Chart (Weekly)</h3>
            </div>
            <canvas id="transactionChart"></canvas>
        </div>

        <!-- Recent Transactions -->
        <div class="card">
            <div class="card-header">
                <h3>Recent Transactions</h3>
            </div>
            <div class="table-container">
                <table class="transaction-table">
                    <thead>
                        <tr>
                            <th>Transaction ID</th>
                            <th>Type</th>
                            <th>Item</th>
                            <th>Quantity</th>
                            <th>Date</th>
                            <th>Partner</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="td-bold">TRX001</td>
                            <td><span class="badge-type in">Masuk</span></td>
                            <td>Paracetamol 500mg</td>
                            <td>100 unit</td>
                            <td class="td-gray">16 Okt 2024</td>
                            <td class="td-gray">PT Pharma Indo</td>
                        </tr>
                        <tr>
                            <td class="td-bold">TRX002</td>
                            <td><span class="badge-type out">Keluar</span></td>
                            <td>Amoxicillin 500mg</td>
                            <td>50 unit</td>
                            <td class="td-gray">16 Okt 2024</td>
                            <td class="td-gray">Apotek Sehat</td>
                        </tr>
                        <tr>
                            <td class="td-bold">TRX003</td>
                            <td><span class="badge-type in">Masuk</span></td>
                            <td>Vitamin C 1000mg</td>
                            <td>200 unit</td>
                            <td class="td-gray">15 Okt 2024</td>
                            <td class="td-gray">CV Medika</td>
                        </tr>
                        <tr>
                            <td class="td-bold">TRX004</td>
                            <td><span class="badge-type out">Keluar</span></td>
                            <td>Antasida 500mg</td>
                            <td>30 unit</td>
                            <td class="td-gray">15 Okt 2024</td>
                            <td class="td-gray">Klinik Pratama</td>
                        </tr>
                        <tr>
                            <td class="td-bold">TRX005</td>
                            <td><span class="badge-type in">Masuk</span></td>
                            <td>Ibuprofen 400mg</td>
                            <td>150 unit</td>
                            <td class="td-gray">14 Okt 2024</td>
                            <td class="td-gray">PT Pharma Indo</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="../jsadmin/home.js"></script>
</body>
</html>
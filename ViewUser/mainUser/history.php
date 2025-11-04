<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Order History - PharmaCare</title>
  <link rel="stylesheet" href="../cssuser/history.css">
</head>
<body>
    <?php include 'navbar.php'; ?>
  <div class="container">
    <h1>Your Order History</h1>

      <!-- FILTER PILLS -->
      <div class="status-filter" id="statusFilter">
        <!-- default aktif: Dikemas -->
         <button class="pill active" data-filter="semua">Semua</button>
        <button class="pill" data-filter="dikemas">Dikemas</button>
        <button class="pill" data-filter="dikirim">Dikirim</button>
        <button class="pill" data-filter="selesai">Selesai</button>
        <button class="pill" data-filter="dibatalkan">Dibatalkan</button>
      </div>

      <!-- LIST PESANAN (CARD STYLE) -->
      <div class="order-list" id="orderList">
        <!-- Contoh 1 -->
        <div class="order-card" data-status="dikemas">
          <div class="order-header">
            <span class="order-id">No. Transaksi: TRX-001</span>
            <span class="order-status pill-badge dikemas">Dikemas</span>
          </div>
          <div class="order-body">
            <img src="../assets/AlparaObat.jpeg" alt="Alpara">
            <div class="order-info">
              <h4>Alpara Antacid</h4>
              <p>Qty: 2</p>
              <p class="price">Rp 28.000</p>
              <p class="date">17 Okt 2025</p>
            </div>
          </div>
          <div class="order-footer">
            <span class="total">Total: <strong>Rp 56.000</strong></span>
            <div class="btns">
              <button class="btn-outline">Lihat Detail</button>
              <button class="btn-primary">Beli Lagi</button>
            </div>
          </div>
        </div>

        <!-- Contoh 2 -->
        <div class="order-card" data-status="dikirim">
          <div class="order-header">
            <span class="order-id">No. Transaksi: TRX-002</span>
            <span class="order-status pill-badge dikirim">Dikirim</span>
          </div>
          <div class="order-body">
            <img src="../assets/balsem.webp" alt="Balsem Geliga">
            <div class="order-info">
              <h4>Balsem Geliga</h4>
              <p>Qty: 1</p>
              <p class="price">Rp 24.000</p>
              <p class="date">15 Okt 2025</p>
            </div>
          </div>
          <div class="order-footer">
            <span class="total">Total: <strong>Rp 24.000</strong></span>
            <div class="btns">
              <button class="btn-outline">Lihat Detail</button>
              <button class="btn-primary">Beli Lagi</button>
            </div>
          </div>
        </div>

        <!-- Contoh 3 -->
        <div class="order-card" data-status="selesai">
          <div class="order-header">
            <span class="order-id">No. Transaksi: TRX-003</span>
            <span class="order-status pill-badge selesai">Selesai</span>
          </div>
          <div class="order-body">
            <img src="../assets/ObhCombi.png" alt="OBH Combi">
            <div class="order-info">
              <h4>OBH Combi Syrup</h4>
              <p>Qty: 1</p>
              <p class="price">Rp 35.000</p>
              <p class="date">13 Okt 2025</p>
            </div>
          </div>
          <div class="order-footer">
            <span class="total">Total: <strong>Rp 35.000</strong></span>
            <div class="btns">
              <button class="btn-outline">Lihat Detail</button>
              <button class="btn-primary">Beli Lagi</button>
            </div>
          </div>
        </div>

        <!-- Contoh 4 -->
        <div class="order-card" data-status="dibatalkan">
          <div class="order-header">
            <span class="order-id">No. Transaksi: TRX-004</span>
            <span class="order-status pill-badge dibatalkan">Dibatalkan</span>
          </div>
          <div class="order-body">
            <img src="../assets/bodrex.png" alt="Bodrex Tablet">
            <div class="order-info">
              <h4>Bodrex Tablet</h4>
              <p>Qty: 1</p>
              <p class="price">Rp 18.000</p>
              <p class="date">10 Okt 2025</p>
            </div>
          </div>
          <div class="order-footer">
            <span class="total">Total: <strong>Rp 18.000</strong></span>
            <div class="btns">
              <button class="btn-outline">Lihat Detail</button>
              <button class="btn-primary">Beli Lagi</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <?php include 'footer.php'; ?>
 <script src="../jsUser/history.js"></script>

</body>
</html>

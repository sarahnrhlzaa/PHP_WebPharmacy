<?php
// session_start();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data User - PharmaCare</title>
    <link rel="stylesheet" href="../cssadmin/user.css">
</head>
<body>
<?php include 'navbar.php';?>
    <div class="container">
        <h1 class="page-title">Data User</h1>
        
        <div class="table-controls">
            <div class="search-box">
                <input type="text" id="searchInput" placeholder="Cari user..." onkeyup="searchUsers()">
            </div>
            <button class="refresh-btn" onclick="refreshData()">🔄 Refresh</button>
        </div>

        <div class="table-wrapper">
            <table id="userTable">
                <thead>
                    <tr>
                        <th>User ID</th>
                        <th>Username</th>
                        <th>Nama Lengkap</th>
                        <th>Email</th>
                        <th>No. Telepon</th>
                        <th>Jenis Kelamin</th>
                        <th>Kota</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody id="userTableBody">
                    <tr>
                        <td colspan="8" style="text-align: center; padding: 40px;">
                            Loading...
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    
    <script src="../jsadmin/user.js"></script>
</body>
</html>
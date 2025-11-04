<?php
session_start();
require_once '../../Connection/connect.php';

header('Content-Type: text/plain');

$action = $_POST['action'] ?? '';

switch($action) {
    case 'delete':
        deleteUser();
        break;
    case 'list':
        listUsers();
        break;
    default:
        echo 'error|Invalid action';
}

function deleteUser() {
    global $conn;
    
    $user_id = $_POST['user_id'];
    
    $stmt = $conn->prepare("DELETE FROM users WHERE user_id = ?");
    $stmt->bind_param("s", $user_id);
    
    if ($stmt->execute()) {
        echo 'success|User berhasil dihapus!';
    } else {
        echo 'error|Gagal menghapus user: ' . $conn->error;
    }
    
    $stmt->close();
}

function listUsers() {
    global $conn;
    
    $keyword = isset($_POST['search']) ? trim($_POST['search']) : '';
    
    if (!empty($keyword)) {
        $keyword = "%{$keyword}%";
        $stmt = $conn->prepare("SELECT user_id, username, full_name, email, phone_number, 
                               birth_date, gender, city, province, address 
                               FROM users 
                               WHERE user_id LIKE ? OR username LIKE ? OR full_name LIKE ? 
                               OR email LIKE ? OR city LIKE ? OR province LIKE ?
                               ORDER BY user_id ASC");
        $stmt->bind_param("ssssss", $keyword, $keyword, $keyword, $keyword, $keyword, $keyword);
        $stmt->execute();
        $result = $stmt->get_result();
    } else {
        $sql = "SELECT user_id, username, full_name, email, phone_number, birth_date, 
                gender, city, province, address 
                FROM users ORDER BY user_id ASC";
        $result = $conn->query($sql);
    }
    
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            echo '<tr>
                    <td>' . htmlspecialchars($row['user_id']) . '</td>
                    <td>' . htmlspecialchars($row['username']) . '</td>
                    <td>' . htmlspecialchars($row['full_name']) . '</td>
                    <td>' . htmlspecialchars($row['email']) . '</td>
                    <td>' . htmlspecialchars($row['phone_number']) . '</td>
                    <td>' . htmlspecialchars($row['gender']) . '</td>
                    <td>' . htmlspecialchars($row['city']) . '</td>
                    <td>
                        <div class="action-buttons">
                            <button class="btn-delete" onclick="confirmDelete(\'' . $row['user_id'] . '\', \'' . htmlspecialchars($row['username']) . '\')">🗑️ Hapus</button>
                        </div>
                    </td>
                </tr>';
        }
    } else {
        echo '<tr><td colspan="8" style="text-align: center; padding: 40px; color: #999;">
                ' . (empty($keyword) ? 'Tidak ada data user' : 'Tidak ada hasil pencarian') . '
              </td></tr>';
    }
    
    if(isset($stmt)) $stmt->close();
}
?>
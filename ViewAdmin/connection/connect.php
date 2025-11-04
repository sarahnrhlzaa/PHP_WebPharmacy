<?php
function getConnection(): mysqli {
  static $conn = null;
  if ($conn instanceof mysqli) return $conn;

  // GANTI sesuai kredensial DB kamu
  $conn = new mysqli('localhost','root','','webpharmacy');
  if ($conn->connect_error) { die('DB connection error: '.$conn->connect_error); }
  $conn->set_charset('utf8mb4');
  return $conn;
}

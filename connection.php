<?php
// Check for login
if (!isset($_SESSION['username']) || empty($_SESSION['username'])) {
    header("location: login.php");
    exit;
  }
  
$dsn = "mysql:host=localhost;dbname=casuscafe";
$username = "root";
$password = "";
try {
  $pdo = new PDO($dsn, $username, $password);
} catch (PDOException $e) {
  die("Error connecting to the database: " . $e->getMessage());
}
<?php
  
$dsn = "mysql:host=localhost;dbname=casuscafe";
$username = "root";
$password = "";
try {
  $pdo = new PDO($dsn, $username, $password);
} catch (PDOException $e) {
  die("Error connecting to the database: " . $e->getMessage());
}
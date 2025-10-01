<?php
$host = "localhost"; // Change this if your database host is different
$dbname = "dbjd87gws8vl2f";
$username = "uasxxqbztmxwm";
$password = "wss863wqyhal";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>

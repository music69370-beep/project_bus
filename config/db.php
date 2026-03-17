<?php
$host = "localhost";
$dbname = "transport_management_system";
$username = "root";
$password = ""; 

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // ເພີ່ມບັນທັດນີ້: ກຳນົດເວລາໃຫ້ເປັນຂອງລາວ/ໄທ (+07:00)
    $conn->exec("SET time_zone = '+07:00'"); 
    
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>
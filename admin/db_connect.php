<?php
// admin/db_connect.php

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "loja_virtual";

// Criar a conexão
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar a conexão
if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}
?>

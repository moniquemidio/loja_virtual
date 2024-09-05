<?php
// admin/delete_product.php
include 'db_connect.php';

$produto_id = $_GET['id'];

// Excluir produto
$stmt = $conn->prepare("DELETE FROM produtos WHERE id = ?");
$stmt->bind_param("i", $produto_id);

if ($stmt->execute()) {
    header("Location: list_products.php");
    exit;
} else {
    echo "Erro ao excluir produto: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>

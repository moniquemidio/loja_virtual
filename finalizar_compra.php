<?php
session_start();
include 'admin/db_connect.php';

// Verificar se o carrinho não está vazio
if (empty($_SESSION['cart'])) {
    header('Location: cart.php');
    exit();
}

// Verificar se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit();
}

// Iniciar uma transação
$conn->begin_transaction();

try {
    $user_id = $_SESSION['usuario_id'];
    $total = 0;

    // Inserir o pedido
    $stmt = $conn->prepare("INSERT INTO pedidos (usuario_id, data_pedido, total) VALUES (?, NOW(), ?)");
    $stmt->bind_param("id", $user_id, $total);
    $stmt->execute();
    $pedido_id = $stmt->insert_id;

    // Inserir itens do pedido
    $stmt = $conn->prepare("INSERT INTO itens_pedido (pedido_id, produto_id, quantidade, preco) VALUES (?, ?, ?, ?)");
    
    foreach ($_SESSION['cart'] as $id => $item) {
        $item_total = $item['preco'] * $item['quantidade'];
        $stmt->bind_param("iiid", $pedido_id, $id, $item['quantidade'], $item['preco']);
        $stmt->execute();
        $total += $item_total;
    }

    // Atualizar o total do pedido
    $stmt = $conn->prepare("UPDATE pedidos SET total = ? WHERE id = ?");
    $stmt->bind_param("di", $total, $pedido_id);
    $stmt->execute();

    // Confirmar a transação
    $conn->commit();

    // Limpar o carrinho
    unset($_SESSION['cart']);

    // Redirecionar para a página de confirmação de pagamento
    header('Location: confirmacao_pagamento.php');
    exit();
} catch (Exception $e) {
    // Reverter a transação em caso de erro
    $conn->rollback();
    echo "Erro ao finalizar a compra: " . $e->getMessage();
    exit();
}

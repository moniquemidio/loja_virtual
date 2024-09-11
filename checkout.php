<?php
session_start();
include 'db_connect.php'; // Ajuste o caminho se necessário

// Processar a finalização da compra
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario_id = isset($_SESSION['usuario_id']) ? $_SESSION['usuario_id'] : null;
    $cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : array();
    $total = 0;

    // Verificar se o carrinho não está vazio
    if (!empty($cart)) {
        // Inserir pedido na tabela de pedidos
        $sql = "INSERT INTO pedidos (usuario_id, total, data_pedido) VALUES (?, ?, NOW())";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("id", $usuario_id, $total);
        $stmt->execute();
        $pedido_id = $stmt->insert_id; // Obter o ID do pedido inserido

        // Inserir itens do pedido
        $sql = "INSERT INTO itens_pedido (pedido_id, produto_id, quantidade, preco) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);

        foreach ($cart as $id => $item) {
            $preco = $item['preco'];
            $quantidade = $item['quantidade'];
            $total += $preco * $quantidade;
            $stmt->bind_param("iiid", $pedido_id, $id, $quantidade, $preco);
            $stmt->execute();
        }

        // Atualizar total do pedido
        $sql = "UPDATE pedidos SET total = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("di", $total, $pedido_id);
        $stmt->execute();

        // Limpar o carrinho
        unset($_SESSION['cart']);
        unset($_SESSION['cart_count']);

        // Redirecionar para a página de confirmação
        header('Location: confirmacao_pagamento.php');
        exit();
    } else {
        header('Location: cart.php');
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Finalizar Compra</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <a class="navbar-brand" href="index.php">Loja Virtual</a>
    <div class="collapse navbar-collapse">
        <ul class="navbar-nav mr-auto">
            <li class="nav-item">
                <a class="nav-link" href="index.php">Home</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="cadastrar.php">Cadastrar</a>
            </li>
        </ul>
        <ul class="navbar-nav ml-auto">
            <?php if (isset($_SESSION['usuario_id'])): ?>
                <li class="nav-item">
                    <span class="navbar-text">Bem-vindo, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</span>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="logout.php">
                        <i class="fas fa-user"></i> Sair
                    </a>
                </li>
            <?php else: ?>
                <li class="nav-item">
                    <a class="nav-link" href="login.php">Login</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="cadastrar.php">Cadastro</a>
                </li>
            <?php endif; ?>
            <li class="nav-item">
                <a class="nav-link" href="cart.php">
                    <i class="fas fa-shopping-cart"></i> Carrinho
                    <?php if (isset($_SESSION['cart_count']) && $_SESSION['cart_count'] > 0): ?>
                        <span class="badge badge-primary"><?php echo htmlspecialchars($_SESSION['cart_count']); ?></span>
                    <?php endif; ?>
                </a>
            </li>
        </ul>
    </div>
</nav>

<div class="container mt-4">
    <h2>Finalizar Compra</h2>
    <form action="checkout.php" method="post">
        <p>Você está prestes a finalizar a compra. Clique em "Confirmar" para concluir.</p>
        <button type="submit" class="btn btn-success">Confirmar</button>
    </form>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listar Produtos</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    header("Location: login.php");
    exit;
}
?>

<body>
    <div class="container mt-4">
        <h2>Listar Produtos</h2>
        <a href="create_product.php" class="btn btn-primary mb-3">Criar Novo Produto</a>
        <div class="row">
            <?php
            include 'db_connect.php';

            $result = $conn->query("SELECT * FROM produtos");

            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo '<div class="col-md-4 mb-4">';
                    echo '<div class="card">';
                    echo '<img src="uploads/' . $row['foto'] . '" class="card-img-top" alt="' . $row['nome'] . '">';
                    echo '<div class="card-body">';
                    echo '<h5 class="card-title">' . $row['nome'] . '</h5>';
                    echo '<p class="card-text">' . $row['descricao'] . '</p>';
                    echo '<p class="card-text">R$ ' . number_format($row['preco'], 2, ',', '.') . '</p>';
                    echo '<a href="edit_product.php?id=' . $row['id'] . '" class="btn btn-warning">Editar</a>';
                    echo '<a href="delete_product.php?id=' . $row['id'] . '" class="btn btn-danger">Excluir</a>';
                    echo '</div>';
                    echo '</div>';
                    echo '</div>';
                }
            } else {
                echo '<p>Nenhum produto encontrado.</p>';
            }

            $conn->close();
            ?>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

<?php

// Inclui o arquivo de autenticação para garantir que o utilizador está logado
require '../API/auth.php';

// Verifica se o utilizador está autenticado (existe na sessão)
if (!isset($_SESSION["user"])) {
    // Se não estiver autenticado, redireciona para a página de login
    header("Location: login.php");
    exit();
}

// Inclui o arquivo de conexão com o banco de dados
require '../API/db.php';

// Prepara uma query SQL para apagar todos os itens do carrinho do utilizador logado
$sql = $con->prepare("DELETE FROM carrinho WHERE userId = ?");

// Obtém o ID do usuário da sessão
$userId = $_SESSION["user"]["id"];

// Associa o parâmetro da query (userId) ao valor do utilizador logado
$sql->bind_param("i", $userId);

// Executa a query (remove os itens do carrinho)
$sql->execute();

// Fecha o statement
$sql->close();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Thank You</title>
    <!-- Importa o CSS do Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <!-- Navbar copiada do index.php para navegação -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom">
        <div class="container">
            <a class="navbar-brand text-danger fw-bold" href="#">Loja Online</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <?php 
                    // Se a função isAdmin existir e retornar true, mostra o link para a área de administração
                    if(function_exists('isAdmin') && isAdmin()){ ?>
                        <li class="nav-item">
                            <a class="nav-link" href="areaadmin.php">Área de Administração</a>
                        </li>
                    <?php } ?>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Página Inicial</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="cart.php">
                            <!-- Ícone do carrinho usando SVG -->
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-cart" viewBox="0 0 16 16" style="margin-right: 4px;">
                                <path d="M0 1.5A.5.5 0 0 1 .5 1h1a.5.5 0 0 1 .485.379L2.89 5H14.5a.5.5 0 0 1 .491.592l-1.5 8A.5.5 0 0 1 13 14H4a.5.5 0 0 1-.491-.408L1.01 2H.5a.5.5 0 0 1-.5-.5zm3.14 4l1.25 6.5h7.22l1.25-6.5H3.14zM5 12a2 2 0 1 0 4 0H5z"/>
                            </svg>
                            Carrinho
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <!-- Conteúdo centralizado verticalmente e horizontalmente -->
    <div class="container vh-100 d-flex justify-content-center align-items-center">
        <div class="col-md-5">
            <div class="card shadow p-4 border-0">
                <!-- Mensagem de agradecimento pela encomenda -->
                <h1 class="mb-4 text-center text-danger">Obrigado pela sua encomenda!</h1>
                <!-- Botão para voltar à página inicial -->
                <form action="index.php" class="text-center">
                    <button type="submit" class="btn btn-danger w-100">Homepage</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>

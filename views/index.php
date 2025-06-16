<?php
// Inicia a sessão para gerir autenticação do utilizador
session_start();

// Inclui funções de autenticação e conexão com o banco de dados
require '../API/auth.php';

// Redireciona para a página de login se o utilizador não estiver autenticado
if(!isset($_SESSION["user"])){
    header("Location: login.php");
    exit();
}

// Obtém o termo de busca da requisição GET e evita SQL Injection
$search = isset($_GET['search']) ? $con->real_escape_string($_GET['search']) : '';

// Monta a query SQL para procurar produtos, com filtro de busca opcional
$sql = "SELECT id, nome, descricao, preco, imagem FROM produto";
if ($search !== '') {
    // Adiciona filtro de busca por nome ou descrição se houver termo pesquisado
    $sql .= " WHERE nome LIKE '%$search%' OR descricao LIKE '%$search%'";
}
$result = $con->query($sql);

// Seleciona todos os produtos encontrados e armazena em um array
$produtos = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $produtos[] = $row;
    }
} 
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Página Inicial</title>
    <!-- Importa o CSS do Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <!-- Barra de navegação -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom">
        <div class="container">
            <a class="navbar-brand text-danger fw-bold" href="#">Loja Online</a>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <!-- Mostra link da área admin se o utilizador for admin -->
                    <?php if(isAdmin()){ ?>
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
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="card shadow p-4 border-0">
                    <!-- Formulário de procura de produtos -->
                    <form class="row mb-4" method="get" action="">
                        <div class="col-md-8">
                            <!-- Campo de texto para busca, preenchido com o termo atual -->
                            <input type="text" class="form-control" name="search" placeholder="Pesquisar produtos..." value="<?php echo htmlspecialchars($search); ?>">
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-danger w-100">Pesquisar</button>    
                        </div>
                    </form>
                    <!-- Grelha de cards de produtos -->
                    <div class="row g-4">
                        <?php foreach ($produtos as $produto): ?>
                            <div class="col-md-4">
                                <div class="card h-100 border-0">
                                    <?php 
                                    // Exibe a imagem do produto ou um placeholder se não houver imagem
                                    if (!empty($produto['imagem'])) {
                                        $imagemData = base64_encode($produto['imagem']);
                                        $src = 'data:image/jpeg;base64,' . $imagemData;
                                    } else {
                                        $src = 'https://via.placeholder.com/300x180?text=Sem+Imagem';
                                    }
                                    ?>
                                    <img src="<?php echo $src; ?>" class="card-img-top" alt="<?php echo htmlspecialchars($produto['nome']); ?>" style="height: 180px; object-fit: cover;">
                                    <div class="card-body d-flex flex-column">
                                        <h5 class="card-title text-danger"><?php echo htmlspecialchars($produto['nome']); ?></h5>
                                        <p class="card-text"><?php echo htmlspecialchars($produto['descricao']); ?></p>
                                        <div class="mt-auto">
                                            <!-- Preço do produto formatado -->
                                            <strong class="text-danger">€<?php echo number_format($produto['preco'], 2, ',', '.'); ?></strong>
                                            <!-- Formulário para adicionar produto ao carrinho -->
                                            <form method="post" action="../API/add_to_cart.php" class="mt-3 d-flex align-items-center gap-2">
                                                <input type="hidden" name="product_id" value="<?php echo $produto['id']; ?>">
                                                <input type="number" name="quantity" value="1" min="1" class="form-control form-control-sm" style="width: 70px;">
                                                <button type="submit" class="btn btn-outline-danger btn-sm">Adicionar ao Carrinho</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Importa o JS do Bootstrap para funcionalidades interativas -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
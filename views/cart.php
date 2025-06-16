<?php

// Inclui o ficheiro de autenticação para garantir que o utilizador está autenticado
require '../API/auth.php';

// Inicia a sessão para aceder às variáveis de sessão
session_start();

// Verifica se o utilizador está autenticado; se não estiver, redireciona para a página de login
if (!isset($_SESSION["user"])) {
    header("Location: login.php");
    exit();
}

// Inclui o ficheiro de ligação à base de dados
require '../API/db.php';

// Prepara uma query SQL para buscar os produtos do carrinho do utilizador autenticado
$sql = $con->prepare("SELECT p.id, p.nome, p.descricao, p.preco, p.imagem, c.quantity FROM produto p JOIN carrinho c ON p.id = c.product_id WHERE c.userId = ?");

// Associa o ID do utilizador autenticado como parâmetro da query
$sql->bind_param("i", $_SESSION["user"]["id"]);

// Executa a query preparada
$sql->execute();

// Obtém o resultado da query
$result = $sql->get_result();

// Define o ID do cliente PayPal (deverá ser preenchido com o valor real)
$PAYPAL_CLIENT_ID = "";

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carrinho</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <!-- Navbar copied from index.php -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom">
        <div class="container">
            <a class="navbar-brand text-danger fw-bold" href="#">Loja Online</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <?php 
                    // Verifica se a função isAdmin existe e se o usuário atual é um administrador
                    if(function_exists('isAdmin') && isAdmin()){ ?>
                        <li class="nav-item">
                            <!-- Exibe o link para a área de administração apenas para administradores -->
                            <a class="nav-link" href="areaadmin.php">Área de Administração</a>
                        </li>
                    <?php } ?>
                    <li class="nav-item">
                        <!-- Link para a página inicial -->
                        <a class="nav-link" href="index.php">Página Inicial</a>
                    </li>
                    <li class="nav-item">
                        <!-- Link para o carrinho, com ícone SVG -->
                        <a class="nav-link" href="cart.php">
                            <!-- Ícone de carrinho do Bootstrap Icons -->
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-cart" viewBox="0 0 16 16" style="margin-right: 4px;">
                                <path d="M0 1.5A.5.5 0 0 1 .5 1h1a.5.5 0 0 1 .485.379L2.89 5H14.5a.5.5 0 0 1 .491.592l-1.5 8A.5.5 0 0 1 13 14H4a.5.5 0 0 1-.491-.408L1.01 2H.5a.5.5 0 0 1-.5-.5zm3.14 4l1.25 6.5h7.22l1.25-6.5H3.14zM5 12a2 2 0 1 0 4 0H5z"/>
                            </svg>
                            Carrinho
                        </a>
                    </li>
                    <li class="nav-item">
                        <!-- Link para logout -->
                        <a class="nav-link" href="logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card shadow p-4 border-0">
                    <h2 class="mb-4 text-center text-danger">Carrinho de Compras</h2>
                    <?php 
                    // Se não houver itens no carrinho, mostra uma mensagem de carrinho vazio
                    if ($result->num_rows === 0): ?>
                        <div class="alert alert-info text-center">O seu carrinho está vazio.</div>
                    <?php endif; ?>
                    <div class="row">
                        <?php 
                        // Percorre cada item do carrinho (cada linha do resultado da query)
                        while($row = $result->fetch_assoc()): ?>
                            <div class="col-md-12 cart-item d-flex align-items-center border-bottom py-3">
                                <?php 
                                    // Codifica a imagem do produto em base64 para exibição direta no HTML
                                    $image = base64_encode($row['imagem']);
                                    $src = 'data:image/jpeg;base64,' . $image;
                                ?>
                                <div class="me-4">
                                    <!-- Exibe a imagem do produto -->
                                    <img src="<?php echo $src ?>" alt="Imagem" class="img-thumbnail" style="width: 120px; height: 80px; object-fit: cover;">
                                </div>
                                <div class="flex-grow-1">
                                    <!-- Nome do produto, protegido contra XSS -->
                                    <h5 class="text-danger"><?php echo htmlspecialchars($row['nome']); ?></h5>
                                    <!-- Descrição do produto, protegida contra XSS -->
                                    <p class="mb-1 text-muted"><?php echo htmlspecialchars($row['descricao']); ?></p>
                                    <!-- Preço do produto formatado em euros -->
                                    <div class="fw-bold mb-2 text-danger"><?php echo number_format($row['preco'], 2, ',', '.'); ?> €</div>
                                    <div class="cart-actions d-flex gap-2">
                                        <!-- Formulário para atualizar a quantidade do produto no carrinho -->
                                        <form action="../API/update_cart.php" method="post" class="d-flex align-items-center gap-2">
                                            <input type="hidden" name="product_id" value="<?php echo $row['id']; ?>">
                                            <input type="number" name="quantity" value="<?php echo $row['quantity']; ?>" min="1" class="form-control form-control-sm" style="width: 70px;">
                                            <button type="submit" class="btn btn-danger btn-sm">Atualizar</button>
                                        </form>
                                        <!-- Formulário para remover o produto do carrinho -->
                                        <form action="../API/delete_cart.php" method="post">
                                            <input type="hidden" name="product_id" value="<?php echo $row['id']; ?>">
                                            <button type="submit" class="btn btn-outline-danger btn-sm">Remover</button>
                                        </form>
                                    </div>
                                </div>
                                <div class="ms-auto text-center">
                                    <!-- Exibe o subtotal do produto (quantidade x preço) -->
                                    <span class="badge bg-danger fs-6">Subtotal: <?php echo $row["quantity"]*$row['preco']; ?> €</span>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                    <?php
                    // Reinicia o ponteiro do resultado para o início
                    $result->data_seek(0);
                    $total = 0;
                    // Calcula o total do carrinho somando o subtotal de cada produto
                    while($row = $result->fetch_assoc()) {
                        $total += $row["quantity"] * $row["preco"];
                    }
                    ?>
                    <?php if ($total > 0): ?>
                        <div class="d-flex justify-content-end mt-4">
                            <!-- Exibe o total do pedido formatado -->
                            <h4>Total do Pedido: <span class="badge bg-danger"><?php echo number_format($total, 2, ',', '.'); ?> €</span></h4>
                        </div>
                        <div class="d-flex justify-content-center mt-4">
                            <!-- Container para o botão do PayPal -->
                            <div id="paypal-button-container" class="w-50"></div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <script src=<?php echo "https://www.paypal.com/sdk/js?client-id=$PAYPAL_CLIENT_ID&currency=EUR" ?>></script>

    <script>
        paypal.Buttons({
            createOrder: function(data, actions) {
                return actions.order.create({
                    purchase_units: [{
                        amount: {
                            value: '<?php echo $total; ?>'
                        }
                    }]
                });
            },
            onApprove: function(data, actions) {
                return actions.order.capture().then(function(details) {
                    window.location.href = "finish.php";
                });
            },
            onError: function(err) {
                console.error('Erro no pagamento:', err);
                alert('Ocorreu um erro durante o pagamento. Tente novamente.');
            }
        }).render('#paypal-button-container');
    </script>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>


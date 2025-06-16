<?php

// Inclui o arquivo de autenticação para garantir que o utilizador está logado
require 'auth.php';

// Verifica se existe uma sessão de utilizador ativa
if (!isset($_SESSION["user"])) {
    // Se não houver utilizador logado, redireciona para a página de login
    header("Location: ../views/login.php");
    exit();
}

// Inclui o arquivo de conexão com o banco de dados
require 'db.php';

// Verifica se a requisição é do tipo POST e se os campos necessários foram enviados
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['product_id']) && isset($_POST['quantity'])) {
    // Obtém o ID do utilizador da sessão
    $userId = $_SESSION["user"]["id"];
    //evitar SQL Injection
    $produtoId = $con->real_escape_string($_POST['product_id']);
    //evitar SQL Injection
    $quantidade = $con->real_escape_string($_POST['quantity']);

    if ($quantidade <= 0) {
        // Se a quantidade for zero ou negativa, remove o produto do carrinho
        $sql = $con->prepare("DELETE FROM carrinho WHERE userId = ? AND product_id = ?");
        $sql->bind_param("ii", $userId, $produtoId);
    } else {
        // Atualiza a quantidade do produto no carrinho
        $sql = $con->prepare("UPDATE carrinho SET quantity = ? WHERE userId = ? AND product_id = ?");
        $sql->bind_param("iii", $quantidade, $userId, $produtoId);
    }
    // Verifica se a execução da query SQL foi bem-sucedida
    if ($sql->execute()) {
        // Se a execução foi bem-sucedida, redireciona para a página do carrinho
        header("Location: ../views/cart.php");
        // Encerra o script imediatamente após o redirecionamento para evitar execução de código extra
        exit();
    } else {
        // Caso a execução da query falhe, exibe uma mensagem de erro
        echo "Erro ao atualizar o carrinho.";
    }
    
    }
?>
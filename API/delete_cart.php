<?php

// Inclui o arquivo de autenticação para garantir que apenas utilizadores autenticados possam acessar esta página
require 'auth.php';

// Inicia a sessão para acessar variáveis de sessão
session_start();

// Verifica se o utilizador está logado; se não estiver, redireciona para a página de login
if(!isset($_SESSION["user"])) {
    header("Location: login.php");
    exit();
}

// Inclui o arquivo de conexão com o banco de dados
require 'db.php';

// Verifica se a requisição é do tipo POST e se o 'product_id' foi enviado
if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['product_id'])) {

    // Obtém o ID do utilizador da sessão
    $userId = $_SESSION["user"]["id"];
    // Obtém o ID do produto enviado pelo formulário e evita SQL Injection
    $productId = $con->real_escape_string($_POST['product_id']);
    // Prepara a query SQL para deletar o produto do carrinho do utilizador
    $sql = $con->prepare("DELETE FROM carrinho WHERE userId = ? AND product_id = ?");
    // Faz o bind dos parâmetros (userId e productId) na query preparada
    $sql->bind_param("ii", $userId, $productId);
    // Executa a query e verifica se foi bem-sucedida
    if ($sql->execute()) {
        // Se a exclusão for bem-sucedida, redireciona para a página do carrinho
        header("Location: ../views/cart.php");
        exit();
    } else {
        // Se houver erro na exclusão, exibe uma mensagem de erro
        echo "Erro ao remover produto do carrinho.";
    }
    
}
?>
<?php

// Verifica se o utilizador está autenticado
require 'auth.php';

session_start();

if (!isset($_SESSION["user"])) {
    header("Location: ../views/login.php");
    exit(); 
}

// Recebe o post com o ID do produto e quantidade
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['product_id']) && isset($_POST['quantity'])) {
    //Verifica se o produto já existe no carrinho e se sim, atualiza a quantidade
    $produto_id = intval($_POST['product_id']);
    $quantidade = intval($_POST['quantity']);
    $sql = $con->prepare("SELECT quantity FROM carrinho WHERE product_id = ? AND userId = ?");
    $sql->bind_param("ii", $produto_id, $_SESSION["user"]["id"]);
    $sql->execute();
    $result = $sql->get_result();
    if ($result->num_rows > 0) {
        // Produto já existe no carrinho, atualiza a quantidade
        $row = $result->fetch_assoc();
        $nova_quantidade = $row['quantity'] + $quantidade;
        $update_sql = $con->prepare("UPDATE carrinho SET quantity = ? WHERE product_id = ? AND userId = ?");
        $update_sql->bind_param("iii", $nova_quantidade, $produto_id, $_SESSION["user"]["id"]);
        $update_sql->execute();
    } else {
        // Produto não existe no carrinho, insere novo registo
        $insert_sql = $con->prepare("INSERT INTO carrinho (userId, product_id, quantity) VALUES (?, ?, ?)");
        $insert_sql->bind_param("iii", $_SESSION["user"]["id"], $produto_id, $quantidade);
        $insert_sql->execute(); 
    }
    // Se não, adiciona o produto ao carrinho
    header("Location: ../views/index.php");

    } 
    


?>
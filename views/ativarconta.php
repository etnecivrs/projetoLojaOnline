<?php
require "../API/auth.php"; // Inclui o arquivo de autenticação

if(isset($_GET["email"]) && isset($_GET["token"])) { // Verifica se os parâmetros 'email' e 'token' foram passados pela URL (método GET).
    ativarConta($_GET["email"], $_GET["token"]); // Chama a função 'ativarConta' passando o email e o token recebidos para ativar a conta
    header("Location: login.php"); // Redireciona para a página de login após a ativação.
    exit(); // Encerra a execução do script para garantir que nada mais seja processado após o redirecionamento.
}
 
?>
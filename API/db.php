<?php

//Ativar relatorio de erros
mysqli_report(MYSQLI_REPORT_ERROR);

//conexao
$con = new mysqli("localhost", "root", "", "loja_php");

//verificar se a conexão foi bem sucedida
if ($con->connect_error) {
    
    die("Erro 2134 - Ocorreu um erro inesperado!" . $con->connect_error);
}
?>
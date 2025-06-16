<?php
// Inicia a sessão para acessar variáveis de sessão
session_start();

// Inclui o arquivo de conexão com o banco de dados
require 'db.php';

// Inclui o arquivo de autenticação
require 'auth.php';

// Verifica se o utilizador é administrador usando a função isAdmin()
// Se não for, retorna um erro em formato JSON e encerra o script
if(!isAdmin()){
    echo json_encode(array("status" => "error", "message" => "Acesso negado"));
    exit();
}

// Verifica se o parâmetro 'id' foi fornecido via GET
// Se não, devolve um erro em formato JSON e encerra o script
if(!isset($_GET['id'])) {
    echo json_encode(array("status" => "error", "message" => "ID do produto não fornecido"));
    exit();
}

// Obtém o ID do produto a ser excluído a partir do parâmetro GET
$id = $_GET['id'];

// Prepara a query SQL para apagar o produto com o ID fornecido
$sql = $con->prepare("DELETE FROM produto WHERE id = ?");

// Faz o bind do parâmetro ID como inteiro na query preparada
$sql->bind_param("i", $id);

// Executa a query e verifica se foi bem-sucedida
if($sql->execute()) {
    // Se sim, devolve mensagem de sucesso em formato JSON
    echo json_encode(array("status" => "success", "message" => "Produto excluído com sucesso"));
} else {
    // Se não, devolve mensagem de erro em formato JSON
    echo json_encode(array("status" => "error", "message" => "Erro ao excluir o produto: "));
}

// Fecha o statement preparado
$sql->close();

// Fecha a conexão com o banco de dados
$con->close();

?>
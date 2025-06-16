<?php
// Inicia a sessão para acessar variáveis de sessão (ex: autenticação do utilizador)
session_start();

// Inclui o arquivo de conexão com o banco de dados
require 'db.php';

// Inclui o arquivo de autenticação 
require 'auth.php';

// Define o tipo de conteúdo da resposta como JSON
header('Content-Type: application/json');

// Verifica se todos os dados obrigatórios foram enviados via POST
if (!isset($_POST['id']) || !isset($_POST['nome']) || !isset($_POST['descricao']) || !isset($_POST['preco'])) {
    // Se faltar algum dado, retorna erro em formato JSON e encerra o script
    echo json_encode(array("status" => "error", "message" => "Faltam dados obrigatórios"));
    exit();
}

// Verifica se o utilizador logado é administrador
if (!isAdmin()) {
    // Se não for admin, retorna erro de acesso negado e encerra o script
    echo json_encode(array("status" => "error", "message" => "Acesso negado"));
    exit();
}

// Obtém os dados recebidos via POST
$id = intval($_POST['id']); // Converte o id para inteiro
$nome = $_POST['nome'];
$descricao = $_POST['descricao'];
$preco = $_POST['preco'];

// Verifica se foi enviado um arquivo de imagem e se ele possui tamanho maior que zero
if (isset($_FILES['imagem']) && $_FILES['imagem']['size'] > 0) {
    // Lê o conteúdo binário da imagem enviada
    $imagem = file_get_contents($_FILES['imagem']['tmp_name']);
    // Prepara a query SQL para atualizar todos os campos, incluindo a imagem
    $sql = $con->prepare("UPDATE produto SET nome=?, descricao=?, preco=?, imagem=? WHERE id=?");
    // Associa os parâmetros à query (nome, descricao, preco, imagem, id)
    $sql->bind_param("ssdsi", $nome, $descricao, $preco, $imagem, $id);
    // Envia os dados binários da imagem para o parâmetro correspondente
    $sql->send_long_data(3, $imagem);
} else {
    // Se não houver imagem, prepara a query para atualizar apenas os outros campos
    $sql = $con->prepare("UPDATE produto SET nome=?, descricao=?, preco=? WHERE id=?");
    // Associa os parâmetros à query (nome, descricao, preco, id)
    $sql->bind_param("ssdi", $nome, $descricao, $preco, $id);
}

// Executa a query preparada
$sql->execute();

// Verifica se alguma linha foi afetada (ou seja, se o produto foi atualizado)
if ($sql->affected_rows > 0) {
    // Devolve mensagem de sucesso em formato JSON
    echo json_encode(array("status" => "success", "message" => "Produto atualizado com sucesso"));
} else {
    // Devolve mensagem de erro (nenhuma alteração feita ou erro na atualização)
    echo json_encode(array("status" => "error", "message" => "Nenhuma alteração feita ou erro ao atualizar produto"));
}

// Fecha o statement e a conexão com o banco de dados
$sql->close();
$con->close();
?>
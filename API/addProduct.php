<?php
require "db.php"; // Inclui o arquivo de conexão com a base de dados
require "auth.php"; // Inclui o arquivo de autenticação (funções de autenticação/validação de utilizador)

// Verifica se todos os campos obrigatórios do formulário foram enviados via POST
if(!isset($_POST["nome"]) || !isset($_POST["descricao"]) || !isset($_POST["preco"])){
    // Se algum campo estiver em falta, devolve erro em formato JSON e termina o script
    echo json_encode(array("status" => "error", "message" => "Preencha todos os campos!"));
    exit();
}

// Verifica se o utilizador tem permissões de administrador
// Se não for admin, retorna erro em formato JSON e termina o script
if(!isAdmin()){
    echo json_encode(array("status" => "error", "message" => "Não tem permissões para aceder a esta página!"));
    exit();
}

// Lê o conteúdo binário da imagem enviada no formulário
$imagem = file_get_contents($_FILES["imagem"]["tmp_name"]);

// Prepara a query SQL para inserir um novo produto na base de dados
$sql = $con->prepare("INSERT INTO produto (nome, descricao, preco, imagem) VALUES (?, ?, ?, ?)");

// Associa os parâmetros recebidos do formulário à query preparada
// "ssdb" indica os tipos dos parâmetros: string, string, double, blob
$sql->bind_param("ssdb", $_POST["nome"], $_POST["descricao"], $_POST["preco"], $imagem);

// Envia os dados binários da imagem para o parâmetro correspondente (índice 3)
$sql->send_long_data(3, $imagem);

// Executa a query preparada
$sql->execute();

// Verifica se a inserção foi bem-sucedida (se alguma linha foi afetada)
if($sql->affected_rows > 0){
    // Sucesso: devolve mensagem de sucesso em formato JSON
    echo json_encode(array("status" => "success", "message" => "Produto adicionado com sucesso!"));
}else{
    // Erro: devolve mensagem de erro em formato JSON
    echo json_encode(array("status" => "error", "message" => "Erro ao adicionar produto!"));
}

// Fecha o statement e a conexão com a base de dados
$sql->close();
$con->close();

?>
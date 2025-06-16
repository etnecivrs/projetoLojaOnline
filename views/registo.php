<?php

// Inicia a sessão para permitir o uso de variáveis de sessão
session_start();

// Inclui o arquivo de autenticação
require "../API/auth.php";

// Inicializa variáveis para controlo de erros e mensagens
$error_message = false;
$message = "";

// Verifica se o formulário foi submetido via POST e se todos os campos necessários estão presentes
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["username"]) && isset($_POST["password"]) && isset($_POST["confirmar_password"]) && isset($_POST["email"]) && isset($_POST["telemovel"]) && isset($_POST["nif"])){

    // Verifica se o campo username está vazio
    if (empty($_POST["username"])){
        $error_message = true;
        $message = "Preencha o username!";
    }
    // Verifica se o campo password está vazio
    if (empty($_POST["password"])){
        $error_message = true;
        $message = "Preencha a password!";
    }
    // Verifica se o campo confirmar_password está vazio
    if (empty($_POST["confirmar_password"])){
        $error_message = true;
        $message = "Confirme a password!";
    }
    // Verifica se o campo email está vazio
    if (empty($_POST["email"])){
        $error_message = true;
        $message = "Preencha o email!";
    }
    // Verifica se o campo telemovel está vazio
    if (empty($_POST["telemovel"])){
        $error_message = true;
        $message = "Preencha o telemóvel!";
    }
    // Verifica se o campo nif está vazio
    if (empty($_POST["nif"])){
        $error_message = true;
        $message = "Preencha o NIF!";
    }
    // Verifica se as passwords coincidem
    if ($_POST["password"] != $_POST["confirmar_password"]){
        $error_message = true;
        $message = "As passwords não coincidem!";
    }
    // Se não houve erro até aqui, tenta registar o utilizador
    if (!$error_message){
        // Chama a função registo para criar o utilizador
        if (registo($_POST["email"], $_POST["username"], $_POST["password"], $_POST["telemovel"], $_POST["nif"])){
            // Se o registo for bem-sucedido, redireciona para a página de login
            header("Location: login.php");
        }else{
            // Caso contrário, mostra mensagem de erro genérica
            $error_message = true;
            $message = "Ocorreu um erro ao registar o utilizador. Verifique os seus dados!";
        }
    }
}

?>


<!DOCTYPE html>
<html lang="pt">

<head>
  <meta charset="UTF-8">
  <title>Registo</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
<!-- Aplica uma cor de fundo clara ao corpo da página usando uma classe do Bootstrap -->

<div class="container mt-5">
    <!-- Cria um container centralizado com margem superior (mt-5) -->
    <div class="row justify-content-center">
        <!-- Cria uma linha e centraliza o conteúdo horizontalmente -->
        <div class="col-md-5">
            <!-- Define a largura da coluna para dispositivos médios (5/12 do espaço) -->
            <div class="card shadow p-4 border-0">
                <!-- Cria um cartão com sombra, padding e sem borda -->
                <h3 class="text-center mb-4 text-danger">Registo de Utilizador</h3>
                <!-- Título centralizado, com margem inferior e cor vermelha -->
                <?php if($error_message): ?>
                    <!-- Se existir uma mensagem de erro, mostra um alerta -->
                    <div class="alert alert-danger text-center" role="alert">
                        <?php echo $message; ?>
                    </div>
                <?php endif; ?>
                <form method="POST">
                    <!-- Início do formulário, método POST para enviar dados de forma segura -->
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="username" name="username" required>
                        <!-- Campo de texto obrigatório para o username -->
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                        <!-- Campo de password obrigatório -->
                    </div>
                    <div class="mb-3">
                        <label for="confirmar_password" class="form-label">Confirmar Password</label>
                        <input type="password" class="form-control" id="confirmar_password" name="confirmar_password" required>
                        <!-- Campo de password para confirmação, obrigatório -->
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                        <!-- Campo de email obrigatório, validação automática de formato -->
                    </div>
                    <div class="mb-3">
                        <label for="telemovel" class="form-label">Telemóvel</label>
                        <input type="tel" class="form-control" id="telemovel" name="telemovel" required>
                        <!-- Campo de telefone obrigatório -->
                    </div>
                    <div class="mb-3">
                        <label for="nif" class="form-label">NIF</label>
                        <input type="text" class="form-control" id="nif" name="nif" maxlength="9" required>
                        <!-- Campo de texto obrigatório para NIF, máximo 9 caracteres -->
                    </div>
                    <button type="submit" class="btn btn-danger w-100">Registar</button>
                    <!-- Botão de submissão do formulário -->
                </form>
                <div class="text-center mt-3">
                    <!-- Div centralizada com margem superior -->
                    <p>Já tens conta? <a href="login.php" class="text-danger">Faz login aqui</a></p>
                    <!-- Link para a página de login -->
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>

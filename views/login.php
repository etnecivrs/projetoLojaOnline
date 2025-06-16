<?php
// Inicia a sessão para permitir o uso de variáveis de sessão
session_start();

// Inclui o arquivo de autenticação
require "../API/auth.php";

// Inicializa variáveis para controle de mensagens de erro
$error_message = false;
$message = "";

// Verifica se o formulário foi enviado via método POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtém os valores enviados pelo formulário
    $username = $_POST["username"];
    $password = $_POST["password"];

    // Verifica se algum dos campos está vazio
    if (empty($username) || empty($password)) {
        $error_message = true; // Sinaliza que há um erro
        $message = "Preencha todos os campos!"; // Mensagem de erro
    } else {
        // Tenta autenticar o utilizador usando a função login()
        if (login($username, $password)) {
            // Se o login for bem-sucedido, redireciona para index.php
            header("Location: index.php");
            exit; // encerra o script após o redirecionamento
        } else {
            // Se o login falhar, define mensagem de erro
            $error_message = true;
            $message = "Username ou Password incorretos!";
        }
    }
}
?>



<!DOCTYPE html>
<html lang="pt">
<head>
  <meta charset="UTF-8">
  <title>Login</title>
  <!-- Importa o CSS do Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<!-- Exibe mensagem de erro, se houver -->
<?php if($error_message): ?>
    <div class="alert alert-danger text-center" role="alert">
        <?php echo $message; ?>
    </div>
<?php endif; ?>


<div class="container mt-5">
  <div class="row justify-content-center">
    <div class="col-md-4">
      <h3 class="text-center mb-4">Login</h3>
      <!-- Formulário de login -->
      <form method="POST">
        <div class="mb-3">
          <label for="username" class="form-label">Username</label>
          <!-- Campo de texto para o nome de utilizador -->
          <input type="text" class="form-control" id="username" name="username" required>
        </div>
        <div class="mb-3">
          <label for="password" class="form-label">Password</label>
          <!-- Campo de password -->
          <input type="password" class="form-control" id="password" name="password" required>
        </div>
        <!-- Botão para enviar o formulário -->
        <button type="submit" class="btn btn-danger w-100">Entrar</button>
      </form>

      <!-- Link para página de registo -->
      <div class="text-center mt-3">
        <p>Não tens conta? <a href="registo.php">Regista-te aqui</a></p>
      </div>
    </div>
  </div>
</div>

</body>
</html>

<?php
    // Termina sessão do utilizador
    header("Location: login.php");
    session_destroy();
    exit();


?>
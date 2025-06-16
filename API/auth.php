<?php
 



require 'db.php';
require 'email.php';
 

/**
 * Login de um utilizador
 * @param string $username -> Nome de utilizador ou email
 * @param string $password -> Password do utilizador
 * @return bool -> true se o login foi bem sucedido, false caso contrário
 */

function login($username,$password){  
    //Ir buscar o utilizador por username ou email.
    global $con;
    $sql = $con->prepare('SELECT * FROM Utilizador WHERE (username = ? OR email = ?) AND active = 1');
    $sql->bind_param('ss',$username,$username);
    $sql->execute();
    $result = $sql->get_result(); //guardamos o resultado numa variável
    if($result->num_rows > 0){
        $row = $result->fetch_assoc();
        $_SESSION['user'] = $row;
        if (password_verify($password,$row['password'])){
            //A password está correta
            return true;
        }else{
            //A password está incorreta
            return false;
        }
    }else{
        //O utilizador não existe ou a conta não está ativa
        return false; 
    }
   
    
}
 
/**
* Registo de um novo utilizador
* @param string $email     -> Email do utilizador
* @param string $username  -> Nome de utilizador
* @param string $password  -> Password do utilizador
* @param string $telemovel -> Número de telemóvel
* @param string $nif       -> Número de Identificação Fiscal
* @return bool -> true se o registo foi bem sucedido, false caso contrário
*/
function registo($email,$username,$password,$telemovel,$nif){
        global $con;
        //1º - Criar e preparar a query de insert
        $sql = $con->prepare('INSERT INTO Utilizador(email,username,password,telemovel,nif,token,roleID) VALUES (?,?,?,?,?,?,2)');
        //2º - Gerar o token aletório
        $token = bin2hex(random_bytes(16));
        //3º - Encriptar a password
        $password = password_hash($password, PASSWORD_DEFAULT);
        //4º - Colocar os dados na query e executar a mesma e ver se deu certo
        $sql->bind_param('ssssss',$email,$username,$password,$telemovel,$nif,$token);
        $sql->execute();
        if($sql->affected_rows > 0){
            //5º - Enviar o email com o token para ativar a conta
            sendEmail($email,'Ativar a conta',"<a href='localhost/views/ativarconta.php?email=$email&token=$token'> Ative a sua conta</a>");
            return true;
        }else{
            //O registo falhou
            return false;
        }
}
 
/**
 * Ativar a conta do utilizador
 * @param string $email -> Email do utilizador
 * @param string $token -> Token de ativação
 * @return bool -> true se a ativação foi bem sucedida, false caso contrário
 */

function ativarConta($email,$token){
    //verificar se existe um registo com o email e o token
    global $con;
    $sql = $con->prepare('SELECT * FROM Utilizador WHERE email = ? AND token = ?'); 
    $sql->bind_param('ss',$email,$token);
    $sql->execute(); 
    $result = $sql->get_result();   
    if($result->num_rows > 0){
        //A ativação da conta foi bem sucedida
        $sql = $con->prepare('UPDATE Utilizador SET active = 1 WHERE email = ? AND token = ?'); 
        $sql->bind_param('ss',$email,$token);
        $sql->execute();    
        return true;
    }else{
        //A ativação da conta falhou
        return false;
    }
}
 

function isAdmin(){
    if($_SESSION["user"]["roleID"] == 1){
        return true;
    }else{
        return false;
    }
}
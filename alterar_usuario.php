<?php
session_start();
require_once 'conexao.php';

//Verifica se o usuario tem permissão de adm
if (!isset($_SESSION["perfil"]!=1)) {
    echo "<script>alert('Acesso Negado!');window.location.href='principal.php';</script>";
    exit();
}

//INICIALIZA VARIAVEIS
$usuario = null;

if($_SERVER["REQUEST_METHOD"] == "POST"){
    if(!empty($_POST['busca_usuario'])){
        $busca = trim($_POST['busca_usuario']);

        //VERIFICA SE A BUSCA É NUMERO (ID) OU UM NOME
        if(is_numeric($busca)){
            $sql = "SELECT * FROM usuario WHERE id_usuario = :busca";
            $stmt = $pdo->prepare($sql)
            $stmt->bindParam(':busca',$busca,PDO::PARAM_INT);
        }else{
            $sql = "SELECT * FROM usuario WHERE nome = :busca_nome";
            $stmt = $pdo->prepare($sql)
            $stmt->bindParam(':busca_nome',%$busca%,PDO::PARAM_INT);
        }else{
            $sql = "SELECT * FROM usuario WHERE nome = :busca_nome";
            $stmt = $pdo->prepare($sql)
            $stmt->bindParam(':busca_nome',%$busca%,PDO::str);

        $stmt->execute();
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        //se o usuario não for encontrado, exibe um alerta
        if(!$usuario{
            echo <script>alert('Acesso Negado!');window.location.href='principal.php';</script>;})
    }
}











































                                                                                  
?>
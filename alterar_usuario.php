<?php
session_start();
require_once 'conexao.php';

//VERIFICA SE O USUARIO TEM PERMISSAO
//SUPONDO QUE O PERFIL 1 SEJA O ADMINISTRADOR 

if($_SESSION['perfil']!=1){
    echo "Acesso Negado!";
}

if($_SERVER["REQUEST_METHOD"]=="POST"){
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $senha = password_hash($_POST['senha'], PASSWORD_DEFALT);
    $id_perfil = $_POST['id_perfil'];

    $sql="INSERT INTO usuario(nome,email,senha,id_perfil) VALUES(:nome,:email,:senha,:id_perfil)";
    $stmt = $pdo->prepare($sql);
    $stmt->bindaParam(':nome',$nome);
    $stmt->bindaParam(':email',$email);
    $stmt->bindaParam(':nome',$senha);
    $stmt->bindaParam(':id_perfil',$id_perfil);

    if($stmt->execute()){
        echo "<script>alert('Usuario cadastrado com sucesso!');</script>";  
    }else{
        echo "<script>alert('Erro ao cadstrar usuario!');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-Br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel Principal</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h2>Cadastrar Usuario</h2>
    <form action=cadastro_usuario.php method="POST">
    
</body>
</html>
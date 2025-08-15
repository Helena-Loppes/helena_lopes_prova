<?php
session_start();
require_once 'conexao.php';

//VERIFICA SE O USUARIO TEM PERMISSAO DE adm OU secretaria
if($_SESSION['perfil'] !=1 && $_SESSION['perfil']!=2){
    echo"<script>alert('Acesso negado!');window.location.href='principal.php';</script>";
    exit();
}

$usuario = []; //INICIALIZA A VARIAVEL PARA EVITAR ERROS

//SE O FORMULARIO FOR ENVIADO, BUSCA O USUARIO PELO ID OU NOME
if($_SERVER["REQUEST_METHOD"]== "POST" && !empty($_POST['busca'])){
    $busca = trim($_POST['busca']);

    //VERIRFICA SE A BUSCA É UM numero OU UM nome
    if(is_numeric($busca)){
        $sql="SELECT * FROM usuario WHERE id_usuario = :busca ORDER BY nome ASC";
        $stmt=$pdo->prepare($sql);
        $stmt->bindParam(':busca',$busca, PDO::PARAM_INT);
    }else{
        $sql="SELECT * FROM usuario WHERE nome LIKE :busca_nome ORDER BY nome ASC";
        $stmt=$pdo->prepare($sql);
        $stmt->bindValue(':busca_nome',"$busca%", PDO::PARAM_STR);
    }
}else{
    $sql="SELECT * FROM usuario ORDER BY nome ASC";
    $stmt=$pdo->prepare($sql);
}
$stmt->execute();
$usuarios = $stmt->fetchALL(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">
    <title>Buscar Usuario</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1>Listar Usuarios</h1>
    <form action="buscar_usuario.php" method="POST">
        <label for="busca">Digite ID ou NOME(opcional): </label>
        <input type="text" id="busca" name="busca">
        <button type="submit">Pesquisar</button>
    </form>
        <?php if(!empty($usuarios)): ?>

            <table class="table">
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Email</th>
                    <th>Perfil</th>
                    <th>Ações</th>
                </tr>

                <?php foreach($usuarios as $usuario): ?>

                <tr>
                    <td><?=htmlspecialchars($usuario['id_usuario'])?></td>
                    <td><?=htmlspecialchars($usuario['nome'])?></td>
                    <td><?=htmlspecialchars($usuario['email'])?></td>
                    <td><?=htmlspecialchars($usuario['id_perfil'])?></td>
                    <td>
                        <a href="alterar_usuario.php?id=<?=htmlspecialchars($usuario['id_usuario'])?>
                        "onclick="return confirm('Tem certeza que deseja excluir este usuario?')">Excluir</a>

                        <a href="excluir_usuario.php?id=<?=htmlspecialchars($usuario['id_usuario'])?>" 
                        onclick="return confirm('Tem certeza que deseja excluir este usuario?')">Excluir</a>
                    </td>
                </tr>
                <?php endforeach;?>
                </table>
            <?php else:?>
                <p>Nenhum usuario encontrado.</p>
            <?php endif;?>

            <a href="principal.php"> VOLTAR</a>
</body>
</html>
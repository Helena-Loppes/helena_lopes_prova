<?php
session_start();
require_once 'conexao.php';

//VERIFICA SE O USUARIO TEM PERMISSÃO DE ADM
if($_SESSION['perfil'] !=1){
    echo "script>alert('ACESSO NEGADO!');window.location.href='principal.php';</script>";
    exit();
}


//INICIALIZA VARIAVEIS
$usuario = null;

if($_SERVER["REQUEST_METHOD"] == "POST"){
    if(!empty($_POST['busca_usuario'])){
        $busca = trim($_POST['busca_usuario']);

        //VERIFICA SE A BUSCA É UM NUMERO(id) OU UM NOME
        if(is_numeric($busca)){
            $sql = "SELECT * FROM usuario WHERE id_usuario = :busca";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':busca', $busca, PDO::PARAM_INT);
        }else{
            $sql = "SELECT * FROM usuario WHERE nome LIKE :busca_nome";
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':busca_nome', "$busca%", PDO::PARAM_STR);
        }
        $stmt->execute();
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        //SE O USUARIO NAO FOR ENCONTRADO, EXIBE UM ALERTA
        if(!$usuario){
            echo "<script>alert('Usuario não encontrado!');</script>";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alterar usuario</title>
    <link rel="stylesheet" href="styles.css">
    <!-- CERTIFIQUE-SE QUE O JS ESTA SENDO CARREGADO CORRECTAMENTE -->
    <script src="scripts.js"></script>
</head>
<body>
    <h2>Alterar Usuario</h2>

    <form action="alterar_usuario.php" method="POST">
        <label for="busca_usuario">Digite o id ou nome de usuario</label>
        <input type="text" id="busca_usuario" name="busca_usuario" required onkeyup="buscarSugestoes()">

        <!-- DIV PARA EXIBIR SUGESTOES DE USUARIOS -->
         <div id="sugestoes"></div>

         <button type="submit">Pesquisar</button>
         <?php if($usuario):?>
            <!-- FORMULARIO PARA ALTERAR USUARIO -->
             <form action="processa_alteracao_usuario.php" method="POST">
                <input type="hidden" name="id_usuario" value="<?=htmlspecialchars($usuario['id_usuario'])?>">

                <label for="nome">Nome:</label>
                <input type="text" id="nome" name="nome" value="<?=htmlspecialchars($usuario['nome'])?>" required>

                <label for="email">E-mail:</label>
                <input type="email" id="email" name="email" value="<?=htmlspecialchars($usuario['email'])?>" required>

                <label for="id_perfil">Perfil:</label>
                <select name="id_perfil" id="id_perfil">
                    <option value="1"<?=$usuario['id_usuario'] == 1 ? 'select' : ''  ?>>Administrador</option>
                    <option value="2"<?=$usuario['id_usuario'] == 2 ? 'select' : ''  ?>>Secretaria</option>
                    <option value="3"<?=$usuario['id_usuario'] == 3 ? 'select' : ''  ?>>Almoxarifado</option>
                    <option value="4"<?=$usuario['id_usuario'] == 4 ? 'select' : ''  ?>>Cliente</option>
                </select>

                <!-- SE O USUARIO LOGADO FOR ADM, EXIBIR OPCAO DE ALTERAR SENHA -->
                 <?php if($_SESSION['perfil'] == 1): ?>
                    <label for="nova_senha">Nova Senha</label>
                    <input type="password" id="nova_senha" name="nova_senha">
                <?php endif; ?>

                    <button type="submit">Alterar</button>
                    <button type="reset">Cancelar</button>
             </form>
            <?php endif; ?>
            <a href="principal.php">Voltar</a>
    </form>
</body>
</html>
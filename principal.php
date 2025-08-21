<?php
session_start();
require_once 'conexao.php';

if(!isset($_SESSION['usuario'])){
    header("Location: login.php");
    exit();
}

//OBTENDO O NOME DO PERFIL DO USUARIO LOGADO
$id_perfil = $_SESSION['perfil'];
$sqlPerfil = "SELECT nome_perfil FROM perfil WHERE id_perfil = :id_perfil";
$stmtPerfil = $pdo->prepare($sqlPerfil);
$stmtPerfil->bindParam(":id_perfil", $id_perfil);
$stmtPerfil->execute();
$perfil = $stmtPerfil->fetch(PDO::FETCH_ASSOC);
$nome_perfil = $perfil['nome_perfil'];

//DEFINIÇÃO DAS PERMISSÕES POR perfil
$permissoes = [
    //adm
    1 => ["cadastrar"=>["cadastro_usuario.php", "cadastro_perfil.php", "cadastro_cliente.php", "cadastro_fornecedor.php", "cadastro_produto.php", "cadastro_funcionario.php"],

    "buscar"=>["buscar_usuario.php", "buscar_perfil.php", "buscar_cliente.php", "buscar_fornecedor.php", "buscar_produto.php", "buscar_funcionario.php"],

    "alterar"=>["alterar_usuario.php", "alterar_perfil.php", "alterar_cliente.php", "alterar_fornecedor.php", "alterar_produto.php", "alterar_funcionario.php"],

    "excluir"=>["excluir_usuario.php", "excluir_perfil.php", "excluir_cliente.php", "excluir_fornecedor.php", "excluir_produto.php", "excluir_funcionario.php"]],


    //secretaria
    2 => ["cadastrar"=>["cadastro_cliente.php"],

    "buscar"=>["buscar_cliente.php", "buscar_fornecedor.php", "buscar_produto.php"],

    "alterar"=>["alterar_fornecedor.php", "alterar_produto.php"],

    "excluir"=>["excluir_produto.php"]],


    //almoxarife
    3 => ["cadastrar"=>["cadastro_fornecedor.php", "cadastro_produto.php"],

    "buscar"=>["buscar_cliente.php", "buscar_fornecedor.php", "buscar_produto.php"],

    "alterar"=>["alterar_fornecedor.php", "alterar_produto.php"],

    "excluir"=>["excluir_produto.php"]],


    //cliente
    4 => ["cadastrar"=>["cadastro_cliente.php"],

    "buscar"=>["buscar_produto.php"],

    "alterar"=>["alterar_cliente.php"],
    ]
];

//OBTENDO AS OPÇÕES DISPONIVEIS PARA O PERFIL LOGADO
$opcoes_menu = $permissoes[$id_perfil];
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel Principal</title>
    <link rel="stylesheet" href="styles.css">
    <script src="scripts.js"></script>
    <!-- CSS para o menu amarelinho -->
    <style>

        header {
            background-color: #f5f5f5;
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        nav {
            background-color: #ffc107; /* amarelo */
        }

        .menu {
            list-style: none;
            margin: 0;
            padding: 0;
            display: flex;
        }

        .menu > li {
            position: relative;
        }

        .menu > li > a {
            display: block;
            padding: 14px 20px;
            color: #ffffffff;
            text-decoration: none;
            font-weight: bold;
        }

        .menu > li > a:hover {
            background-color: #ffca2c; /* tom mais escuro no hover */
            color: #ffffffff;
        }

        .dropdown-menu {
            display: none;
            position: absolute;
            background-color: #fff3cd; /* amarelo claro */
            list-style: none;
            margin: 0;
            padding: 0;
            border: 1px solid #ffffffff;
            z-index: 1000;
        }

        .dropdown-menu li a {
            display: block;
            padding: 10px 16px;
            text-decoration: none;
            color: #ffbf00ff;
        }

        .dropdown-menu li a:hover {
            background-color: #ffe8a1;
        }

    </style>
</head>
<body>
    <header>
        <div class="saudacao">
            <h2>Bem vindo, <?php echo $_SESSION['usuario']; ?>! Perfil: <?php echo $nome_perfil; ?></h2>
        </div>
        <div class="logout">
            <form action="logout.php" method="POST">
                <button type="submit">LogOut</button>
            </form>
        </div>
    </header>

    <nav>
        <ul class="menu">
            <?php foreach($opcoes_menu as $categoria => $arquivos):  ?>
                <li class="dropdown">
                    <a href="#"><?=$categoria?></a>
                    <ul class="dropdown-menu">
                        <?php foreach($arquivos as $arquivo): ?>
                            <li>
                                <a href="<?=$arquivo ?>"><?=ucfirst(str_replace("_"," ",basename($arquivo, ".php")))?></a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </li>
            <?php endforeach; ?>
        </ul>
    </nav>

    <address>Helena Lopes - Desenvolvimento de Sistemas - Senai</address>
</body>
</html>
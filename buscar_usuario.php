<?php
session_start();
require_once 'conexao.php';

// VERIFICA SE O USUÁRIO TEM PERMISSÃO DE adm OU secretaria
if ($_SESSION['perfil'] != 1 && $_SESSION['perfil'] != 2) {
    echo "<script>alert('Acesso negado!');window.location.href='principal.php';</script>";
    exit();
}

// OBTÉM NOME DO PERFIL LOGADO
$id_perfil = $_SESSION['perfil'];
$sqlPerfil = "SELECT nome_perfil FROM perfil WHERE id_perfil = :id_perfil";
$stmtPerfil = $pdo->prepare($sqlPerfil);
$stmtPerfil->bindParam(":id_perfil", $id_perfil);
$stmtPerfil->execute();
$perfil = $stmtPerfil->fetch(PDO::FETCH_ASSOC);
$nome_perfil = $perfil['nome_perfil'];

// PERMISSÕES POR PERFIL
$permissoes = [
    1 => [ // ADM
        "cadastrar" => ["cadastro_usuario.php", "cadastro_perfil.php", "cadastro_cliente.php", "cadastro_fornecedor.php", "cadastro_produto.php", "cadastro_funcionario.php"],
        "buscar"    => ["buscar_usuario.php", "buscar_perfil.php", "buscar_cliente.php", "buscar_fornecedor.php", "buscar_produto.php", "buscar_funcionario.php"],
        "alterar"   => ["alterar_usuario.php", "alterar_perfil.php", "alterar_cliente.php", "alterar_fornecedor.php", "alterar_produto.php", "alterar_funcionario.php"],
        "excluir"   => ["excluir_usuario.php", "excluir_perfil.php", "excluir_cliente.php", "excluir_fornecedor.php", "excluir_produto.php", "excluir_funcionario.php"]
    ],
    2 => [ // SECRETARIA
        "cadastrar" => ["cadastro_cliente.php"],
        "buscar"    => ["buscar_cliente.php", "buscar_fornecedor.php", "buscar_produto.php"],
        "alterar"   => ["alterar_fornecedor.php", "alterar_produto.php"],
        "excluir"   => ["excluir_produto.php"]
    ],
];

$opcoes_menu = $permissoes[$id_perfil];

// BUSCA DE USUÁRIOS
$usuario = [];
if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST['busca'])) {
    $busca = trim($_POST['busca']);

    if (is_numeric($busca)) {
        $sql = "SELECT * FROM usuario WHERE id_usuario = :busca ORDER BY nome ASC";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':busca', $busca, PDO::PARAM_INT);
    } else {
        $sql = "SELECT * FROM usuario WHERE nome LIKE :busca_nome ORDER BY nome ASC";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':busca_nome', "$busca%", PDO::PARAM_STR);
    }
} else {
    $sql = "SELECT * FROM usuario ORDER BY nome ASC";
    $stmt = $pdo->prepare($sql);
}

$stmt->execute();
$usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Buscar Usuário</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
    /* NAV BAR FULL WIDTH E FIXADA NO TOPO */
    nav {
        background-color: #ffc107;
        width: 100%;
        position: fixed; /* fixa no topo */
        top: 0;
        left: 0;
        z-index: 10000;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }

    /* LISTA DE MENU PRINCIPAL: FLEX E SEM MARGIN/PADDING */
    .menu {
        list-style: none;
        margin: 0;
        padding: 0;
        display: flex;
        max-width: 1200px; /* limita a largura centralizada */
        margin-left: auto;
        margin-right: auto;
    }

    /* ITENS DO MENU */
    .menu > li {
        position: relative;
    }

    .menu > li > a {
        display: block;
        padding: 14px 20px;
        color: #ffffff;
        text-decoration: none;
        font-weight: bold;
        white-space: nowrap;
    }

    .menu > li > a:hover {
        background-color: #ffca2c;
    }

    /* MENU DROPDOWN */
    .dropdown-menu {
        display: none;
        position: absolute;
        background-color: #fff3cd;
        list-style: none;
        margin: 0;
        padding: 0;
        border: 1px solid #ffc107;
        z-index: 11000;
        min-width: 200px;
        top: 100%;
        left: 0;
    }

    .dropdown-menu li a {
        display: block;
        padding: 10px 16px;
        text-decoration: none;
        color: #ffbf00;
    }

    .dropdown-menu li a:hover {
        background-color: #ffe8a1;
    }

    .menu > li:hover .dropdown-menu {
        display: block;
    }

    /* ESPAÇAMENTO DO CONTEÚDO PARA NÃO FICAR ATRÁS DO MENU FIXO */
    body {
        padding-top: 48px; /* altura aproximada do menu */
    }

    address {
        margin-top: 50px;
        font-style: italic;
        color: #555;
        text-align: center;
    }
</style>
</head>
<body class="container py-4">
    <nav>
        <ul class="menu">
            <?php foreach ($opcoes_menu as $categoria => $arquivos): ?>
                <li>
                    <a href="#"><?= ucfirst($categoria) ?></a>
                    <ul class="dropdown-menu">
                        <?php foreach ($arquivos as $arquivo): ?>
                            <li>
                                <a href="<?= $arquivo ?>">
                                    <?= ucfirst(str_replace("_", " ", basename($arquivo, ".php"))) ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </li>
            <?php endforeach; ?>
        </ul>
    </nav>

    <h1 class="content">Listar Usuários</h1>

    <form action="buscar_usuario.php" method="POST" class="mb-4">
        <label for="busca">Digite ID ou NOME (opcional): </label>
        <input type="text" id="busca" name="busca">
        <button type="submit">Pesquisar</button>
    </form>

    <?php if (!empty($usuarios)): ?>
        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Email</th>
                    <th>Perfil</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($usuarios as $usuario): ?>
                    <tr>
                        <td><?= htmlspecialchars($usuario['id_usuario']) ?></td>
                        <td><?= htmlspecialchars($usuario['nome']) ?></td>
                        <td><?= htmlspecialchars($usuario['email']) ?></td>
                        <td><?= htmlspecialchars($usuario['id_perfil']) ?></td>
                        <td>
                            <a href="alterar_usuario.php?id=<?= $usuario['id_usuario'] ?>" class="btn btn-sm btn-warning">Alterar</a>
                            <a href="excluir_usuario.php?id=<?= $usuario['id_usuario'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Tem certeza que deseja excluir este usuário?')">Excluir</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </body>
        </table>
    <?php else: ?>
        <div class="alert alert-info">Nenhum usuário encontrado.</div>
    <?php endif; ?>

    <a href="principal.php" class="btn btn-secondary mt-4">Voltar</a>

    <address>Helena Lopes - Desenvolvimento de Sistemas - Senai</address>

</body>
</html>

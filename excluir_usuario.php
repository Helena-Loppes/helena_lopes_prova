<?php
session_start();
require_once 'conexao.php';

// VERIFICA SE O USUÁRIO TEM PERMISSÃO DE ADM
if ($_SESSION['perfil'] != 1) {
    echo "<script>alert('Acesso Negado!'); window.location.href='principal.php';</script>";
    exit();
}

// MENU - OPÇÕES DO PERFIL ADM
$permissoes = [
    "cadastrar" => ["cadastro_usuario.php", "cadastro_perfil.php", "cadastro_cliente.php", "cadastro_fornecedor.php", "cadastro_produto.php", "cadastro_funcionario.php"],
    "buscar"    => ["buscar_usuario.php", "buscar_perfil.php", "buscar_cliente.php", "buscar_fornecedor.php", "buscar_produto.php", "buscar_funcionario.php"],
    "alterar"   => ["alterar_usuario.php", "alterar_perfil.php", "alterar_cliente.php", "alterar_fornecedor.php", "alterar_produto.php", "alterar_funcionario.php"],
    "excluir"   => ["excluir_usuario.php", "excluir_perfil.php", "excluir_cliente.php", "excluir_fornecedor.php", "excluir_produto.php", "excluir_funcionario.php"]
];

// INICIALIZA VARIÁVEIS
$usuarios = [];

// BUSCA TODOS OS USUÁRIOS
$sql = "SELECT * FROM usuario ORDER BY nome ASC";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

// EXCLUSÃO CASO O ID VENHA VIA GET
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id_usuario = $_GET['id'];

    $sql = "DELETE FROM usuario WHERE id_usuario = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id', $id_usuario);

    if ($stmt->execute()) {
        echo "<script>alert('Usuário excluído com sucesso!'); window.location.href='excluir_usuario.php';</script>";
    } else {
        echo "<script>alert('Erro ao excluir o usuário.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Excluir Usuário</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">

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

<!-- MENU AMARELO -->
<nav>
    <ul class="menu">
        <?php foreach ($permissoes as $categoria => $arquivos): ?>
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

<!-- CONTEÚDO ORIGINAL MANTIDO -->
<h2 class="content">Excluir Usuário</h2>

<?php if (!empty($usuarios)) { ?>
    <table class="table table-striped table-bordered align-middle">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>E-mail</th>
                <th>Perfil</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($usuarios as $usuario) { ?>
                <tr>
                    <td><?= htmlspecialchars($usuario['id_usuario']) ?></td>
                    <td><?= htmlspecialchars($usuario['nome']) ?></td>
                    <td><?= htmlspecialchars($usuario['email']) ?></td>
                    <td><?= htmlspecialchars($usuario['id_perfil']) ?></td>
                    <td>
                        <a href="excluir_usuario.php?id=<?= htmlspecialchars($usuario['id_usuario']) ?>" 
                           class="btn btn-sm btn-danger"
                           onclick="return confirm('Tem certeza que deseja excluir este usuário?')">
                           Excluir
                        </a>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
<?php } else { ?>
    <div class="alert alert-info">Nenhum usuário encontrado.</div>
<?php } ?>

<!-- Botão Voltar centralizado -->
<div class="text-center mt-4">
    <a href="principal.php" class="btn btn-secondary">Voltar</a>
</div>

<address>Helena Lopes - Desenvolvimento de Sistemas - Senai</address>

</body>
</html>

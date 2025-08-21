<?php
session_start();
require_once("conexao.php");

// VERIFICA SE O USUÁRIO TEM PERMISSÃO DE ADM
if ($_SESSION['perfil'] != 1) {
    echo "<script>alert('Acesso negado!');window.location.href='principal.php'</script>";
    exit();
}

// MENU (ADM)
$permissoes = [
    "cadastrar" => ["cadastro_usuario.php", "cadastro_perfil.php", "cadastro_cliente.php", "cadastro_fornecedor.php", "cadastro_produto.php", "cadastro_funcionario.php"],
    "buscar"    => ["buscar_usuario.php", "buscar_perfil.php", "buscar_cliente.php", "buscar_fornecedor.php", "buscar_produto.php", "buscar_funcionario.php"],
    "alterar"   => ["alterar_usuario.php", "alterar_perfil.php", "alterar_cliente.php", "alterar_fornecedor.php", "alterar_produto.php", "alterar_funcionario.php"],
    "excluir"   => ["excluir_usuario.php", "excluir_perfil.php", "excluir_cliente.php", "excluir_fornecedor.php", "excluir_produto.php", "excluir_funcionario.php"]
];

// BUSCA
$usuario = null;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!empty($_POST['busca_usuario'])) {
        $busca = trim($_POST['busca_usuario']);

        if (is_numeric($busca)) {
            $sql = "SELECT * FROM usuario WHERE id_usuario = :busca";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':busca', $busca, PDO::PARAM_INT);
        } else {
            $sql = "SELECT * FROM usuario WHERE nome LIKE :busca_nome";
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':busca_nome', "%$busca%", PDO::PARAM_STR);
        }

        $stmt->execute();
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$usuario) {
            echo "<script>alert('Usuário não encontrado!');</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Alterar Usuário</title>
    <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="styles.css">
    <script src="scripts.js"></script>

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

<body>

<!-- MENU AMARELO TOPO -->
<nav>
    <ul class="menu">
        <?php foreach ($permissoes as $categoria => $arquivos): ?>
            <li>
                <a href="#"><?= ucfirst($categoria) ?></a>
                <ul class="dropdown-menu">
                    <?php foreach ($arquivos as $arquivo): ?>
                        <li><a href="<?= $arquivo ?>"><?= ucfirst(str_replace("_", " ", basename($arquivo, ".php"))) ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </li>
        <?php endforeach; ?>
    </ul>
</nav>

<!-- CONTEÚDO ORIGINAL MANTIDO -->
<h2 class="title">Alterar usuário</h2>

<form action="alterar_usuario.php" method="POST">
    <label for="busca_usuario">Digite o id ou nome do usuário</label>
    <input type="text" id="busca_usuario" name="busca_usuario" class="form-control" required onkeyup="buscarSugestoes()">
    <div id="sugestoes"></div>
    <button type="submit" class="btn btn-success">Buscar</button>
</form>

<?php if ($usuario): ?>
    <form action="processa_alteracao_usuario.php" method="POST">
        <input type="hidden" name="id_usuario" class="form-control" value="<?= htmlspecialchars($usuario['id_usuario']) ?>">

        <label for="nome">Nome:</label>
        <input type="text" id="nome" name="nome" class="form-control" value="<?= htmlspecialchars($usuario['nome']) ?>" required>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" class="form-control" value="<?= htmlspecialchars($usuario['email']) ?>" required>

        <label for="id_perfil">Perfil:</label>
        <select name="id_perfil" id="id_perfil" class="form-select">
            <option value="1" <?= $usuario['id_perfil'] == 1 ? 'selected' : '' ?>>Administrador</option>
            <option value="2" <?= $usuario['id_perfil'] == 2 ? 'selected' : '' ?>>Secretária</option>
            <option value="3" <?= $usuario['id_perfil'] == 3 ? 'selected' : '' ?>>Almoxarife</option>
            <option value="4" <?= $usuario['id_perfil'] == 4 ? 'selected' : '' ?>>Cliente</option>
        </select>

        <?php if ($_SESSION['perfil'] == 1): ?>
            <label for="nova_senha">Nova senha</label>
            <input type="password" name="nova_senha" id="nova_senha" class="form-control">
        <?php endif; ?>

        <button type="submit" class="btn btn-success">Alterar</button>
        <button type="reset" class="btn btn-success">Cancelar</button>
    </form>
<?php endif; ?>

<a href="principal.php">Voltar</a>
<address>Helena Lopes - Desenvolvimento de Sistemas - Senai</address>
</body>
</html>

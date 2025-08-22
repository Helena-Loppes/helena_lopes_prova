<?php
    session_start();
    require_once "conexao.php";

    // VERIFICA SE O USUÁRIO TEM PERMISSÃO DE ADM
    if ($_SESSION['perfil'] !=1 && $_SESSION['perfil'] !=2 && $_SESSION['perfil'] !=3) {
        echo "<script>alert('Acesso Negado!');window.location.href='principal.php';</script>";
        exit();
    }

    // INICIALIZA VARIÁVEIS
    $fornecedor = null;

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (!empty($_POST['busca_fornecedor'])) {
            $busca = trim($_POST['busca_fornecedor']);

            // VERIFICA SE A BUSCA É UM NÚMERO (id) OU UM NOME
            if (is_numeric($busca)) {
                $sql = "SELECT * FROM fornecedor WHERE id_fornecedor = :busca";
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(":busca", $busca, PDO::PARAM_INT);
            } else {
                $sql = "SELECT * FROM fornecedor WHERE nome_fornecedor LIKE :busca_nome";
                $stmt = $pdo->prepare($sql);
                $stmt->bindValue(':busca_nome', "$busca%", PDO::PARAM_STR);
            }
            
            $stmt->execute();
            $fornecedor = $stmt->fetch(PDO::FETCH_ASSOC);

            // SE O FORNECEDOR NAO FOR ENCONTRADO, EXIBE UM ALERTA
            if (!$fornecedor) {
                echo "<script>alert('Fornecedor não encontrado!');</script>";
            }
        }
    }

    // OBTENDO O NOME DO PERFIL DO USUÁRIO LOGADO
    $id_perfil = $_SESSION['perfil'];
    $sqlPerfil = "SELECT nome_perfil FROM perfil WHERE id_perfil = :id_perfil";
    $stmtPerfil = $pdo->prepare($sqlPerfil);
    $stmtPerfil->bindParam(':id_perfil', $id_perfil);
    $stmtPerfil->execute();
    $perfil = $stmtPerfil->fetch(PDO::FETCH_ASSOC);
    $nome_perfil = $perfil['nome_perfil'];

    // DEFINIÇÃO DAS PERMISSÕES POR PERFIL
    $permissoes = [
        // PERMISSÕES DO ADMIN
        1 => ["Cadastrar"=>["cadastro_usuario.php", "cadastro_perfil.php", "cadastro_cliente.php", "cadastro_fornecedor.php", "cadastro_produto.php", "cadastro_funcionario.php"],
              "Buscar"=>["buscar_usuario.php", "buscar_perfil.php", "buscar_cliente.php", "buscar_fornecedor.php", "buscar_produto.php", "buscar_funcionario.php"],
              "Alterar"=>["alterar_usuario.php", "alterar_perfil.php", "alterar_cliente.php", "alterar_fornecedor.php", "alterar_produto.php", "alterar_funcionario.php"],
              "Excluir"=>["excluir_usuario.php", "excluir_perfil.php", "excluir_cliente.php", "excluir_fornecedor.php", "excluir_produto.php", "excluir_funcionario.php"]],

        // PERMISSÕES DA SECRETÁRIA
        2 => ["Cadastrar"=>["cadastro_cliente.php"],
              "Buscar"=>["buscar_cliente.php", "buscar_fornecedor.php", "buscar_produto.php"],
              "Alterar"=>["alterar_fornecedor.php", "alterar_produto.php"],
              "Excluir"=>["excluir_produto.php"]],

        // PERMISSÕES DO ALMOXARIFE
        3 => ["Cadastrar"=>["cadastro_fornecedor.php", "cadastro_produto.php"],
              "Buscar"=>["buscar_cliente.php", "buscar_fornecedor.php", "buscar_produto.php"],
              "Alterar"=>["alterar_fornecedor.php", "alterar_produto.php"],
              "Excluir"=>["excluir_produto.php"]],

        // PERMISSÕES DO CLIENTE
        4 => ["Cadastrar"=>["cadastro_cliente.php"],
              "Buscar"=>["buscar_cliente.php"],
              "Alterar"=>["alterar_cliente.php"]],
    ];

    // OBTENDO AS OPÇÕES DISPONIVEIS PARA O PERFIL DO USUÁRIO LOGADO
    $opcoes_menu = $permissoes["$id_perfil"];
?>


<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> Alterar Fornecedor </title>
    <link rel="stylesheet" href="styles.css">

    <!-- CERTIFIQUE-SE DE QUE O JAVASCRIPT ESTÁ SENDO CARREGADO CORRETAMENTE -->
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
    <h2> Alterar Fornecedor </h2>

    <nav>
        <ul class="menu">
            <?php foreach($opcoes_menu as $categoria => $arquivos) { ?>
                <li class="dropdown">
                    <a href="#"><?= $categoria ?></a>

                    <ul class="dropdown-menu">
                        <?php foreach($arquivos as $arquivo) { ?>
                            <li>   
                                <a href="<?= $arquivo ?>"><?= ucfirst(str_replace("_", " ", basename($arquivo, ".php"))) ?></a>
                            </li>
                        <?php } ?>
                    </ul>
                </li>
            <?php } ?>
        </ul>
    </nav>

    <form action="alterar_fornecedor.php" method="POST">
    <label for="busca"> Digite o ID ou NOME do Fornecedor: </label>
        <input type="text" name="busca_fornecedor" id="busca_fornecedor" required onkeyup="buscarSugestões()">

        <!-- DIV PARA EXCLUIR SUGESTÕES DE FORNECEDORES -->
         <div id="sugestoes"></div>
        <button type="submit"> Buscar </button>
    </form>

    <?php if ($fornecedor) { ?>

        <!-- FORMULÁRIO PARA ALTERAR FORNECEDOR -->
         <form id="formProcessar" action="processa_alteracao_fornecedor.php" method="POST">
            <input type="hidden" name="id_fornecedor" value="<?= htmlspecialchars($fornecedor['id_fornecedor']) ?>">

            <label for="nome"> Nome do Fornecedor: </label>
            <input type="text" id="nome_fornecedor" name="nome_fornecedor" value="<?= htmlspecialchars($fornecedor['nome_fornecedor']) ?>" required>

            <label for="endereco"> Endereço: </label>
            <input type="text" id="endereco" name="endereco" value="<?= htmlspecialchars($fornecedor['endereco']) ?>" required>
            
            <label for="telefone"> Telefone: </label>
            <input type="text" id="telefone" name="telefone" value="<?= htmlspecialchars($fornecedor['telefone']) ?>" required>

            <label for="email"> E-mail: </label>
            <input type="email" id="email" minlength="8" minlength="11" name="email" value="<?= htmlspecialchars($fornecedor['email']) ?>" required>

            <label for="contato"> Contato: </label>
            <input type="text" id="contato" name="contato" value="<?= htmlspecialchars($fornecedor['contato']) ?>" required>

            <button type="submit" >Alterar</button>
        <button type="reset" >Cancelar</button>
        </form>

    <?php } ?>

    <a class="voltar" href="principal.php"> Voltar </a>

    <script>
        document.getElementById("formProcessar").addEventListener("submit", function(event) {
            let nome = document.getElementById("nome_fornecedor").value.trim();
            let contato = document.getElementById("contato").value.trim();
            let telefone = document.getElementById("telefone").value.trim();


            // Regex: aceita apenas letras (maiúsculas e minúsculas) e espaços
            let nomeRegex = /^[A-Za-zÀ-ÿ\s]+$/;
            let contatoRegex = /^[A-Za-zÀ-ÿ\s]+$/;

            if (nome.length < 3) {
                alert("O nome deve conter pelo menos 3 caracteres.");
                event.preventDefault();
                return;
            }

            if (!nomeRegex.test(nome)) {
                alert("O nome não pode conter números ou caracteres especiais!");
                event.preventDefault();
                return;
            }

            if (contato.length < 3) {
                alert("O contato deve conter pelo menos 3 caracteres.");
                event.preventDefault();
                return;
            }

            if (!contatoRegex.test(contato)) {
                alert("O contato não pode conter números ou caracteres especiais!");
                event.preventDefault();
                return;
            }
            if (telefoneInput) {
            Inputmask({ mask: ["(99) 9999-9999", "(99) 99999-9999"], keepStatic: true }).mask(telefoneInput);
        }
        });
    </script>
    <address>Helena Lopes - Desenvolvimento de Sistemas - Senai</address>
</body>
</html>
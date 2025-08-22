<?php
session_start();
require_once 'conexao.php';

// VERIFICA SE O USUÁRIO TEM PERMISSÃO (ADM = 1)
if ($_SESSION['perfil'] != 1) {
    echo "<script>alert('Acesso Negado!');window.location.href='principal.php';</script>";
    exit();
}

if($_SERVER["REQUEST_METHOD"]=="POST"){
    $nome_fornecedor = $_POST['nome_fornecedor'];
    $endereco = $_POST['endereco'];
    $telefone = $_POST['telefone'];
    $email = $_POST['email'];
    $contato = $_POST['contato'];
    
    // Validação do lado do servidor
    $erros = [];
    
    // Validação do nome do fornecedor
    if (strlen($nome_fornecedor) < 3) {
        $erros[] = "O nome do fornecedor deve ter pelo menos 3 caracteres.";
    }
    if (strlen($nome_fornecedor) > 100) {
        $erros[] = "O nome do fornecedor deve ter no máximo 100 caracteres.";
    }
    
    // Verificar se email já existe
    $sql_check = "SELECT id_fornecedor FROM fornecedor WHERE email = :email";
    $stmt_check = $pdo->prepare($sql_check);
    $stmt_check->bindParam(':email', $email);
    $stmt_check->execute();
    if ($stmt_check->fetch()) {
        $erros[] = "Este e-mail já está cadastrado.";
    }
    
    // Validação do contato
    if (strlen($contato) < 3) {
        $erros[] = "O contato deve ter pelo menos 3 caracteres.";
    }
    if (strlen($contato) > 100) {
        $erros[] = "O contato deve ter no máximo 100 caracteres.";
    }
    
    // Se não há erros, prossegue com o cadastro
    if (empty($erros)) {
        $sql="INSERT INTO fornecedor(nome_fornecedor,endereco,telefone,email,contato) VALUES (:nome_fornecedor,:endereco,:telefone,:email,:contato)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':nome_fornecedor',$nome_fornecedor);
        $stmt->bindParam(':endereco',$endereco);
        $stmt->bindParam(':telefone',$telefone);
        $stmt->bindParam(':email',$email);
        $stmt->bindParam(':contato',$contato);

        if($stmt->execute()){
            echo "<script>alert('Fornecedor cadastrado com sucesso!');</script>";
        }else{
            echo "<script>alert('Erro ao cadastrar fornecedor');</script>";
        }
    } else {
        echo "<script>alert('" . implode("\\n", $erros) . "');</script>";
    }
}

// MENU (ADM)
$permissoes = [
    "cadastrar" => ["cadastro_usuario.php", "cadastro_perfil.php", "cadastro_cliente.php", "cadastro_fornecedor.php", "cadastro_produto.php", "cadastro_funcionario.php"],
    "buscar"    => ["buscar_usuario.php", "buscar_perfil.php", "buscar_cliente.php", "buscar_fornecedor.php", "buscar_produto.php", "buscar_funcionario.php"],
    "alterar"   => ["alterar_usuario.php", "alterar_perfil.php", "alterar_cliente.php", "alterar_fornecedor.php", "alterar_produto.php", "alterar_funcionario.php"],
    "excluir"   => ["excluir_usuario.php", "excluir_perfil.php", "excluir_cliente.php", "excluir_fornecedor.php", "excluir_produto.php", "excluir_funcionario.php"]
];
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Cadastrar Usuário</title>
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


    address {
        margin-top: 50px;
        font-style: italic;
        color: #555;
        text-align: center;
    }
    .telefone {
                width: 80%; /* Ocupa toda a largura do formulário */
                padding: 8px;
                margin-bottom: 10px;
                border: 1px solid #ccc;
                border-radius: 5px;
                font-size: 16px;
            }
</style>
</head>
<body>

    <!-- MENU AMARELO -->
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
    <h2 class="content">Cadastrar Fornecedor</h2>

    <form action="cadastro_fornecedor.php" method="POST" id="formCadastro">
        <label for="nome_fornecedor"> Nome do Fornecedor: </label>
        <input type="text" name="nome_fornecedor" id="nome_fornecedor" required>

        <label for="endereco"> Endereço: </label>
        <input type="text" name="endereco" id="endereco" required>

        <label for="telefone"> Telefone: </label>
        <input type="tel" name="telefone" minlength="8" minlength="11" id="telefone" class="telefone" required>

        <label for="email"> E-mail: </label>
        <input type="email" name="email" id="email" required>

        <label for="contato"> Contato: </label>
        <input type="text" name="contato" id="contato" required>

        <button type="submit"> Salvar </button>
        <button type="reset"> Cancelar </button>
    </form>
    
    <a class="voltar" href="principal.php"> Voltar </a>

    <script>
    
    <a href="principal.php">Voltar</a>
    <address>Helena Lopes - Desenvolvimento de Sistemas - Senai</address>
    <script>
        <a class="voltar" href="principal.php"> Voltar </a>

<script>
        const telefone = document.getElementById("telefone");
        telefone.addEventListener('input', function () {
            let telefone = this.value.replace(/\D/g, "");

            if (telefone.length > 10) {
                    telefone = telefone.replace(/^(\d{2})(\d{5})(\d{4}).*/, "($1) $2-$3");
                } else if (telefone.length > 5) {
                    telefone = telefone.replace(/^(\d{2})(\d{4})(\d{0,4}).*/, "($1) $2-$3");
                } else if (telefone.length > 2) {
                    telefone = telefone.replace(/^(\d{2})(\d{0,5}).*/, "($1) $2");
                } else {
                    telefone = telefone.replace(/^(\d*)/, "($1");
                }

            this.value = telefone;
        });
    </script>

    <script>
        document.getElementById("formCadastro").addEventListener("submit", function(event) {
            let nome = document.getElementById("nome_fornecedor").value.trim();
            let contato = document.getElementById("contato").value.trim();
            let telefone = document.getElementById("telefone").value.trim();

            // Regex: aceita apenas letras (maiúsculas e minúsculas) e espaços
            let nomeRegex = /^[A-Za-zÀ-ÿ\s]+$/;
            let contatoRegex = /^[A-Za-zÀ-ÿ\s]+$/;

            if (nome.length < 3) {
                alert("O nome deve conter pelo menos 3 caracteres.")
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

            if (telefone.length !== 15) {
                alert('Telefone inválido!');
                event.preventDefault();
                return;
            }
            if (telefoneInput) {
            Inputmask({ mask: ["(99) 9999-9999", "(99) 99999-9999"], keepStatic: true }).mask(telefoneInput);
        }
        });
</body>
</html>

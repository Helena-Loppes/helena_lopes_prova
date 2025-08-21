<?php
session_start();
require_once 'conexao.php';

// VERIFICA SE O USUÁRIO TEM PERMISSÃO (ADM = 1)
if ($_SESSION['perfil'] != 1) {
    echo "<script>alert('Acesso Negado!');window.location.href='principal.php';</script>";
    exit();
}

if($_SERVER["REQUEST_METHOD"]=="POST"){
    $nome_fornecedor = $_POST['nome'];
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
    
    // Validação do endereço
    if (strlen($endereco) < 5) {
        $erros[] = "O endereço deve ter pelo menos 5 caracteres.";
    }
    if (strlen($endereco) > 255) {
        $erros[] = "O endereço deve ter no máximo 255 caracteres.";
    }
    
    // Validação do telefone
    $telefone_numeros = preg_replace('/\D/', '', $telefone); // Remove caracteres não numéricos
    if (strlen($telefone_numeros) < 10) {
        $erros[] = "O telefone deve ter pelo menos 10 dígitos.";
    }
    if (strlen($telefone_numeros) > 11) {
        $erros[] = "O telefone deve ter no máximo 11 dígitos.";
    }
    
    // Validação do email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erros[] = "Digite um e-mail válido.";
    }
    if (strlen($email) > 100) {
        $erros[] = "O e-mail deve ter no máximo 100 caracteres.";
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

    <!-- CONTEÚDO ORIGINAL MANTIDO -->
    <h2>Cadastrar Fornecedor</h2>
    <form action="cadastro_fornecedor.php" method="POST">
        <label for="nome">Nome Fornecedor:</label>
        <input type="text" id="nome" name="nome" required>

        <label for="endereco">Endereço:</label>
        <input type="text" id="endereco" name="endereco" required>

        <label for="telefone">Telefone:</label>
        <input type="tel" id="telefone" minlength="8" maxlength="11" name="telefone"  required>

        <label for="email">E-mail:</label>
        <input type="email" id="email" name="email" required>

        <label for="contato">Contato:</label>
        <input type="text" id="contato" name="contato" 
        placeholder="Nome do fornecedor responsavel" required>

        <button type="submit">Salvar</button>
        <button type="reset">Cancelar</button>
    </form>
    
    <a href="principal.php">Voltar</a>
    <address>Helena Lopes - Desenvolvimento de Sistemas - Senai</address>
    <script>
        document.getElementById("formCadastro").addEventListener("submit", function(event) {
            let nome = document.getElementById("nome").value.trim();
            let senha = document.getElementById("senha").value;

            // Regex: aceita apenas letras (maiúsculas e minúsculas) e espaços
            let nomeRegex = /^[A-Za-zÀ-ÿ\s]+$/;

            if (!nomeRegex.test(nome)) {
                alert("O nome não pode conter números ou caracteres especiais!");
                event.preventDefault();
                return;
            }

            // Validação da senha: mínimo de 8 caracteres
            if (numero.length < 11) {
                alert("A o nunero deve ter no mínimo 8 caracteres!");
                event.preventDefault();
                return;
            }
        });
    </script>
</body>
</html>

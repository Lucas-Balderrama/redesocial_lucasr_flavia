<?php
include 'conexao.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nome = $_POST["nome"];
    $email = $_POST["email"];
    $senha = $_POST["senha"];

    $senha_hashed = password_hash($senha, PASSWORD_DEFAULT);

    $sql_insercao = "INSERT INTO usuarios (nome, email, senha) VALUES (?, ?, ?)";
    $stmt = $conexao->prepare($sql_insercao);
    $stmt->bind_param("sss", $nome, $email, $senha_hashed);


    if ($stmt->execute()) {
        header("Location: index.php");
    } else {
        echo "Erro ao cadastrar usuário: " . $conexao->error;
    }

    $stmt->close();
    $conexao->close();
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HostCenter - Cadastrar</title>
    <link rel="shortcut icon" href="img/hostcenter-icon.png" type="image/png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@100..900&family=Plus+Jakarta+Sans:wght@200..800&display=swap" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=Reem+Kufi:wght@400..700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&family=Plus+Jakarta+Sans:ital,wght@0,200..800;1,200..800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="./css/login.css">
</head>

<body class="body-cadastro">
    <section class="secao-cadastro">
        <a href="index.php" class="botao-voltar">
            <i class="fas fa-arrow-left"></i> Voltar
        </a>
        <div class="box-cadastro">
            <img class="logo_login" src="./img/nexa_logo_branca-removebg-preview.png" alt="Logo Nexa">
            <form action="" method="POST">
                <h1>Cadastre-se</h1>
                <label for="nome">Nome</label>
                <input class="inserir" type="text" name="nome">

                <label for="email">Email</label>
                <input class="inserir" type="email" name="email">

                <label for="senha">Senha</label>
                <input class="inserir" id="senha-campo" type="password" name="senha">
                <div id='mostrar2'>
                    <input type='checkbox' onclick='mostrarSenha()'> Mostrar senha
                </div>

                <button id="botao-cadastrar" type="submit">Cadastrar</button>

                <p class="celular2">Já possui uma conta? <a href="./index.php">Entrar</a></p>
            </form>
        </div>
    </section>
    <script>
        function mostrarSenha() {
            var x = document.getElementById("senha-campo");
            if (x.type === "password") {
                x.type = "text";
            } else {
                x.type = "password";
            }
        }
    </script>
</body>

</html>
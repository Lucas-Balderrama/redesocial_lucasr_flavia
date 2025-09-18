<?php
session_start();
include 'conexao.php';

$mensagem = "";
$tipoAlerta = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = $_POST["email"];
    $novaSenha = $_POST["senha"];
    $confirmarSenha = $_POST["confirmar_senha"];

    if ($novaSenha !== $confirmarSenha) {
        $mensagem = "As senhas não coincidem.";
        $tipoAlerta = "error";
    } else {
        $novaSenhaHash = password_hash($novaSenha, PASSWORD_DEFAULT);

        // Verifica se o email existe
        $stmt = $conexao->prepare("SELECT id_usuario FROM usuarios WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows === 1) {
            $stmt->close();
            $stmt = $conexao->prepare("UPDATE usuarios SET senha = ? WHERE email = ?");
            $stmt->bind_param("ss", $novaSenhaHash, $email);
            if ($stmt->execute()) {
                $mensagem = "Senha atualizada com sucesso!";
                $tipoAlerta = "success";
            } else {
                $mensagem = "Erro ao atualizar a senha.";
                $tipoAlerta = "error";
            }
        } else {
            $mensagem = "Email não encontrado!";
            $tipoAlerta = "error";
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Recuperar Senha</title>
    <link rel="stylesheet" href="./css/esqueceuSenha.css">
     <link rel="shortcut icon" href="img/hostcenter-icon.png" type="image/png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@100..900&family=Plus+Jakarta+Sans:wght@200..800&display=swap" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=Reem+Kufi:wght@400..700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&family=Plus+Jakarta+Sans:ital,wght@0,200..800;1,200..800&display=swap" rel="stylesheet">   
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
<section id="secao-login">
   <a href="index.php" class="botao-voltar">
            <i class="fas fa-arrow-left"></i> Voltar
        </a>
    <div id="box-login">

        <h1>Recuperar senha</h1>

        <form method="POST" action="">
            <label for="email">Email</label>
            <input class="inserir" type="email" name="email" required placeholder="Digite seu email">

            <label for="senha">Nova senha</label>
            <input class="inserir" type="password" name="senha" id="senha" required placeholder="Nova senha">

            <label for="confirmar_senha">Confirmar senha</label>
            <input class="inserir" type="password" name="confirmar_senha" id="confirmar_senha" required placeholder="Confirme a senha">

            <div id="mostrar">
                <input type="checkbox" onclick="mostrarSenha()">
                <label for="mostrar-senha">Mostrar senha</label>
            </div>

            <input id="entrar" type="submit" value="Atualizar senha">
        </form>
    </div>
</section>

<?php if (!empty($mensagem)) : ?>
<script>
Swal.fire({
    icon: '<?= $tipoAlerta ?>',
    title: '<?= $tipoAlerta === "success" ? "Sucesso!" : "Erro!" ?>',
    text: '<?= $mensagem ?>'
});
</script>
<?php endif; ?>

<script>
function mostrarSenha() {
    const campos = [document.getElementById('senha'), document.getElementById('confirmar_senha')];
    campos.forEach(campo => {
        campo.type = campo.type === 'password' ? 'text' : 'password';
    });
}
</script>
</body>
</html>

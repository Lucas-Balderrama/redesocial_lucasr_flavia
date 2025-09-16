<?php
session_start();
require 'conexao.php';

if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit();
}

$usuario_id = $_SESSION['id_usuario'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $novo_nome = trim($_POST['nome'] ?? '');
    $novo_email = trim($_POST['email'] ?? '');
    $novo_foto = $_FILES['foto_perfil'] ?? null;

    if (!empty($novo_nome) && !empty($novo_email)) {
        $sql_update = "UPDATE usuarios SET nome = ?, email = ? WHERE id_usuario = ?";
        $stmt = $conexao->prepare($sql_update);
        $stmt->bind_param("ssi", $novo_nome, $novo_email, $usuario_id);
        $stmt->execute();
        $stmt->close();

        if ($novo_foto && $novo_foto['error'] === UPLOAD_ERR_OK) {
            $permitidos = ['image/jpeg', 'image/png', 'image/gif'];
            if (in_array($novo_foto['type'], $permitidos)) {
                $extensao = pathinfo($novo_foto['name'], PATHINFO_EXTENSION);
                $nome_arquivo = uniqid('foto_', true) . '.' . $extensao;
                $caminho = 'img/' . $nome_arquivo;

                if (!is_dir('img')) {
                    mkdir('img', 0755, true);
                }

                move_uploaded_file($novo_foto['tmp_name'], $caminho);

                $sql_foto = "UPDATE usuarios SET foto_perfil = ? WHERE id_usuario = ?";
                $stmt_foto = $conexao->prepare($sql_foto);
                $stmt_foto->bind_param("si", $caminho, $usuario_id);
                $stmt_foto->execute();
                $stmt_foto->close();
            }
        }

        header("Location: perfil.php");
        exit();
    }
}

$sql = "SELECT nome, email, foto_perfil FROM usuarios WHERE id_usuario = ?";
$stmt = $conexao->prepare($sql);
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$result = $stmt->get_result();
$usuario = $result->fetch_assoc();
$stmt->close();

$sql_amigos = "
SELECT u.id_usuario, u.nome, u.foto_perfil 
FROM amigos a
JOIN usuarios u ON u.id_usuario = a.amigo_id
WHERE a.usuario_id = ? AND a.status = 'aceito'

UNION

SELECT u.id_usuario, u.nome, u.foto_perfil 
FROM amigos a
JOIN usuarios u ON u.id_usuario = a.usuario_id
WHERE a.amigo_id = ? AND a.status = 'aceito'
";
$stmt_amigos = $conexao->prepare($sql_amigos);
$stmt_amigos->bind_param("ii", $usuario_id, $usuario_id);
$stmt_amigos->execute();
$amigos_result = $stmt_amigos->get_result();
$stmt_amigos->close();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Perfil de <?php echo htmlspecialchars($usuario['nome']); ?></title>
    <link rel="stylesheet" href="./css/perfil.css">
</head>
<body>
    <header id="header">
        <div id="container">
            <a href="index.php" id="box-img"><img class="logo" src="./img/nexa_logo.png" alt="logo"></li></a>
            <nav>
                <ul id="nav1">
                    <li>
                        <h3><a id="inicio" href="./index.php">Feed</a></h3>
                        <h3><a id="perfil" href="./perfil.php">Perfil</a></h3>
                    </li>
                </ul>
                <div id="user-div">
                    <?php
               

                    if (isset($_SESSION['nome']) && $_SESSION['nome'] != '' ) {
                        echo "
                    <select id='user' onchange='redirecionar(this.value)'>
                        <option value='' id='opt-nome'>" . $_SESSION['nome'] . "</option>
                        <option value='logout.php'>Sair</option>
                    </select>";
                    } else {
                        echo "<h3><a id='login' href='./login.php'>Entrar</a></h3>";
                    }
                    ?>

                    <script>
                        function redirecionar(url) {
                            if (url) {
                                window.location.href = url;
                            }
                        }
                    </script>
                </div>
            </nav>
        </div>
    </header>
    <h2>Perfil de <?php echo htmlspecialchars($usuario['nome']); ?></h2>

    <?php if (!empty($usuario['foto_perfil'])): ?>
        <img src="<?php echo htmlspecialchars($usuario['foto_perfil']); ?>" alt="Foto de perfil" class="foto">
    <?php else: ?>
        <img src="padrao.png" alt="Sem foto" class="foto">
    <?php endif; ?>

    <h3>Editar Perfil</h3>
    <form method="POST" enctype="multipart/form-data">
        <label>Nome:</label><br>
        <input type="text" name="nome" value="<?php echo htmlspecialchars($usuario['nome']); ?>" required><br><br>

        <label>Email:</label><br>
        <input type="email" name="email" value="<?php echo htmlspecialchars($usuario['email']); ?>" required><br><br>

        <label>Foto de perfil:</label><br>
        <input type="file" name="foto_perfil" accept="image/*"><br><br>

        <button type="submit">Salvar alterações</button>
    </form>

    <hr>

    <div class="amigos">
        <h3>Amigos</h3>
        <?php if ($amigos_result->num_rows > 0): ?>
            <?php while ($amigo = $amigos_result->fetch_assoc()): ?>
                <div class="amigo">
                    <?php if (!empty($amigo['foto_perfil'])): ?>
                        <img src="<?php echo htmlspecialchars($amigo['foto_perfil']); ?>" alt="Foto do amigo">
                    <?php else: ?>
                        <img src="padrao.png" alt="Sem foto">
                    <?php endif; ?>
                    <span><?php echo htmlspecialchars($amigo['nome']); ?></span>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>Você ainda não tem amigos adicionados.</p>
        <?php endif; ?>
    </div>
</body>
</html>

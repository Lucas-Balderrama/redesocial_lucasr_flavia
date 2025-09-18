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

                if (!is_dir('img')) mkdir('img', 0755, true);

                move_uploaded_file($novo_foto['tmp_name'], $caminho);

                $sql_foto = "UPDATE usuarios SET foto_perfil = ? WHERE id_usuario = ?";
                $stmt_foto = $conexao->prepare($sql_foto);
                $stmt_foto->bind_param("si", $caminho, $usuario_id);
                $stmt_foto->execute();
                $stmt_foto->close();


                $_SESSION['foto_perfil'] = $caminho;
            }
        }

        $_SESSION['nome'] = $novo_nome;

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
    <link rel="stylesheet" href="./css/nav.css">
    <link rel="stylesheet" href="./css/feed.css">
</head>
<body>

<header id="header">
    <div id="container">
        <a href="feed.php" id="box-img">
            <img class="logo" src="./img/nexa_logo.png" alt="logo">
        </a>
        <nav>
            <ul id="nav1">
                <li><h3><a id="inicio" href="./feed.php">Feed</a></h3></li>
                <li><h3><a id="perfil" href="./perfil.php">Perfil</a></h3></li>
                <li><h3><a id="chat" href="./chat.php">Conversas</a></h3></li>
            </ul>
            <div id="user-div">
                <?php if (!empty($_SESSION['nome'])): 
                    $fotoPerfil = !empty($_SESSION['foto_perfil']) ? $_SESSION['foto_perfil'] : './img/user_default.jpg'; ?>
                    <div class="user-menu">
                        <button id="user-btn">
                            <img class="user-foto" src="<?php echo htmlspecialchars($fotoPerfil); ?>" alt="Foto de Perfil">
                        </button>
                        
                        <div id="user-modal" class="modal">
                            <div class="modal-content">
                                <span id="close-modal">&times;</span>
                                <div class="user-info">
                                    <img class="user-foto-modal" src="<?php echo htmlspecialchars($fotoPerfil); ?>" alt="Foto de Perfil">
                                    <div class="info">
                                        <h3><?php echo htmlspecialchars($_SESSION['nome']); ?></h3>
                                        <a href="./perfil.php">Acessar Perfil</a>
                                    </div>
                                </div>
                                <div class="logout">
                                    <a href="logout.php">Sair</a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <h3><a id="login" href="./index.php">Entrar</a></h3>
                <?php endif; ?>
            </div>
        </nav>
    </div>
</header>

<h2 class="h2-perfil">Perfil de <?php echo htmlspecialchars($usuario['nome']); ?></h2>

<div class="profile-container">
    <div class="sidebar">
        <img src="<?php echo !empty($usuario['foto_perfil']) ? htmlspecialchars($usuario['foto_perfil']) : 'padrao.png'; ?>" alt="Foto de perfil">
        <h2><?php echo htmlspecialchars($usuario['nome']); ?></h2>
        <p><?php echo htmlspecialchars($usuario['email']); ?></p>
    </div>

    <div class="main-content">
        <form action="perfil.php" method="POST" enctype="multipart/form-data">
            <label>Nome:</label>
            <input type="text" name="nome" value="<?php echo htmlspecialchars($usuario['nome']); ?>" required>

            <label>Email:</label>
            <input type="email" name="email" value="<?php echo htmlspecialchars($usuario['email']); ?>" required>

            <label>Foto de perfil:</label>
            <input type="file" name="foto_perfil" accept="image/*">

            <button type="submit">Salvar alterações</button>
        </form>

        <div class="amigos">
            <h3>Amigos</h3>
            <?php if ($amigos_result->num_rows > 0): ?>
                <?php while ($amigo = $amigos_result->fetch_assoc()): ?>
                    <div class="amigo">
                        <img src="<?php echo !empty($amigo['foto_perfil']) ? htmlspecialchars($amigo['foto_perfil']) : 'padrao.png'; ?>" alt="Foto do amigo">
                        <span><?php echo htmlspecialchars($amigo['nome']); ?></span>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>Você ainda não tem amigos adicionados.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

</body>
</html>

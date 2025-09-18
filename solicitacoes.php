<?php
session_start();
require 'conexao.php';

if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit;
}

$usuario_id = $_SESSION['id_usuario'];

if (isset($_GET['aceitar'])) {
    $id_pedido = intval($_GET['aceitar']);
    $sql = "UPDATE amigos SET status = 'aceito' WHERE id_amigo = ? AND amigo_id = ?";
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("ii", $id_pedido, $usuario_id);
    $stmt->execute();
    $stmt->close();
}

if (isset($_GET['recusar'])) {
    $id_pedido = intval($_GET['recusar']);
    $sql = "DELETE FROM amigos WHERE id_amigo = ? AND amigo_id = ?";
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("ii", $id_pedido, $usuario_id);
    $stmt->execute();
    $stmt->close();
}

$sql = "SELECT a.id_amigo, u.nome, u.email, u.foto_perfil 
        FROM amigos a
        JOIN usuarios u ON u.id_usuario = a.usuario_id
        WHERE a.amigo_id = ? AND a.status = 'pendente'";
$stmt = $conexao->prepare($sql);
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Solicitações de Amizade</title>
    <link rel="stylesheet" href="./css/nav.css">
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

    <h2>Solicitações Recebidas</h2>

    <?php if ($result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
            <div style="margin-bottom:10px;">
                <img src="<?php echo !empty($row['foto_perfil']) ? $row['foto_perfil'] : './img/user_default.jpg'; ?>" width="40" height="40" style="border-radius:50%;">
                <strong><?php echo htmlspecialchars($row['nome']); ?></strong> (<?php echo htmlspecialchars($row['email']); ?>)
                <a href="?aceitar=<?php echo $row['id_amigo']; ?>">[Aceitar]</a>
                <a href="?recusar=<?php echo $row['id_amigo']; ?>">[Recusar]</a>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p>Não há solicitações pendentes.</p>
    <?php endif; ?>
</body>
</html>

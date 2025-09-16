<?php
session_start();
require 'conexao.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit();
}

$usuario_id = $_SESSION['usuario_id'];

$sql = "SELECT nome, email, foto_perfil FROM usuarios WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$result = $stmt->get_result();
$usuario = $result->fetch_assoc();

$sql2 = "
SELECT u.id, u.nome, u.foto_perfil 
FROM amigos a 
JOIN usuarios u ON (u.id = a.amigo_id) 
WHERE a.usuario_id = ? AND a.status = 'aceito'
";
$stmt2 = $conn->prepare($sql2);
$stmt2->bind_param("i", $usuario_id);
$stmt2->execute();
$amigos = $stmt2->get_result();
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
     <title>Perfil de <?php echo htmlspecialchars($usuario['nome']); ?></title>
    <link rel="stylesheet" href="perfil.css">
</head>

<body>

    <div class="perfil">
        <h2>Perfil de <?php echo htmlspecialchars($usuario['nome']); ?></h2>
        <p><strong>Email:</strong> <?php echo htmlspecialchars($usuario['email']); ?></p>
        <?php if ($usuario['foto_perfil']): ?>
            <img src="<?php echo htmlspecialchars($usuario['foto_perfil']); ?>" alt="Foto de perfil" class="foto">
        <?php else: ?>
            <p>Sem foto de perfil</p>
        <?php endif; ?>

        <div class="amigos">
            <h3>Amigos</h3>
            <?php if ($amigos->num_rows > 0): ?>
                <?php while ($amigo = $amigos->fetch_assoc()): ?>
                    <div class="amigo">
                        <?php if ($amigo['foto_perfil']): ?>
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
    </div>

</body>

</html>